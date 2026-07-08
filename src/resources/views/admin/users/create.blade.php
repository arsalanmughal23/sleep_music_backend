@extends('admin.layouts.app')

@section('title')
    <h1>Create User</h1>
@endsection

@section('content')
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.users.store', 'files'=>true, 'id'=>'target']) !!}
                    @include('admin.users.fields')
                    <div id="message"></div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection
