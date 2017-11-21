<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TracksCountController extends Controller
{
    public function post(Request $request)  {
        session(['count' => $request->all()['count']]);
    }
}
