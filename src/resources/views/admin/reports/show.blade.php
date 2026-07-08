@extends('admin.layouts.app')

@section('title')
    <h1>View Report</h1>
@endsection

@section('content')
    <div class="content">

        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        @if (Session::has('errors'))
            <div class="alert alert-danger">
                {{ Session::get('errors')->first() }}
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    <dl class="dl-horizontal">
                        @include('admin.reports.show_fields')
                    </dl>
                </div>
                <div class="row" style="padding-left:20px; display:flex; gap:2px;">
                    <div class='btn-group'>
                        @ability('super-admin' ,'reports.show')
                        <a href="{!! route('admin.reports.index') !!}" class="btn btn-default">
                            <i class="glyphicon glyphicon-arrow-left"></i> Back
                        </a>
                        @endability
                    </div>
                    <div class='btn-group'>
                        @ability('super-admin' ,'reports.edit')
                        <a href="{{ route('admin.reports.edit', $report->id) }}" class='btn btn-default'>
                            <i class="glyphicon glyphicon-edit"></i> Edit
                        </a>
                        @endability
                    </div>

                    {!! Form::open(['route' => ['admin.reports.destroy', $report->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        @ability('super-admin' ,'reports.destroy')
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i> Delete', [
                            'type' => 'submit',
                            'class' => 'btn btn-danger',
                            'onclick' => "confirmDelete($(this).parents('form')[0]); return false;"
                        ]) !!}
                        @endability
                    </div>
                    {!! Form::close() !!}
                    
                    @ability('super-admin' ,'report-update-status')
                    {!! Form::open(['url' => ['admin/reports-status/'.$report->id], 'method' => 'put']) !!}
                        <div class='btn-group'>
                            <!-- 'glyphicon-check' : 'glyphicon-alert' -->
                            @if($report->status)   
                                <input type="hidden" value="0" name="status">
                                {!! Form::button('<i class="glyphicon glyphicon-hourglass"></i> Under Investigation', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger'
                                ]) !!}
                            @else                        
                                <input type="hidden" value="1" name="status">
                                {!! Form::button('<i class="glyphicon glyphicon-check"></i> Resolved', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-success'
                                ]) !!}
                            @endif
                        </div>
                    {!! Form::close() !!}
                    @endability
                </div>
            </div>
        </div>
    </div>
@endsection