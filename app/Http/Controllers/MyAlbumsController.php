<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class MyAlbumsController extends Controller
{
    /*
     * method shows user's albums
     */
    public function index() {
        $albums = DB::table('albums')->where('user_id', Auth::id())->
            select('album_id', 'album_name', 'album_year')->get();
        return view('myalbums', compact('albums'));
    }
}
