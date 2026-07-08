@extends('admin.layouts.app')

@section('title')
    {{ $clientConnectionLog->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($clientConnectionLog, ['route' => ['admin.client-connection-logs.update', $clientConnectionLog->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.client_connection_logs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection