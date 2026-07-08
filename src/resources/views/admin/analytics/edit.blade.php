@extends('admin.layouts.app')

@section('title')
    {{ $analytic->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($analytic, ['route' => ['admin.analytics.update', $analytic->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.analytics.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection