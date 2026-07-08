@extends('admin.layouts.app')

@section('title')
    <h1>Create Sound</h1>
@endsection

@section('content')
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.medias.store', 'files' => true ,'id'=>'target']) !!}

                    @include('admin.medias.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
