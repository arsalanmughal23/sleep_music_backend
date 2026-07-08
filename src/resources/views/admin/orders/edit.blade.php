@extends('admin.layouts.app')

@section('title')
    {{ $order->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($order, ['route' => ['admin.orders.update', $order->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.orders.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection