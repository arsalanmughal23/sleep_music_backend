@extends('admin.layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        @include('adminlte-templates::common.errors')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                {!! Form::open(['route' => 'admin.import.media.1', 'files' => true]) !!}
                <div class="form-group col-sm-6">
                    {!! Form::label('file', 'CSV File:') !!}
                    {!! Form::file('file', ['class' => 'form-control', 'accept'=>'.csv']) !!}
                </div>
                <div class="form-group col-sm-6">
                    {!! Form::label('type', 'Type:') !!}
                    {!! Form::select('type', \App\Models\Category::$TYPES, null, ['class' => 'form-control select2']) !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('has_headers', 'Has Header (Title Row):') !!}
                    {!! Form::hidden('has_headers', 0) !!}
                    {!! Form::checkbox('has_headers', 1, 1, ['class' => 'form-control', 'data-toggle'=>'toggle']) !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('create_artists', 'Find or Create Artists by Name:') !!}
                    {!! Form::hidden('create_artists', 0) !!}
                    {!! Form::checkbox('create_artists', 1, 1, ['class' => 'form-control', 'data-toggle'=>'toggle']) !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('create_categories', 'Find or Create Categories by Name:') !!}
                    {!! Form::hidden('create_categories', 0) !!}
                    {!! Form::checkbox('create_categories', 1, 1, ['class' => 'form-control', 'data-toggle'=>'toggle']) !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('create_playlists', 'Find or Create Playlists by Name:') !!}
                    {!! Form::hidden('create_playlists', 0) !!}
                    {!! Form::checkbox('create_playlists', 1, 1, ['class' => 'form-control', 'data-toggle'=>'toggle']) !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>
@endsection
@push("css")
    <style>
        label {
            min-width: 20% !important;
        }
    </style>
@endpush