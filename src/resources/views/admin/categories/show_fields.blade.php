<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $category->id !!}</dd>

@if($category->parent)
    <!-- Parent Id Field -->
    <dt>{!! Form::label('parent', 'Parent:') !!}</dt>
    <dd>{!! $category->parent->name !!}</dd>
@endif
<!-- Name Field -->
<dt>{!! Form::label('name', 'Name:') !!}</dt>
<dd>{!! $category->name !!}</dd>

<!-- Type Field -->
<dt>{!! Form::label('type', 'Type:') !!}</dt>
<dd>{!! $category->type_text !!}</dd>

<!-- Image Field -->
<dt>{!! Form::label('image', 'Image:') !!}</dt>
<dd style="margin-top:10px;"><img id="fimg" src="{!! $category->image_url !!}"/>
</dd>

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $category->created_at !!}</dd>

<!-- Updated At Field -->
<dt>{!! Form::label('updated_at', 'Updated At:') !!}</dt>
<dd>{!! $category->updated_at !!}</dd>

