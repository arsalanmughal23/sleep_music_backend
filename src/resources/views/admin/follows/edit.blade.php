@extends('admin.layouts.app')

@section('title')
    {{ $follow->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($follow, ['route' => ['admin.follows.update', $follow->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.follows.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection