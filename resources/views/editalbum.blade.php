@extends('layouts.app')

@section('content')
    <div ng-app="deleteApp">
        <div ng-controller="DeleteController">
            <div id="deleteAlbum">
                <form method="POST" enctype="multipart/form-data" action="{{ route("delete.album", $album->album_id) }}">
                    {{csrf_field()}}
                    <input type="hidden" value="{{$album->album_id  }}" name="id" />
                    <h3>Are you sure you want to delete album "{{ $album->album_name }}"?</h3>
                    <div id="YesNo">
                        <div id="Yes"><input type="submit" value="Yes" id="Yes" /></div>
                        <div id="No" ng-click="No()">No</div>
                    </div>
                </form>
            </div>

            <div class="container">
                <div class="container">
                    <div class="row centered-form">
                        <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div id="delete" ng-click="Form()"></div>
                                    <h3 class="panel-title">Edit album "{{ $album->album_name }}"</h3>
                                </div>
                                <div class="panel-body">
                                    <form role="form" id="form" enctype="multipart/form-data" method="POST" action="{{ route("edit.album", $album->album_id) }}">
                                        {{ csrf_field() }}
                                        <input type="hidden" value="{{$album->album_id  }}" name="album_id" />
                                        <div class="row">
                                            @if ($errors->has('album_name'))
                                                <span class="help-block">
                                                <small>{{ $errors->first('album_name') }}</small>
                                            </span>
                                            @endif
                                            @if ($errors->has('album_year'))
                                                <span class="help-block">
                                                <small>{{ $errors->first('album_year') }}</small>
                                            </span>
                                            @endif

                                            <!-- input for Album Name   -->
                                            <div class="col-xs-6 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label><small>Album name</small></label>
                                                    <input type="text" name="album_name" id="album_name" class="form-control input-sm" placeholder="Album Name" value="{{ $album->album_name }}" />
                                                </div>
                                            </div>

                                                <!-- input for Album Year   -->
                                            <div class="col-xs-6 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label><small>Album Year</small></label>
                                                    <input type="text" name="album_year" id="album_year" class="form-control input-sm" placeholder="Album Year" value="{{ $album->album_year }}" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-6 col-sm-6 col-md-6">
                                                <h4>Add tracks</h4>
                                            </div>
                                        </div>



                                        <div id="wrapper">
                                            @foreach($tracks as $key => $track)
                                                <div class="border">
                                                    <b class="number">Track {{ $key + 1 }}</b>
                                                    <div class="deleteTrack"><input type="checkbox" name="checkbox{{ $track->track_id }}"/> Delete track</div>
                                                    <div class="row">
                                                        <!-- input for Track Name   -->
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <label><small>Track name</small></label>
                                                                <input type="text" name="track_name{{ $track->track_id }}" class="form-control input-sm" value="{{ $track->track_name }}" placeholder="Track Name" />
                                                            </div>
                                                        </div>
                                                        <!-- input for Performer Name   -->
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <label><small>Performer</small></label>
                                                                <input type="text" name="track_performer{{ $track->track_id }}" class="form-control input-sm" value="{{ $track->track_performer }}" placeholder="Track Performer" />
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <label><small>Track Duration</small></label>
                                                                {{ $track->track_duration }}

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        {{ $tracks->links() }}

                                        <div class="form-group" id="one-more-track" ng-click="Show()">
                                            <b>+</b> Add one more track
                                        </div>

                                        <!-- new track   -->
                                            <div class="border" id="newTrack">
                                                <b class="number">Track {{ $key + 2 }}</b>
                                                <div class="form-group input-file-wrapper">
                                                    <div class="button" onclick="this.nextElementSibling.nextElementSibling.click()"></div>
                                                    <input type="text" class="form-control input-sm image" onclick="this.nextElementSibling.click()" disabled />
                                                    <input type="file" name="track0" onchange="this.previousElementSibling.value = (this.files[0] != undefined) ? this.files[0].name : '' " class="form-control input-sm file" accept=".mp3"/>
                                                </div>

                                                <div class="row">
                                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="track_name0" value="{{ old('track_name0') }}" class="form-control input-sm track-name" placeholder="Track Name" />
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="track_performer0" value="{{ old('track_performer0') }}" class="form-control input-sm track-performer" placeholder="Track Performer" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        <input type="submit" value="Save" class="btn btn-info btn-block" />

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection