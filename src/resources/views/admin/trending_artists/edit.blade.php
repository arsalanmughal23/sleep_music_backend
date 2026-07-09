@extends('admin.layouts.app')

@section('title')
    {{ $trendingArtist->name }} <small>{{ $title }}</small>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($trendingArtist, ['route' => ['admin.trending-artists.update', $trendingArtist->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.trending_artists.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection