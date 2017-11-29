<?php

namespace App\Http\Controllers;

use App\Album;
use Illuminate\Support\Facades\Auth;
class MyAlbumsController extends Controller
{
    /*
     * method shows user's albums
     */
    public function index() {
        $albums = Album::where('user_id', Auth::id())->
            select('album_id', 'album_name', 'album_year')->paginate(15);
        return view('myalbums', compact('albums'));
    }
}
