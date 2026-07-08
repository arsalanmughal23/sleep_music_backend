@extends('admin.layouts.app')

@section('title')
    Users
@endsection

@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>

        <form action="{{ route('admin.users.index') }}" class="box box-primary" style="padding:10px; background:white;">
            <div style="display:flex; gap:5px; margin-bottom:5px;">
                <select name="subscription" class="form-control">
                    <option selected disabled>Select Is Subscriber</option>
                    <option value="1" {{ request()->get('subscription') == '1' ? 'selected' : '' }} >Yes</option>
                    <option value="0" {{ request()->get('subscription') == '0' ? 'selected' : '' }} >No</option>
                </select>
                <select name="status" class="form-control">
                    <option selected disabled>Select Status</option>
                    <option value="1" {{ request()->get('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request()->get('status') == '0' ? 'selected' : '' }}>In-Active</option>
                </select>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-danger">Reset</a>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-body">
                @include('admin.users.table')
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>
@endsection

