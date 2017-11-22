<?php
/**
 * Created by PhpStorm.
 * User: olgakolos
 * Date: 22.11.17
 * Time: 11:51
 */
namespace App\Classes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class Album
{
    private $albumName;
    private $albumYear;
    private $albumId;

    public function __construct($albumName, $albumYear, $albumId = 0)
    {
        $this->albumYear = $albumYear;
        $this->albumName = $albumName;
        $this->albumId = $albumId;
        $this->album();
    }

    /*
     *  method checks albumYear data
     * method checks album existence in DB. And if it's true, creates new album new name
     */
    private function album() {
        if(null == $this->albumYear or !is_int(+$this->albumYear) or $this->albumYear > date('Y') or $this->albumYear < 1) {
            $this->albumYear = date('Y');
        }
        if(0 == $this->albumId) {
            $albumNameExistenceCount = DB::table('albums')->where('user_id', Auth::id())->
            where('album_name', 'like', "$this->albumName%")->where('album_year', $this->albumYear)->count();
        }
        else {
            $albumNameExistenceCount = DB::table('albums')->where('user_id', Auth::id())
                ->where('album_id', '!=', $this->albumId)
                ->where('album_name', 'like', "$this->albumName%")
                ->where('album_year', $this->albumYear)->count();
        }
        if ($albumNameExistenceCount > 0) {
            $this->albumName = $this->albumName . uniqid();
        }

    }

    public function getAlbumYear() {
        return $this->albumYear;
    }

    public function getAlbumName() {
        return $this->albumName;
    }
}