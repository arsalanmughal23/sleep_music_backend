@extends('admin.layouts.app')

@section('title')
    {{ $package->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($package, ['route' => ['admin.packages.update', $package->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.packages.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection