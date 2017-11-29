<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'track_id', 'track_name', 'track_duration', 'track_path'
    ];
    public $timestamps = false;

    protected $primaryKey = 'album_id';

    public function album() {
        return $this->belongsTo('App\Album', 'album_id', 'album_id');
    }

    public function performer() {
        return $this->belongsTo('App\Performer', 'performer_id', 'performer_id');
    }
}
