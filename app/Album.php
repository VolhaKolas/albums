<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $primaryKey = 'user_id';

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function track() {
        return $this->hasMany('App\Track', 'album_id', 'album_id');
    }
}
