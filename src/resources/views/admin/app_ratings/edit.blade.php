@extends('admin.layouts.app')

@section('title')
    {{ $appRating->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($appRating, ['route' => ['admin.app-ratings.update', $appRating->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.app_ratings.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection