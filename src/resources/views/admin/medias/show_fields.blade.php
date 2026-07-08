<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $media->id !!}</dd>

@if($media->user)
    <!-- User Id Field -->
    <dt>{!! Form::label('user_id', 'User:') !!}</dt>
    <dd>{!! $media->user->details->full_name !!}</dd>
@endif

@if($media->category)
    <!-- Category Id Field -->
    <dt>{!! Form::label('category_id', 'Category:') !!}</dt>
    <dd>{!! $media->category->name !!}</dd>
@endif

<!-- Name Field -->
<dt>{!! Form::label('name', 'Name:') !!}</dt>
<dd>{!! $media->name !!}</dd>

<!-- Duration Field -->
<dt>{!! Form::label('duration', 'Duration:') !!}</dt>
<dd>{!! $media->duration ?? 0 !!} Seconds</dd>

<!-- Is Featured Field -->
<dt>{!! Form::label('is_premium', 'Is Premium:') !!}</dt>
<dd>{!! "<span class='label label-". \App\Helper\Util::getBoolCss($media->is_premium) . "'>" . \App\Helper\Util::getBoolText($media->is_premium) . '</span>' !!}</dd>

<!-- Image Field -->
<dt>{!! Form::label('image', 'Image:') !!}</dt>
<dd style="margin-top:10px;"><img id="fimg" class="bg-black-gradient" style="padding:8px;" src="{!! $media->image_url !!}"/></dd>

<!-- File Field -->
<dt>{!! Form::label('media', 'Audio:') !!}</dt>
@if($media->file_url)
    <dd style="margin-top:10px;">
        <audio controls src="{{$media->file_absolute_url}}" style="width: 100%"></audio>
    </dd>
@else
    <dd><span class='label label-danger'>Not Exists</span></dd>
@endif

<!-- File Url Field -->
<dt>{!! Form::label('file_url', 'Audio Url:') !!}</dt>
@if($media->file_url)
    <dd>{!! $media->file_url !!}</dd>
@else
    <dd><span class='label label-danger'>Not Exists</span></dd>
@endif

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $media->created_at !!}</dd>

<!-- Updated At Field -->
<dt>{!! Form::label('updated_at', 'Updated At:') !!}</dt>
<dd>{!! $media->updated_at !!}</dd>

