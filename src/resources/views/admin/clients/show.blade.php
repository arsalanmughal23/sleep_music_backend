@extends('admin.layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    <dl class="dl-horizontal">
                        @include('admin.clients.show_fields')
                    </dl>
                    {!! Form::open(['route' => ['admin.clients.destroy', $client->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        @ability('super-admin' ,'clients.show')
                        <a href="{!! route('admin.clients.index') !!}" class="btn btn-default">
                            <i class="glyphicon glyphicon-arrow-left"></i> Back
                        </a>
                        @endability
                    </div>
                    <div class='btn-group'>
                        @ability('super-admin' ,'clients.edit')
                        <a href="{{ route('admin.clients.edit', $client->id) }}" class='btn btn-default'>
                            <i class="glyphicon glyphicon-edit"></i> Edit
                        </a>
                        @endability
                    </div>
                    <div class='btn-group'>
                        @ability('super-admin' ,'clients.destroy')
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

        @ability('super-admin' ,'client-connection-logs.show')
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Connection Status Logs</h3>
            </div>
            <div class="box-body">
                @include('admin.client_connection_logs.table')
            </div>
        </div>
        @endability
    </div>
@endsection

