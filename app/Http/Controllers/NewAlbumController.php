<?php

namespace App\Http\Controllers;

use App\Album;
use App\Http\Requests\NewAlbumRequest;
use App\Performer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use wapmorgan\Mp3Info\Mp3Info;
class NewAlbumController extends Controller
{

    public function index() {
        $count = 1;
        if(session('count') >= 1) {
            $count = session('tracksCount');
            session(['tracksCount' => 1]);
        }
        if($count < 1) {
            $count = 1;
        }
        return view('newalbum', compact('count'));
    }

    public function post(NewAlbumRequest $request) {
        session(['tracksCount' => 1]);
        session(['count' => 1]);

        $requestKeys = array_keys($request->all());
        $tracksKeys = [];
        foreach ($requestKeys as $requestKey) {
            if(preg_match('/track(\d+)/', $requestKey, $matches)) {
                $tracksKeys = array_merge($tracksKeys, [$matches[1]]);
            }
        }
        if(count($tracksKeys) > 0) {
            $albumYear = Album::albumYear($request->all()['album_year']);
            $albumName = Album::albumName($request->all()['album_name'], $albumYear);

            DB::table('albums')->insert([
               'album_id' => null,
                'user_id' => Auth::id(),
                'album_name' => $albumName,
                'album_year' => $albumYear
            ]);
            $albumId = DB::table('albums')->where('user_id', Auth::id())->
                where('album_name', $albumName)->where('album_year', $albumYear)->pluck('album_id')[0];

            foreach ($tracksKeys as $tracksKey) {
                $trackPerformerKey = 'track_performer' . $tracksKey;
                $trackPerformer = $request->all()[$trackPerformerKey];
                $performerId = Performer::performerId($trackPerformer);

                $trackKey = 'track' . $tracksKey;
                if($request->hasFile($trackKey)) {
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

                    $fileName = $request->all()['track_name' . $tracksKey];
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
            }
        }
        session(['tracksCount' => 1]);
        return redirect()->back();
    }
}
