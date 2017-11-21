<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M2mPerformerTrack extends Model
{
    protected $primaryKey = ['track_id', 'performer_id'];

    public function track() {
        return $this->belongsTo('App\Track', 'track_id', 'track_id');
    }

    public function performer() {
        return $this->belongsTo('App\Performer', 'performer_id', 'performer_id');
    }
}
