<!-- Client Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('client_id', 'Client Id:') !!}
    {!! Form::text('client_id', null, ['class' => 'form-control', 'placeholder'=>'Enter client_id']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::text('status', null, ['class' => 'form-control', 'placeholder'=>'Enter status']) !!}
</div>

<!-- Seconds Until Next Field -->
<div class="form-group col-sm-6">
    {!! Form::label('seconds_until_next', 'Seconds Until Next:') !!}
    {!! Form::text('seconds_until_next', null, ['class' => 'form-control', 'placeholder'=>'Enter seconds_until_next']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    @if(!isset($clientConnectionLog))
        {!! Form::submit(__('Save And Add Translations'), ['class' => 'btn btn-primary', 'name'=>'translation']) !!}
    @endif
    {!! Form::submit(__('Save And Add More'), ['class' => 'btn btn-primary', 'name'=>'continue']) !!}
    <a href="{!! route('admin.client-connection-logs.index') !!}" class="btn btn-default">Cancel</a>
</div>