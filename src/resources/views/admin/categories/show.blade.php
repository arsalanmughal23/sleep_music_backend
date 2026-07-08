@extends('admin.layouts.app')

@section('title')
    <h1>View Category</h1>
@endsection

@push('css')
<style>
    .buttons-create{display: none;}
    .buttons-excel{
        border-top-left-radius: 3px !important;
        border-bottom-left-radius: 3px !important;
    }
</style>
@endpush

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    <dl class="dl-horizontal">
                        @include('admin.categories.show_fields')
                    </dl>
                    {!! Form::open(['route' => ['admin.categories.destroy', $category->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        @ability('super-admin' ,'categories.show')
                        <a href="{!! route('admin.categories.index') !!}" class="btn btn-default">
                            <i class="glyphicon glyphicon-arrow-left"></i> Back
                        </a>
                        @endability
                    </div>
                    <div class='btn-group'>
                        @ability('super-admin' ,'categories.edit')
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class='btn btn-default'>
                            <i class="glyphicon glyphicon-edit"></i> Edit
                        </a>
                        @endability
                    </div>
                    <div class='btn-group'>
                        @ability('super-admin' ,'categories.destroy')
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i> Delete', [
                            'type' => 'submit',
                            'class' => 'btn btn-danger',
                            'onclick' => "confirmDelete($(this).parents('form')[0]); return false;"
                        ]) !!}
                        @endability
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                @include('admin.medias.table')
            </div>
        </div>
    </div>

    {{--<div class="content">--}}
    {{--<div class="box box-primary">--}}
    {{--<div class="box-header with-border">--}}
    {{--<h3 class="box-title">Media Related to {{$category->name}} </h3>--}}
    {{--</div>--}}
    {{--<div class="box-body">--}}
    {{--<div class="row">--}}

    {{--<div class="col-sm-12 datatable-action-urls"--}}
    {{--data-action-create="{{route('admin.medias.create')}}">--}}
    {{--@push('css')--}}
    {{--@include('admin.layouts.datatables_css')--}}
    {{--@endpush--}}

    {{--{!! $dataTable->table(['width' => '100%']) !!}--}}

    {{--@push('scripts')--}}
    {{--@include('admin.layouts.datatables_js')--}}
    {{--{!! $dataTable->scripts() !!}--}}
    {{--@endpush--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
@endsection