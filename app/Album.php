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
}
