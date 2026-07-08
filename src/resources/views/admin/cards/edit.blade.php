@extends('admin.layouts.app')

@section('title')
    {{ $card->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($card, ['route' => ['admin.cards.update', $card->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.cards.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection