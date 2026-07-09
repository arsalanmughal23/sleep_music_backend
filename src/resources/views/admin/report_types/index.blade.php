@extends('admin.layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')
        <style>
            .btn .btn-default .btn-xs {
                padding: 5px !important;
            }

            .btn .btn-danger .btn-xs {
                padding: 5px !important;
            }
        </style>
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                @include('admin.report_types.table')
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>
@endsection

