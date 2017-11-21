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

    public static function performerId($performerName) {
        if(null == $performerName) {
            $performerName = 'unknown';
        }
        $performerExistenceCount = DB::table('performers')->
        where('performer_name', $performerName)->pluck('performer_id');
        if(count($performerExistenceCount) > 0) {
            $performerId = $performerExistenceCount[0];
        }
        else {
            DB::table('performers')->insert([
                'performer_id' => null,
                'performer_name' => $performerName
            ]);
            $performerId = DB::table('performers')->
            where('performer_name', $performerName)->pluck('performer_id')[0];
        }
        return $performerId;
    }
}
