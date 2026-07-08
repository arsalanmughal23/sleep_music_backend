@extends('admin.layouts.app')

@section('title')
    <h1>Create Report</h1>
@endsection

@section('content')
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.reports.store', 'files' => true]) !!}

                        @include('admin.reports.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
