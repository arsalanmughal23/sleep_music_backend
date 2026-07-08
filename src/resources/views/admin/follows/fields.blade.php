<!-- Followed User Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('followed_user_id', 'Followed User Id:') !!}
    {!! Form::text('followed_user_id', null, ['class' => 'form-control', 'placeholder'=>'Enter followed_user_id']) !!}
</div>

<!-- Followed By User Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('followed_by_user_id', 'Followed By User Id:') !!}
    {!! Form::text('followed_by_user_id', null, ['class' => 'form-control', 'placeholder'=>'Enter followed_by_user_id']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    @if(!isset($follow))
        {!! Form::submit(__('Save And Add Translations'), ['class' => 'btn btn-primary', 'name'=>'translation']) !!}
    @endif
    {!! Form::submit(__('Save And Add More'), ['class' => 'btn btn-primary', 'name'=>'continue']) !!}
    <a href="{!! route('admin.follows.index') !!}" class="btn btn-default">Cancel</a>
</div>