<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewAlbumRequest;
use getID3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use wapmorgan\Mp3Info\Mp3Info;
class NewAlbumController extends Controller
{

    public function index() {
        $count = 1;
        if(session('count') >= 1) {
            $count = session('tracksCount');
            session(['tracksCount' => 1]);
        }
        return view('newalbum', compact('count'));
    }

    public function post(NewAlbumRequest $request) {
        session(['tracksCount' => 1]);
        session(['count' => 1]);
        $requestKeys = array_keys($request->all());
        $albumName = $request->all()['album_name'];
        $albumYear = $request->all()['album_year'];
        if(null == $albumYear or !is_int($albumYear) or $albumYear > date('Y') or $albumYear < 1) {
            $albumYear = date('Y');
        }
        $tracksKeys = [];
        foreach ($requestKeys as $requestKey) {
            if(preg_match('/track(\d+)/', $requestKey, $matches)) {
                $tracksKeys = array_merge($tracksKeys, [$matches[1]]);
            }
        }
        if(count($tracksKeys) > 0) {
            $albumNameExistenceCount = DB::table('albums')->where('user_id', Auth::id())->
            where('album_name', 'like', "$albumName%")->where('album_year', $albumYear)->count();
            if($albumNameExistenceCount > 0) {
                $albumName = $albumName . ($albumNameExistenceCount + 1);
            }
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
                if(null == $trackPerformer) {
                    $trackPerformer = 'unknown';
                }

                $performerExistenceCount = DB::table('performers')->
                    where('performer_name', $trackPerformer)->pluck('performer_id');
                if(count($performerExistenceCount) > 0) {
                    $performerId = $performerExistenceCount[0];
                }
                else {
                    DB::table('performers')->insert([
                       'performer_id' => null,
                        'performer_name' => $trackPerformer
                    ]);
                    $performerId = DB::table('performers')->
                    where('performer_name', $trackPerformer)->pluck('performer_id')[0];
                }

                $trackKey = 'track' . $tracksKey;
                if($request->hasFile($trackKey)) {
                    $file = $request->file($trackKey);
                    $filePathName = $request->file($trackKey)->getFilename();
                    $filePath = public_path() . '/tracks/' . Auth::id() . "/" . $albumId;
                    $file->move($filePath);


                    $audio = new Mp3Info($filePath . '/' .$filePathName, true);
                    $trackDuration = round($audio->duration, 0);

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
                        'track_path' => $filePathName,
                        'album_id' => $albumId,
                        'track_duration' => $trackDuration,
                    ]);

                    $trackId = DB::table('tracks')->
                        where('track_name', $fileName)
                        ->where('track_path', $filePathName)
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
