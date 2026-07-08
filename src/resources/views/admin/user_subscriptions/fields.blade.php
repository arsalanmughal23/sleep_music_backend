<!-- Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id', 'Id:') !!}
    {!! Form::text('id', null, ['class' => 'form-control', 'placeholder'=>'Enter id']) !!}
</div>

<!-- User Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user_id', 'User Id:') !!}
    {!! Form::text('user_id', null, ['class' => 'form-control', 'placeholder'=>'Enter user_id']) !!}
</div>

<!-- Package Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('package_id', 'Package Id:') !!}
    {!! Form::text('package_id', null, ['class' => 'form-control', 'placeholder'=>'Enter package_id']) !!}
</div>

<!-- Reference Key Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reference_key', 'Reference Key:') !!}
    {!! Form::text('reference_key', null, ['class' => 'form-control', 'placeholder'=>'Enter reference_key']) !!}
</div>

<!-- Platform Field -->
<div class="form-group col-sm-6">
    {!! Form::label('platform', 'Platform:') !!}
    {!! Form::text('platform', null, ['class' => 'form-control', 'placeholder'=>'Enter platform']) !!}
</div>

<!-- Data Field -->
<div class="form-group col-sm-6">
    {!! Form::label('data', 'Data:') !!}
    {!! Form::text('data', null, ['class' => 'form-control', 'placeholder'=>'Enter data']) !!}
</div>

<!-- Expiry Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('expiry_date', 'Expiry Date:') !!}
    {!! Form::text('expiry_date', null, ['class' => 'form-control', 'placeholder'=>'Enter expiry_date']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::text('status', null, ['class' => 'form-control', 'placeholder'=>'Enter status']) !!}
</div>

<!-- Created At Field -->
<div class="form-group col-sm-6">
    {!! Form::label('created_at', 'Created At:') !!}
    {!! Form::text('created_at', null, ['class' => 'form-control', 'placeholder'=>'Enter created_at']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    @if(!isset($userSubscription))
        {!! Form::submit(__('Save And Add Translations'), ['class' => 'btn btn-primary', 'name'=>'translation']) !!}
    @endif
    {!! Form::submit(__('Save And Add More'), ['class' => 'btn btn-primary', 'name'=>'continue']) !!}
    <a href="{!! route('admin.user-subscriptions.index') !!}" class="btn btn-default">Cancel</a>
</div>