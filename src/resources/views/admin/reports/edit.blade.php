@extends('admin.layouts.app')

@section('title')
    <h1>Edit Report</h1>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($report, ['route' => ['admin.reports.update', $report->id], 'method' => 'patch', 'files' => true]) !!}

                        @include('admin.reports.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection