@extends('admin.layouts.app')

@section('title')
    {{ $like->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($like, ['route' => ['admin.likes.update', $like->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.likes.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection