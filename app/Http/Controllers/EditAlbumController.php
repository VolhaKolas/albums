<?php

namespace App\Http\Controllers;

use App\Album;
use App\Classes\PerformerId;
use App\Http\Requests\EditAlbumRequest;
use App\Performer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use wapmorgan\Mp3Info\Mp3Info;
class EditAlbumController extends Controller
{
    /*
     * method shows form for edit album
     */
    public function index(Request $request) {
        $key = -1;
        $id = $request->all()['number']; //album id
        $album = Album::where('album_id', $id)->select('album_name', 'album_id', 'album_year')->get(); //current album
        if(count($album) > 0) {
            $album = $album[0];
            $tracks = DB::table('m2m_performer_tracks AS pt') //tracks in current album
                ->join('performers AS p', 'pt.performer_id', '=', 'p.performer_id')
                ->join('tracks AS t', 'pt.track_id', '=', 't.track_id')
                ->select('t.track_id AS track_id', 't.track_name AS track_name',
                    't.track_duration AS track_duration', 'p.performer_name AS track_performer')
                ->where('t.album_id', $id)
                ->get();

            return view('editalbum', compact('album', 'tracks', 'key'));
        }
        else {
            return redirect()->back();
        }
    }

    /*
     * method edits album
     */
    public function post(EditAlbumRequest $request) {
        $albumId = $request->all()['album_id'];
        $album = new \App\Classes\Album($request->all()['album_name'], $request->all()['album_year'], $albumId);
        $albumName = $album->getAlbumName();
        $albumYear = $album->getAlbumYear();
        if($request->hasFile('track0')) { //if tracks0 (new track for upload) issets
            $trackKey = 'track0';
            $tracksKey= DB::table('tracks')->where('album_id', $request->all()['album_id'])->max('track_id') + 1;
            $performer = new PerformerId($request->all()['track_performer0']);
            $performerId = $performer->getPerformerId(); //put performer name if needed into DB and get performer id

            $file = $request->file($trackKey);
            $filePathName = $request->file($trackKey)->getFilename();
            $filePath = public_path() . '/tracks/' . Auth::id() . "/" . $albumId . '/' .$tracksKey;
            $file->move($filePath); //put file on server


            $audio = new Mp3Info($filePath . '/' .$filePathName, true); //calculate track duration
            $trackDuration = round($audio->duration, 0);
            $minutes = floor($trackDuration / 60);
            if($minutes < 10) {
                $minutes = "0" . $minutes;
            }
            $seconds = $trackDuration % 60;
            if($seconds < 10) {
                $seconds = '0' . $seconds;
            }
            $trackDuration = $minutes . ":" . $seconds;

            $fileName = $request->all()['track_name0']; //create new or take existing file name
            if(null == $fileName){
                $fileName = $request->file($trackKey)->getClientOriginalName();
            }
            else {
                if(!isset(pathinfo($fileName)['extension'])) {
                    $fileName = $fileName . "." .
                        pathinfo($request->file($trackKey)->getClientOriginalName())['extension'];
                }
            }

            DB::table('tracks')->insert([ //put data into tracks table
                'track_id' => null,
                'track_name' => $fileName,
                'track_path' => $tracksKey . "/" .$filePathName,
                'album_id' => $albumId,
                'track_duration' => $trackDuration,
            ]);

            $trackId = DB::table('tracks')-> //get track id
            where('track_name', $fileName)
                ->where('track_path',$tracksKey . "/" .$filePathName)
                ->where('album_id', $albumId)
                ->where('track_duration', $trackDuration)
                ->pluck('track_id')[0];

            DB::table('m2m_performer_tracks')->insert([ //put data into m2m_performer_tracks table
                'm2m_performer_track_id' => null,
                'track_id' => $trackId,
                'performer_id' => $performerId
            ]);
        }

        $requestKeys = array_keys($request->all());
        foreach ($requestKeys as $requestKey) {
            if(preg_match('/checkbox(\d+)/', $requestKey, $matches)) {//here I get checkboxes for tracks to delete
                $deleteTrackId = $matches[1];
                $trackPath = DB::table('tracks') ->where('track_id', $deleteTrackId)->pluck('track_path')[0];
                DB::table('tracks')->where('track_id', $deleteTrackId)->delete();
                unlink(public_path() . '/tracks/' . Auth::id() . "/" . $albumId . '/' . $trackPath);//delete track from server
            }
            else if(preg_match('/track_name(\d+)/', $requestKey, $matches)) {//here I edit tracks name
                DB::table('tracks')->where('track_id', $matches[1])->
                    update([
                       "track_name" => $request->all()[$requestKey]
                ]);
            }
            else if(preg_match('/track_performer(\d+)/', $requestKey, $matches)) {//here I edit tracks performer
                $performerCorrect = new PerformerId($request->all()[$requestKey]);
                $idPerformer = $performerCorrect->getPerformerId();
                DB::table('m2m_performer_tracks')->where('track_id', $matches[1])->
                update([
                    "performer_id" => $idPerformer
                ]);
            }
        }

        DB::table('albums')->where('album_id', $albumId)->//here I edit album name and album year
            update([
                'album_name' => $albumName,
                'album_year' => $albumYear
        ]);
        return redirect()->back();
    }
}
