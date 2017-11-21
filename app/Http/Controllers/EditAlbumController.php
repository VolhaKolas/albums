<?php

namespace App\Http\Controllers;

use App\Album;
use App\Http\Requests\EditAlbumRequest;
use App\Performer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use wapmorgan\Mp3Info\Mp3Info;
class EditAlbumController extends Controller
{
    public function index(Request $request) {

        $id = $request->all()['number'];
        $album = Album::where('album_id', $id)->select('album_name', 'album_id', 'album_year')->get();
        if(count($album) > 0) {
            $album = $album[0];
            $tracks = DB::table('m2m_performer_tracks AS pt')
                ->join('performers AS p', 'pt.performer_id', '=', 'p.performer_id')
                ->join('tracks AS t', 'pt.track_id', '=', 't.track_id')
                ->select('t.track_id AS track_id', 't.track_name AS track_name',
                    't.track_duration AS track_duration', 'p.performer_name AS track_performer')
                ->where('t.album_id', $id)
                ->get();

            return view('editalbum', compact('album', 'tracks'));
        }
        else {
            return redirect()->back();
        }
    }

    public function post(EditAlbumRequest $request) {
        $albumId = $request->all()['album_id'];
        $albumName = $request->all()['album_name'];
        $albumYear = $request->all()['album_year'];
        if($request->hasFile('track0')) {
            $trackKey = 'track0';
            $tracksKey= DB::table('tracks')->where('album_id', $request->all()['album_id'])->max('track_id') + 1;
            $performerId = Performer::performerId($request->all()['track_performer0']);

            $file = $request->file($trackKey);
            $filePathName = $request->file($trackKey)->getFilename();
            $filePath = public_path() . '/tracks/' . Auth::id() . "/" . $albumId . '/' .$tracksKey;
            $file->move($filePath);


            $audio = new Mp3Info($filePath . '/' .$filePathName, true);
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

            $fileName = $request->all()['track_name0'];
            if(null == $fileName){
                $fileName = $request->file($trackKey)->getClientOriginalName();
            }
            else {
                if(!isset(pathinfo($fileName)['extension'])) {
                    $fileName = $fileName . "." .
                        pathinfo($request->file($trackKey)->getClientOriginalName())['extension'];
                }
            }

            DB::table('tracks')->insert([
                'track_id' => null,
                'track_name' => $fileName,
                'track_path' => $tracksKey . "/" .$filePathName,
                'album_id' => $albumId,
                'track_duration' => $trackDuration,
            ]);

            $trackId = DB::table('tracks')->
            where('track_name', $fileName)
                ->where('track_path',$tracksKey . "/" .$filePathName)
                ->where('album_id', $albumId)
                ->where('track_duration', $trackDuration)
                ->pluck('track_id')[0];

            DB::table('m2m_performer_tracks')->insert([
                'm2m_performer_track_id' => null,
                'track_id' => $trackId,
                'performer_id' => $performerId
            ]);
        }

        $requestKeys = array_keys($request->all());
        foreach ($requestKeys as $requestKey) {
            if(preg_match('/checkbox(\d+)/', $requestKey, $matches)) {
                $deleteTrackId = $matches[1];
                DB::table('tracks')->where('track_id', $deleteTrackId)->delete();
            }
            else if(preg_match('/track_name(\d+)/', $requestKey, $matches)) {
                DB::table('tracks')->where('track_id', $matches[1])->
                    update([
                       "track_name" => $request->all()[$requestKey]
                ]);
            }
            else if(preg_match('/track_performer(\d+)/', $requestKey, $matches)) {
                $idPerformer = Performer::performerId($request->all()[$requestKey]);
                DB::table('m2m_performer_tracks')->where('track_id', $matches[1])->
                update([
                    "performer_id" => $idPerformer
                ]);
            }
        }

        DB::table('albums')->where('album_id', $albumId)->
            update([
                'album_name' => $albumName,
                'album_year' => $albumYear
        ]);
        return redirect()->back();
    }
}
