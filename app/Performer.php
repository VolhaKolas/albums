<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Performer extends Model
{
    protected $fillable = [
        'performer_id', 'performer_name',
    ];

    public function m2mPerformerTrack() {
        return $this->hasMany('App\M2mPerformerTrack', 'performer_id', 'performer_id');
    }
}
