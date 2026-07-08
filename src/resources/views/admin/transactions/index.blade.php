@extends('admin.layouts.app')

@section('title')
    {{ $title }}
@endsection
<style>
    div.dataTables_processing {
        font-weight: bold;
    }

    .btn-default {
        padding: 4px !important;
    }

    .btn-danger {
        padding: 4px !important;
    }
</style>
@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                @include('admin.transactions.table')
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>
@endsection

