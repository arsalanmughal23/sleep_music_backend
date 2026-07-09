@extends('admin.layouts.app')

@section('title')
    {{ $transaction->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($transaction, ['route' => ['admin.transactions.update', $transaction->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.transactions.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection