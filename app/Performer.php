<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Performer extends Model
{
    public function m2mPerformerTrack() {
        return $this->hasMany('App\M2mPerformerTrack', 'performer_id', 'performer_id');
    }
}
