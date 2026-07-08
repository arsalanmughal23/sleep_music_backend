@extends('admin.layouts.app')

@section('title')
    {{ $mediaview->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($mediaview, ['route' => ['admin.mediaviews.update', $mediaview->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.mediaviews.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection