<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $deleteType->id !!}</dd>

<!-- Name Field -->
<dt>{!! Form::label('name', 'Name:') !!}</dt>
<dd>{!! $deleteType->name !!}</dd>

<!-- Status Field -->
<dt>{!! Form::label('status', 'Status:') !!}</dt>
<dd>
    @if ($deleteType->status == \App\Models\DeleteType::ACTIVE)
        Active
    @else
        Inactive
    @endif
</dd>

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $deleteType->created_at !!}</dd>



