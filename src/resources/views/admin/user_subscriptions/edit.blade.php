@extends('admin.layouts.app')

@section('title')
    {{ $userSubscription->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($userSubscription, ['route' => ['admin.user-subscriptions.update', $userSubscription->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.user_subscriptions.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection