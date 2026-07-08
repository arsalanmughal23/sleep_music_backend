@if(!isset($parent))
    <!-- User Id Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('user_id', 'User (Playlist Creator):') !!}
        {!! Form::select('user_id',[0=>'None'] + $users, null, ['class' => 'form-control select2']) !!}
    </div>
@endif

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder'=>'Enter name']) !!}
</div>

<!-- Image Field -->
<div class="form-group col-sm-3">
    {!! Form::label('image', 'Image:') !!}
    {!! Form::file('image', ['class' => 'form-control', 'accept'=>'image/*']) !!}
</div>
<!-- Image Field -->
<div class="form-group col-sm-3" id="image_preview">
    @if(isset($playlist) && $playlist->image_url)
        <img src="{{ $playlist->image_url }}" style="max-width: 200px;">
    @endif
</div>

@if(!isset($parent))
    <!-- Type Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('type', 'Type:') !!}
        {!! Form::select('type', \App\Models\Category::$TYPES, null, ['class' => 'form-control select2']) !!}
    </div>

    <!-- Category Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('category_id', 'Category:') !!}
        {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'data-depends'=>'type', 'data-url'=>route('admin.playlist.categories')]) !!}
    </div>
    {{--<!-- Category Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('parent_id', 'Parent Playlist:') !!}
        {!! Form::select('parent_id', $playlists, null, ['class' => 'form-control select2', 'data-depends'=>'type', 'data-url'=>route('admin.playlist.playlists')]) !!}
    </div>--}}
    <!-- Is Featured Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('is_featured', 'Is Featured:') !!}
        {!! Form::hidden('is_featured' , 0, null) !!}
        {!! Form::checkbox('is_featured', 1, null, ['class' => 'form-control', 'data-toggle'=>'toggle']) !!}
    </div>
@else
    {!! Form::hidden('user_id', $parent->user_id ? $parent->user_id : 0) !!}
    {!! Form::hidden('type', $parent->type) !!}
    {!! Form::hidden('category_id', $parent->category_id) !!}
    {!! Form::hidden('parent_id', $parent->id) !!}
    {!! Form::hidden('is_featured', $parent->is_featured ? 1 : 0) !!}
@endif
{{--
<!-- Is Protected Field -->
<div class="form-group col-sm-6">
    {!! Form::label('is_protected', 'Is Protected:') !!}
    {!! Form::hidden('is_protected', 0, null) !!}
    {!! Form::checkbox('is_protected', 1, null, ['class' => 'form-control', 'data-toggle'=>'toggle' ]) !!}
</div>
--}}

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    @if(!isset($playlist))
        {!! Form::submit(__('Save And Edit'), ['class' => 'btn btn-primary', 'name'=>'translation']) !!}
    @endif
    {!! Form::submit(__('Save And Add More'), ['class' => 'btn btn-primary', 'name'=>'continue']) !!}
    <a href="{!! route('admin.playlists.index') !!}" class="btn btn-default">Cancel</a>
</div>