@extends('admin.layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.views.store', 'files' => true]) !!}

                        @include('admin.views.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
