<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder'=>'Enter name']) !!}
</div>

<!-- Ip Address Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cidr', 'CIDR:') !!}
    {!! Form::text('cidr', null, ['class' => 'form-control', 'placeholder'=>'127.0.0.1 OR 192.168.0.0/16']) !!}
</div>

<!-- Mac Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mac', 'MAC Address:') !!}
    {!! Form::text('mac', null, ['class' => 'form-control', 'placeholder'=>'01:23:45:67:89:AB OR 01-23-45-67-89-AB']) !!}
</div>

<!-- Connection Limit Field -->
<div class="form-group col-sm-4">
    {!! Form::label('connection_limit', 'Connection Limit:') !!}
    {!! Form::number('connection_limit', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-2">
    {!! Form::label('status', 'Status:', ['style'=>'display:block']) !!}
    {!! Form::hidden('status', 0) !!}
    {!! Form::checkbox('status', 1,  null, ['data-toggle'=>'toggle']) !!}
</div>

<!-- Status Message Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('status_message', 'Status Message:') !!}
    {!! Form::textarea('status_message', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    @if(!isset($client))
        {!! Form::submit(__('Save And Add Translations'), ['class' => 'btn btn-primary', 'name'=>'translation']) !!}
    @endif
    {!! Form::submit(__('Save And Add More'), ['class' => 'btn btn-primary', 'name'=>'continue']) !!}
    <a href="{!! route('admin.clients.index') !!}" class="btn btn-default">Cancel</a>
</div>