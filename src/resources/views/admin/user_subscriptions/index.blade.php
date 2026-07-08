@extends('admin.layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>

        <form action="{{ route('admin.user-subscriptions.index') }}" class="box box-primary" style="padding:10px; background:white;">
            <div style="display:flex; gap:5px; margin-bottom:5px;">
                <select name="subscription_name" class="form-control">
                    <option selected disabled>Select Product Id</option>
                    <option value="monthly" {{ request()->get('subscription_name') == "monthly" ? 'selected' : '' }} >Monthly</option>
                    <option value="yearly" {{ request()->get('subscription_name') == "yearly" ? 'selected' : '' }} >Yearly</option>
                </select>
                
                <select name="status" class="form-control">
                    <option selected disabled>Select Status</option>
                    <option value="{{ App\Models\UserSubscription::STATUS_ACTIVE }}" {{ App\Models\UserSubscription::STATUS_ACTIVE == request()->get('status') ? 'selected' : '' }} >Active</option>
                    <option value="{{ App\Models\UserSubscription::STATUS_CANCELLED }}" {{ App\Models\UserSubscription::STATUS_CANCELLED == request()->get('status') ? 'selected' : '' }} >Cancelled</option>
                    <option value="{{ App\Models\UserSubscription::STATUS_EXPIRE }}" {{ App\Models\UserSubscription::STATUS_EXPIRE == request()->get('status') ? 'selected' : '' }} >Expire</option>
                    <option value="{{ App\Models\UserSubscription::STATUS_TRIAL }}" {{ App\Models\UserSubscription::STATUS_TRIAL == request()->get('status') ? 'selected' : '' }} >Trial</option>
                </select>

                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('admin.user-subscriptions.index') }}" class="btn btn-danger">Reset</a>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-body">
                    @include('admin.user_subscriptions.table')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection

