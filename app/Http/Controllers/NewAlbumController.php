<?php

namespace App\Http\Controllers;

use App\Album;
use App\Classes\PerformerId;
use App\Classes\SecondsToMinutes;
use App\Http\Requests\NewAlbumRequest;
use App\M2mPerformerTrack;
use App\Performer;
use App\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use wapmorgan\Mp3Info\Mp3Info;
class NewAlbumController extends Controller
{
    /*
     * method shows form for creation new album
     */
    public function showNewAlbumForm() {
        $count = 1; //look at TracksCountController
        if(session('count') >= 1) {
            $count = session('tracksCount');
            session(['tracksCount' => 1]);
        }
        if($count < 1) {
            $count = 1;
        }
        return view('newalbum', compact('count'));
    }

    /*
     * method creates new album
     */
    public function createNewAlbum(NewAlbumRequest $request) {
        session(['tracksCount' => 1]); //look at TracksCountController
        session(['count' => 1]);

        $requestKeys = array_keys($request->all());
        $tracksKeys = []; //this array consists of tracks numbers which user uploaded
        foreach ($requestKeys as $requestKey) {
            if(preg_match('/track(\d+)/', $requestKey, $matches)) {
                $tracksKeys = array_merge($tracksKeys, [$matches[1]]);
            }
        }
        if(count($tracksKeys) > 0) { //if user upload 1 and more tracks
            $album = new \App\Classes\Album($request->all()['album_name'], $request->all()['album_year']);
            $albumYear = $album->getAlbumYear();
            $albumName = $album->getAlbumName();

            //add data to albums table
            Album::insert([
               'album_id' => null,
                'user_id' => Auth::id(),
                'album_name' => $albumName,
                'album_year' => $albumYear
            ]);
            //get albumId
            $albumId = Album::where('user_id', Auth::id())->
                where('album_name', $albumName)->where('album_year', $albumYear)->pluck('album_id')[0];

            foreach ($tracksKeys as $tracksKey) { //put tracks into DB
                $trackPerformerKey = 'track_performer' . $tracksKey;
                $performer = new PerformerId($request->all()[$trackPerformerKey]);
                $performerId = $performer->getPerformerId(); //put performer name if needed into DB and get performer id

                $trackKey = 'track' . $tracksKey;
                if($request->hasFile($trackKey)) { //put file with track on server and file data into DB
                    $file = $request->file($trackKey);
                    $filePathName = $request->file($trackKey)->getFilename();
                    $filePath = public_path() . '/tracks/' . Auth::id() . "/" . $albumId . '/' .$tracksKey;
                    $file->move($filePath); //put file on server


                    $audio = new Mp3Info($filePath . '/' .$filePathName, true); //calculate track duration
                    $trackDurationConvert = new SecondsToMinutes($audio->duration);
                    $trackDuration = $trackDurationConvert->getConversion();


                    $fileName = $request->all()['track_name' . $tracksKey]; //create new or take existing file name
                    if(null == $fileName){
                        $fileName = $request->file($trackKey)->getClientOriginalName();
                    }
                    else {
                        if(!isset(pathinfo($fileName)['extension'])) {
                            $fileName = $fileName . "." .
                                pathinfo($request->file($trackKey)->getClientOriginalName())['extension'];
                        }
                    }

                    Track::insert([ //put data into tracks table
                        'track_id' => null,
                        'track_name' => $fileName,
                        'track_path' => $tracksKey . "/" .$filePathName,
                        'album_id' => $albumId,
                        'track_duration' => $trackDuration,
                        'performer_id' => $performerId
                    ]);
                }
            }
        }
        session(['tracksCount' => 1]);
        return redirect()->back();
    }
}
