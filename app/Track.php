<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $primaryKey = 'album_id';

    public function album() {
        return $this->belongsTo('App\Album', 'album_id', 'album_id');
    }

    public function m2mPerformerTrack() {
        return $this->hasMany('App\M2mPerformerTrack', 'track_id', 'track_id');
    }
}
