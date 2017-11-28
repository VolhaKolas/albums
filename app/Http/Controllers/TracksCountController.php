<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TracksCountController extends Controller
{
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
        session(['count' => $request->all()['count']]);
    }
}
