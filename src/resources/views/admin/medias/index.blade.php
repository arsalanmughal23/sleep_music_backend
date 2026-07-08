@extends('admin.layouts.app')

@section('title')
    <h1>Sounds</h1>
@endsection
<style>
    .btn.btn-xs {
        padding: 4px 6px !important;
    }
</style>
@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>

        <form action="{{ route('admin.medias.index') }}" class="box box-primary" style="padding:10px; background:white;">
            <div style="display:flex; gap:5px; margin-bottom:5px;">
                <select name="category_id" class="form-control">
                    <option selected disabled>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request()->get('category_id') == $category->id ? 'selected' : '' }} >{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="premium_type" class="form-control">
                    <option selected disabled>Select Is Premium</option>
                    <option value="1" {{ request()->get('premium_type') == '1' ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ request()->get('premium_type') == '0' ? 'selected' : '' }}>No</option>
                </select>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('admin.medias.index') }}" class="btn btn-danger">Reset</a>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-body">
                @include('admin.medias.table')
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

