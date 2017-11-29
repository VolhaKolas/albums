<?php

namespace App\Http\Controllers;

use App\Album;
use App\Classes\PerformerId;
use App\Classes\SecondsToMinutes;
use App\Http\Requests\NewAlbumRequest;
use App\M2mPerformerTrack;
use App\Performer;
use App\Track;
use Exception;
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
        $tracksKeys = []; //this array consists of track's numbers which user uploaded
        foreach ($requestKeys as $requestKey) {
            if(preg_match('/track(\d+)/', $requestKey, $matches)) {
                $tracksKeys = array_merge($tracksKeys, [$matches[1]]);
            }
        }
        if(count($tracksKeys) > 0) { //if user upload 1 or more tracks
            $album = new \App\Classes\Album($request->get('album_name'), $request->get('album_year'));
            $albumYear = $album->getAlbumYear();
            $albumName = $album->getAlbumName();

            //add data to the albums table
            Album::insert([
               'album_id' => null,
                'user_id' => Auth::id(),
                'album_name' => $albumName,
                'album_year' => $albumYear
            ]);
            //get the albumId
            $albumId = Album::where('user_id', Auth::id())->
                where('album_name', $albumName)->where('album_year', $albumYear)->pluck('album_id')->first();

            foreach ($tracksKeys as $tracksKey) { //put tracks into DB
                $trackPerformerKey = 'track_performer' . $tracksKey;
                $performer = new PerformerId($request->get($trackPerformerKey));
                $performerId = $performer->getPerformerId(); //put the performer name if it's needed into DB and get the performer id

                $trackKey = 'track' . $tracksKey;
                if($request->hasFile($trackKey)) { //put the file with track on server and file data into DB
                    $file = $request->file($trackKey);
                    $filePathName = $request->file($trackKey)->getFilename();
                    $filePath = public_path() . '/tracks/' . Auth::id() . "/" . $albumId . '/' .$tracksKey;
                    $file->move($filePath); //put the file on server

                    if(file_exists($filePath . '/' . $filePathName)) {
                        try {
                            $audio = new Mp3Info($filePath . '/' . $filePathName, true); //calculate track duration
                            $trackDurationConvert = new SecondsToMinutes($audio->duration);
                            $trackDuration = $trackDurationConvert->getConversion();
                        }
                        catch (Exception $e) {
                            $trackDuration = "00:00";
                        }
                        $fileName = $request->all()['track_name' . $tracksKey]; //create new or take the existing file name
                        if (null == $fileName) {
                            $fileName = $request->file($trackKey)->getClientOriginalName();
                        } else {
                            if (!isset(pathinfo($fileName)['extension'])) {
                                $fileName = $fileName . "." .
                                    pathinfo($request->file($trackKey)->getClientOriginalName())['extension'];
                            }
                        }
                        Track::insert([ //put data into the tracks table
                            'track_id' => null,
                            'track_name' => $fileName,
                            'track_path' => $tracksKey . "/" . $filePathName,
                            'album_id' => $albumId,
                            'track_duration' => $trackDuration,
                            'performer_id' => $performerId
                        ]);


                    }
                }
            }
        }
        session(['tracksCount' => 1]);
        return redirect()->back();
    }

    /*
    * ajax method. Puts into session count of tracks which user want to load.
    * Works only in the case when user entered wrong data which not confirmed by Request.
    * For this purpose I used also session('tracksCount').
    * Value from this session I put into session('tracksCount') in NewAlbumRequest.
    * Then if validation is successful and we trap into NewAlbumController method post, I put into both session - 1.
    * When I trap into NewAlbumController method index, I don't know exactly how user trap here (from post method, or form get).
    * That is why I set into session('tracksCount') value - 1.
    */
    public function count(Request $request)  {
        session(['count' => $request->get('count')]);
    }
}
