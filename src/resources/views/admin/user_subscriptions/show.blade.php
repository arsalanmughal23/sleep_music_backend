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
                        @include('admin.user_subscriptions.show_fields')
                    </dl>
                    {!! Form::open(['route' => ['admin.user-subscriptions.destroy', $userSubscription->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        @ability('super-admin' ,'user-subscriptions.show')
                        <a href="{!! route('admin.user-subscriptions.index') !!}" class="btn btn-default">
                            <i class="glyphicon glyphicon-arrow-left"></i> Back
                        </a>
                        @endability
                    </div>
                    <div class='btn-group'>
                        @ability('super-admin' ,'user-subscriptions.edit')
                        <a href="{{ route('admin.user-subscriptions.edit', $userSubscription->id) }}" class='btn btn-default'>
                            <i class="glyphicon glyphicon-edit"></i> Edit
                        </a>
                        @endability
                    </div>
                    <div class='btn-group'>
                        @ability('super-admin' ,'user-subscriptions.destroy')
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
    </div>

    
<table class="table dataTable no-footer dtr-inline" role="grid"  style=" background:#FFF;margin:20px;width: 98%;">
    <h2   style=" margin:20px;">Transaction</h2>
    <dl class="dl-horizontal">
                  
        <th>User Name</th>
        <th>Currency</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Description</th>
        <th>Created At</th>
        <th>Actions</th>
        
        @if($userSubscription->transactions->count() > 0)
            @foreach($userSubscription->transactions as $transaction)
                <tr>
                    <td>
                        @if($transaction->user)
                            <a href="{{route('admin.users.show', $transaction->user->id)}}">{{$transaction->user->name ?? ''}}</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $transaction->currency }}</td>
                    <td>{{ $transaction->amount }}</td>
                    <td>{!! $transaction->status_badge !!}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->created_at }}</td>
                    <td>
                        <a href="{{route('admin.transactions.show', $transaction->id)}}"><i class="fa fa-eye"></i></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center">No Record Found</td>
            </tr>
        @endif
    </dl>
</table>

@endsection