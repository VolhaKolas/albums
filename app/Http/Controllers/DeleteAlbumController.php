<?php

namespace App\Http\Controllers;

use App\Album;
use Illuminate\Http\Request;

class DeleteAlbumController extends Controller
{
    /*
     * method deletes album
     */
    public function post(Request $request) {
        $albumId = $request->all()['id'];
        Album::where('album_id', $albumId)->delete();
        return redirect()->route('myAlbums');
    }
}
