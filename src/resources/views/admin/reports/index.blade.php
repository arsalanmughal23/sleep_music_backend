@extends('admin.layouts.app')

@section('title')
    <h1>Reports</h1>
@endsection
<style>
    /* div.dataTables_processing {
        font-weight: bold;
    }

    .btn-default {
        padding: 4px !important;
    }

    .btn-danger {
        padding: 4px !important;
    } */
</style>
@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        
        <form action="{{ route('admin.reports.index') }}" class="box box-primary" style="padding:10px; background:white;">
            <div style="display:flex; gap:5px; margin-bottom:5px;">
                <select name="report_type_id" class="form-control">
                    <option selected disabled>Select Report Type</option>
                    @foreach($reportTypes as $reportType)
                        <option value="{{ $reportType->id }}" {{ request()->get('report_type_id') == $reportType->id ? 'selected' : '' }} >{{ $reportType->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control">
                    <option selected disabled>Select Status</option>
                    <option value="1" {{ request()->get('status') == '1' ? 'selected' : '' }}>Resolved</option>
                    <option value="0" {{ request()->get('status') == '0' ? 'selected' : '' }}>Under Investigation</option>
                </select>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-danger">Reset</a>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-body">
                @include('admin.reports.table')
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

