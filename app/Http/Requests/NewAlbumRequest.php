<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewAlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $count = session('count'); //look at TracksCountController
        if($count >= 1) {
            session(['tracksCount' => $count]);
        }

        $outputArray = [
            'album_name' => 'required|string|max:100',
            'album_year' => "required|integer",
        ];
        if($count >= 2) {
            for ($i = 2; $i <= $count; $i++) {
                $track = 'track' . $i;
                $outputArray = array_merge($outputArray, [
                    $track => 'mimes:mpga'
                ]);
            }
        }
        return $outputArray;
    }
}
