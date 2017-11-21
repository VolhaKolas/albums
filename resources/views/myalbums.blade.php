@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <a href="{{  route('newAlbum') }}"><button type="button" class="btn btn-info btn-lg btn3d"><b>+</b> Add new album</button></a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-list-search">
                    <thead>
                    <tr>
                        <th><i>Album Name</i></th>
                        <th><i>Year</i></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><a href="{{ route('editAlbum', ['number' => 1]) }}">Rilnt <sup>edit</sup></a></td>
                        <td>2016</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection