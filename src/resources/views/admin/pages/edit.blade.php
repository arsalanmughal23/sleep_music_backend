@extends('admin.layouts.app')

@section('title')
    <h1>Edit Page</h1>
@endsection

@section('content')
    <div class="content">
        @include('adminlte-templates::common.errors')
        {{--<div class="box box-primary">--}}
            {{--<div class="box-body">--}}
                <div class="row">
                    {!! Form::model($page, ['route' => ['admin.pages.update', $page->id], 'method' => 'patch', 'id' => 'target']) !!}
                    @include('admin.pages.fields')
                    {!! Form::close() !!}
                </div>
            {{--</div>--}}
        {{--</div>--}}
    </div>
@endsection