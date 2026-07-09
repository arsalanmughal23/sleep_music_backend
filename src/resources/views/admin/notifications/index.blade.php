@extends('admin.layouts.app')

@section('title')
    Notifications
@endsection

@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                @include('admin.notifications.table')
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function () {


        $('.input-sm').attr('maxlength', '3');
    });
</script>
