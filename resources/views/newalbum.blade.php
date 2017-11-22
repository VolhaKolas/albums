@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row centered-form">
            <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Create new album</h3>
                    </div>
                    <div class="panel-body" ng-app="formApp">
                        <form role="form" id="form" ng-controller="FormController" enctype="multipart/form-data" method="POST" action="{{ route('newAlbum') }}">
                            {{ csrf_field() }}
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
                                        <input type="text" name="album_name" id="album_name" value="{{ old('album_name') }}" class="form-control input-sm" placeholder="Album Name" />
                                    </div>
                                </div>

                                    <!-- input for Album Year   -->
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="album_year" value="{{ old('album_year') }}" id="album_year" class="form-control input-sm" placeholder="Album Year" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <h4>Add tracks</h4>
                                </div>
                            </div>



                            <div id="wrapper">
                                @for($i = 1; $i <= $count; $i++)
                                    <div class="border">
                                        <b class="number">Track {{ $i }}</b>

                                        @if ($errors->has('track'. $i))
                                            <span class="help-block">
                                                <small>{{ $errors->first('track' . $i) }}</small>
                                            </span>
                                        @endif

                                        <!-- input for Track File   -->
                                        <div class="form-group input-file-wrapper">
                                            <div class="button" onclick="this.nextElementSibling.nextElementSibling.click()"></div>
                                            <input type="text" class="form-control input-sm image" onclick="this.nextElementSibling.click()" disabled />
                                            <input type="file" name="track{{ $i }}" onchange="this.previousElementSibling.value = (this.files[0] != undefined) ? this.files[0].name : '' " class="form-control input-sm file" accept=".mp3"/>
                                        </div>

                                        <div class="row">
                                            <!-- input for Track Name   -->
                                            <div class="col-xs-6 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <input type="text" name="track_name{{ $i }}" value="{{ old('track_name' . $i) }}" class="form-control input-sm track-name" placeholder="Track Name" />
                                                </div>
                                            </div>

                                            <!-- input for Performer Name   -->
                                            <div class="col-xs-6 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <input type="text" name="track_performer{{ $i }}" value="{{ old('track_performer' . $i) }}" class="form-control input-sm track-performer" placeholder="Track Performer" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="form-group" id="one-more-track" ng-click="cloneDiv()">
                                <b>+</b> Add one more track
                            </div>

                            <input type="submit" value="Create album" class="btn btn-info btn-block" />

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection