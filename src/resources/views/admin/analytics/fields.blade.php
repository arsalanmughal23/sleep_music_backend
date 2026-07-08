<!-- Music Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('music_id', 'Music Id:') !!}
    {!! Form::text('music_id', null, ['class' => 'form-control', 'placeholder'=>'Enter music_id']) !!}
</div>

<!-- Views Field -->
<div class="form-group col-sm-6">
    {!! Form::label('views', 'Views:') !!}
    {!! Form::text('views', null, ['class' => 'form-control', 'placeholder'=>'Enter views']) !!}
</div>

<!-- User Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user_id', 'User Id:') !!}
    {!! Form::text('user_id', null, ['class' => 'form-control', 'placeholder'=>'Enter user_id']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    @if(!isset($analytic))
        {!! Form::submit(__('Save And Add Translations'), ['class' => 'btn btn-primary', 'name'=>'translation']) !!}
    @endif
    {!! Form::submit(__('Save And Add More'), ['class' => 'btn btn-primary', 'name'=>'continue']) !!}
    <a href="{!! route('admin.analytics.index') !!}" class="btn btn-default">Cancel</a>
</div>