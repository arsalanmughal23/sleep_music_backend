@extends('admin.layouts.app')
@section('title')
    <h1>View User</h1>
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
                        @include('admin.users.show_fields')
                    </dl>
                </div>
                
                <div class="row" style="padding-left:20px; display:flex; gap:2px;">

                    <!-- Check is Viewable User have Admin Role -->
                    @if($user->hasRole('admin'))
                        <div class='btn-group'>
                            @ability('super-admin' ,'users.show')
                            <a href="{!! route('admin.users.index') !!}" class="btn btn-default">
                                <i class="glyphicon glyphicon-arrow-left"></i> Back
                            </a>
                            @endability
                        </div>
                    @else
                        {!! Form::open(['route' => ['admin.users.destroy', $user->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                @ability('super-admin' ,'users.show')
                                <a href="{!! route('admin.users.index') !!}" class="btn btn-default">
                                    <i class="glyphicon glyphicon-arrow-left"></i> Back
                                </a>
                                @endability
                            </div>
                            <div class='btn-group'>
                                @ability('super-admin' ,'users.edit')
                                <a href="{{ route('admin.users.edit', $user->id) }}" class='btn btn-default'>
                                    <i class="glyphicon glyphicon-edit"></i> Edit
                                </a>
                                @endability
                            </div>
                            <div class='btn-group'>
                                @ability('super-admin' ,'users.destroy')
                                {!! Form::button('<i class="glyphicon glyphicon-trash"></i> Delete', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger',
                                    'onclick' => "confirmDelete($(this).parents('form')[0]); return false;"
                                ]) !!}
                                @endability
                            </div>
                        {!! Form::close() !!}
                        
                        {!! Form::open(['url' => ['admin/users-status/'.$user->id], 'method' => 'put']) !!}
                            <div class='btn-group'>
                                <!-- 'glyphicon-check' : 'glyphicon-ban-circle' -->
                                @if($user->status)
                                    <input type="hidden" value="0" name="status">
                                    {!! Form::button('<i class="glyphicon glyphicon-ban-circle"></i> In-Active', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-danger'
                                    ]) !!}
                                @else
                                    <input type="hidden" value="1" name="status">
                                    {!! Form::button('<i class="glyphicon glyphicon-check"></i> Active', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-success'
                                    ]) !!}
                                @endif
                            </div>
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
  
  
<table class="table dataTable no-footer dtr-inline" role="grid"  style=" background:#FFF;margin:20px;width: 98%;">
    <h2   style=" margin:20px;">Media</h2>
    <dl class="dl-horizontal">
                  
        <th>Name</th>
        <th>Category</th>
        <th>Actions</th>

        @if($user->media->count() > 0)
            @foreach($user->media as $media)
                <tr>
                    <td>{{$media->name}}</td>
                    <td>{{$media->category->name}}</td> 
                    <td>
                        <div class='btn-group'>
                            {!! Form::open(['route' => ['admin.medias.destroy', $media->id], 'method' => 'delete']) !!}
                                @ability('super-admin' ,'media.destroy')
                                {!! Form::button('<i class="glyphicon glyphicon-trash"></i> Delete', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger',
                                    'onclick' => "confirmDelete($(this).parents('form')[0]); return false;"
                                ]) !!}
                                @endability
                            {!! Form::close() !!}
                            
                            @if($media->category)
                                @if($media->file_url && $media->category->type===\App\Models\Category::TYPE_AUDIO)
                                    <button type="button" class="btn btn-default btn-xs audiocontrol" data-id="audio_{{$media->id}}">
                                        <i class="glyphicon glyphicon-play"></i>
                                    </button>

                                    <div class="hidden">
                                        <audio src="{{$media->file_url}}" id="audio_{{$media->id}}"></audio>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3" class="text-center">No Record Found</td>
            </tr>
        @endif
    </dl>
</table>
<style>
    .content-wrapper{
        padding-bottom:50px;
    }
</style>
@endsection