<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $clientConnectionLog->id !!}</dd>

<!-- Client Id Field -->
<dt>{!! Form::label('client_id', 'Client:') !!}</dt>
<dd>{!! $clientConnectionLog->client->name !!}</dd>

<!-- Status Field -->
<dt>{!! Form::label('status', 'Status:') !!}</dt>
<dd>{!! '<span class="label label-' . \App\Helper\Util::getBoolCss($clientConnectionLog->status) . '">' . \App\Helper\Util::getBoolText($clientConnectionLog->status,"Connected", "Disconnected") . '</span>'; !!}</dd>

<!-- Seconds Until Next Field -->
<dt>{!! Form::label('seconds_until_next', 'Total Time (Seconds):') !!}</dt>
<dd>{!! $clientConnectionLog->seconds_until_next !!}</dd>

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $clientConnectionLog->created_at !!}</dd>

<!-- Updated At Field -->
<dt>{!! Form::label('updated_at', 'Updated At:') !!}</dt>
<dd>{!! $clientConnectionLog->updated_at !!}</dd>

<!-- Deleted At Field -->
<dt>{!! Form::label('deleted_at', 'Deleted At:') !!}</dt>
<dd>{!! $clientConnectionLog->deleted_at !!}</dd>

