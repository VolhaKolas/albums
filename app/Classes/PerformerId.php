<?php
/**
 * Created by PhpStorm.
 * User: olgakolos
 * Date: 22.11.17
 * Time: 11:34
 */
namespace App\Classes;
use Illuminate\Support\Facades\DB;
class PerformerId
{
    private $performerId;
    private $performerName;

    public function __construct($performerName)
    {
        $this->performerName = $performerName;
        $this->performerId();
    }

    /*
     * method searches in DB performer id by performer name.
     * if there isn't performer name in DB, method creates new performer.
     */
    private function performerId() {
        if(null == $this->performerName) {
            $this->performerName = 'unknown';
        }
        $performerExistenceCount = DB::table('performers')->
        where('performer_name', $this->performerName)->pluck('performer_id');
        if(count($performerExistenceCount) > 0) {
            $this->performerId = $performerExistenceCount[0];
        }
        else {
            DB::table('performers')->insert([
                'performer_id' => null,
                'performer_name' => $this->performerName
            ]);
            $this->performerId = DB::table('performers')->
            where('performer_name', $this->performerName)->pluck('performer_id')[0];
        }
    }

    public function getPerformerId() {
        return $this->performerId;
    }
}