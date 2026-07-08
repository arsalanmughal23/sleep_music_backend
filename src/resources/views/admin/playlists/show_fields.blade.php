<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $playlist->id !!}</dd>

@if($playlist->user)
    <!-- Parent Id Field -->
    <dt>{!! Form::label('user', 'User:') !!}</dt>
    <dd>{!! $playlist->user->details->full_name !!}</dd>
@endif

<!-- Name Field -->
<dt>{!! Form::label('name', 'Name:') !!}</dt>
<dd>{!! $playlist->name !!}</dd>

<!-- Type Field -->
<dt>{!! Form::label('type', 'Type:') !!}</dt>
<dd>{!! $playlist->type_text !!}</dd>

<!-- Category Field -->
<dt>{!! Form::label('category', 'Category:') !!}</dt>
<dd>{!! $playlist->category? $playlist->category->name : \App\Helper\Util::getNone() !!}</dd>

@if($playlist->type==\App\Models\Category::TYPE_VIDEO)
    <!-- Parent Field -->
    <dt>{!! Form::label('parent', 'Parent:') !!}</dt>
    <dd>{!! $playlist->parent ? $playlist->parent->full_name : \App\Helper\Util::getNone() !!}</dd>
@endif

<!-- Is Featured Field -->
<dt>{!! Form::label('is_featured', 'Is Featured:') !!}</dt>
<dd>{!! "<span class='label label-". \App\Helper\Util::getBoolCss($playlist->is_featured) . "'>" . \App\Helper\Util::getBoolText($playlist->is_featured) . '</span>' !!}</dd>

<!-- Is Protected Field -->
<dt>{!! Form::label('is_protected', 'Is Protected:') !!}</dt>
<dd>{!! "<span class='label label-". \App\Helper\Util::getBoolCss($playlist->is_protected) . "'>" . \App\Helper\Util::getBoolText($playlist->is_protected) . '</span>' !!}</dd>

<!-- Sort Key Field -->
<dt>{!! Form::label('sort_key', 'Sort Key:') !!}</dt>
<dd>{!! $playlist->sort_key !!}</dd>

<!-- Image Field -->
<dt>{!! Form::label('image', 'Image:') !!}</dt>
<dd style="margin-top:10px;"><img src="{!! $playlist->image_url !!}" style="max-width: 200px;"/></dd>

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $playlist->created_at !!}</dd>

<!-- Updated At Field -->
<dt>{!! Form::label('updated_at', 'Updated At:') !!}</dt>
<dd>{!! $playlist->updated_at !!}</dd>

<!-- Deleted At Field -->
<dt>{!! Form::label('deleted_at', 'Deleted At:') !!}</dt>
<dd>{!! $playlist->deleted_at !!}</dd>

