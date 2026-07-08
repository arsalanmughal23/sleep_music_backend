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
                {!! Form::open(['route' => 'admin.import.media.2']) !!}
                @foreach($columns as $key => $column)
                    <div class="form-group col-sm-6">
                        {!! Form::label($key, $column) !!}
                        {!! Form::select($key, $csv_headers, null, ['class' => 'form-control select2']) !!}
                    </div>
                @endforeach
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