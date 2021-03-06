<?php

namespace App\Http\Controllers;

use App\Album;
use App\Classes\PerformerId;
use App\Classes\SecondsToMinutes;
use App\Http\Requests\EditAlbumRequest;
use App\M2mPerformerTrack;
use App\Performer;
use App\Track;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use wapmorgan\Mp3Info\Mp3Info;
class EditAlbumController extends Controller
{
    /*
     * method shows form for edit album
     */
    public function showEditForm($id) {//album id
        $key = -1; //used for creation new track, value '-1' - if the current album doesn't have any tracks
        $album = Album::where('album_id', $id)->select('album_name', 'album_id', 'album_year')->get(); //current album
        if(count($album) > 0) {
            $album = $album->first();
            $tracks = DB::table('performers AS p') //tracks in current album
                ->join('tracks AS t', 't.performer_id', '=', 'p.performer_id')
                ->select('t.track_id AS track_id', 't.track_name AS track_name',
                    't.track_duration AS track_duration', 'p.performer_name AS track_performer')
                ->where('t.album_id', $id)
                ->paginate(10);

            return view('editalbum', compact('album', 'tracks', 'key'));
        }
        else {
            return redirect()->back();
        }
    }

    /*
     * method edits album
     */
    public function editAlbum(EditAlbumRequest $request) {
        $albumId = $request->get('album_id');
        $album = new \App\Classes\Album($request->get('album_name'), $request->get('album_year'), $albumId);
        $albumName = $album->getAlbumName();
        $albumYear = $album->getAlbumYear();
        if($request->hasFile('track0')) { //if tracks0 (new track for upload) issets
            $trackKey = 'track0';
            $tracksKey= Track::where('album_id', $request->get('album_id'))->max('track_id') + 1;
            $performer = new PerformerId($request->get('track_performer0'));
            $performerId = $performer->getPerformerId(); //put the performer name if it's needed into DB and get the performer id

            $file = $request->file($trackKey);
            $filePathName = $request->file($trackKey)->getFilename();
            $filePath = public_path() . '/tracks/' . Auth::id() . "/" . $albumId . '/' .$tracksKey;
            $file->move($filePath); //put file on server

            if(file_exists($filePath . '/' . $filePathName)) {
                try {
                    $audio = new Mp3Info($filePath . '/' . $filePathName, true); //calculate track duration
                    $trackDurationConvert = new SecondsToMinutes($audio->duration);
                    $trackDuration = $trackDurationConvert->getConversion();
                }
                catch (Exception $e) {
                    $trackDuration = "00:00";
                }
                $fileName = $request->get('track_name0'); //create new or take existing file name
                if (null == $fileName) {
                    $fileName = $request->file($trackKey)->getClientOriginalName();
                } else {
                    if (!isset(pathinfo($fileName)['extension'])) {
                        $fileName = $fileName . "." .
                            pathinfo($request->file($trackKey)->getClientOriginalName())['extension'];
                    }
                }
                Track::insert([ //put data into tracks table
                    'track_id' => null,
                    'track_name' => $fileName,
                    'track_path' => $tracksKey . "/" . $filePathName,
                    'album_id' => $albumId,
                    'track_duration' => $trackDuration,
                    'performer_id' => $performerId
                ]);
            }
        }

        $requestKeys = array_keys($request->all());
        foreach ($requestKeys as $requestKey) {
            if(preg_match('/checkbox(\d+)/', $requestKey, $matches)) {//here I get checkboxes for tracks to delete
                $deleteTrackId = $matches[1];
                $trackPath = Track::where('track_id', $deleteTrackId)->select('track_path')->first();
                Track::where('track_id', $deleteTrackId)->delete();
                if(file_exists(public_path() . '/tracks/' . Auth::id() . "/" . $albumId . '/' . $trackPath)) {
                    unlink(public_path() . '/tracks/' . Auth::id() . "/" . $albumId . '/' . $trackPath);//delete track from server
                }
            }
            else if(preg_match('/track_name(\d+)/', $requestKey, $matches)) {//here I edit tracks name
                Track::where('track_id', $matches[1])->
                    update([
                       "track_name" => $request->all()[$requestKey]
                ]);
            }
            else if(preg_match('/track_performer(\d+)/', $requestKey, $matches)) {//here I edit tracks performer
                $performerCorrect = new PerformerId($request->get($requestKey));
                $idPerformer = $performerCorrect->getPerformerId();
                Track::where('track_id', $matches[1])->
                update([
                    "performer_id" => $idPerformer
                ]);
            }
        }

        Album::where('album_id', $albumId)->//here I edit the album name and the album year
            update([
                'album_name' => $albumName,
                'album_year' => $albumYear
        ]);
        return redirect()->back();
    }

    /*
     * method deletes album
     */
    public function delete(Request $request) {
        $albumId = $request->get('id');
        Album::where('album_id', $albumId)->delete();

        $albumPath = public_path() . '/tracks/' . Auth::id() . "/" . $albumId;
        if (is_dir($albumPath)) { //delete input folders and files
            $folders = scandir($albumPath);
            if(count($folders) >= 2) {
                foreach($folders as $key1 => $folder){
                    if($key1 >= 2 and is_dir($albumPath . "/" . $folder)) {
                        $files = scandir($albumPath . "/" . $folder);
                        if(count($files >= 2)) {
                            foreach ($files as $key2 => $file) {
                                if ($key2 >= 2 and file_exists($albumPath . "/" . $folder . "/" . $file)) {
                                    unlink($albumPath . "/" . $folder . "/" . $file);
                                }
                            }
                        }
                        rmdir($albumPath . "/" . $folder);
                    }
                }
            }
            rmdir($albumPath); //delete album folder
        }
        return redirect()->route('myAlbums');
    }
}
