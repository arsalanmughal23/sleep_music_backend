@extends('admin.layouts.app')

@section('title')
    {{ $playlist->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($playlist, ['route' => ['admin.playlists.update', $playlist->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.playlists.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection