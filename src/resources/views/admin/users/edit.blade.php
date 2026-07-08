@extends('admin.layouts.app')

@section('title')
    @if(strpos(request()->url(), 'user/profile') !== false)
        <h1>Edit Profile</h1>
    @else
        <h1>Edit User</h1>
    @endif
@endsection

@section('content')
    <div class="content">
        
        @include('flash::message')
        
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">

                    {!! Form::model($user, ['route' => ['admin.users.update', $user->id], 'method' => 'patch', 'files'=>true, 'id'=>'target']) !!}
                    @include('admin.users.fields')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection