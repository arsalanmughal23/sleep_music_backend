@extends('admin.layouts.app')

@section('title')
    <h1>Edit Category</h1>
@endsection

@section('content')
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($category, ['route' => ['admin.categories.update', $category->id], 'method' => 'patch', 'files' => true, 'id' => 'target']) !!}

                        @include('admin.categories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection