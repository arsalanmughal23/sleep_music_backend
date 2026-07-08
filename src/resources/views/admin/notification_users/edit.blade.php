@extends('admin.layouts.app')

@section('title')
    {{ $notificationUser->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($notificationUser, ['route' => ['admin.notification-users.update', $notificationUser->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.notification_users.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection