<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class Album extends Model
{
    protected $fillable = [
        'album_id', 'album_name', 'album_year',
    ];

    protected $primaryKey = 'user_id';

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function track() {
        return $this->hasMany('App\Track', 'album_id', 'album_id');
    }

    public static function albumName($albumName, $albumYear) {
        $albumNameExistenceCount = DB::table('albums')->where('user_id', Auth::id())->
        where('album_name', 'like', "$albumName%")->where('album_year', $albumYear)->count();
        if($albumNameExistenceCount > 0) {
            $albumName = $albumName . ($albumNameExistenceCount + 1);
        }
        return $albumName;
    }

    public static function albumYear($albumYear) {
        if(null == $albumYear or !is_int(+$albumYear) or $albumYear > date('Y') or $albumYear < 1) {
            $albumYear = date('Y');
        }
        return $albumYear;
    }
}
