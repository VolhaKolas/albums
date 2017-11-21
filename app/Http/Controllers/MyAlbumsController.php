<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyAlbumsController extends Controller
{
    public function index() {
        return view('myalbums');
    }
}
