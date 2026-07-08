@extends('admin.layouts.app')

@section('title')
    {{ $view->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($view, ['route' => ['admin.views.update', $view->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.views.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection