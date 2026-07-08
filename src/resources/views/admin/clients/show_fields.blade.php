<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $client->id !!}</dd>

<!-- Name Field -->
<dt>{!! Form::label('name', 'Name:') !!}</dt>
<dd>{!! $client->name !!}</dd>

<!-- Ip Address Field -->
<dt>{!! Form::label('cidr', 'CIDR:') !!}</dt>
<dd>{!! $client->cidr !!}</dd>

<!-- Mac Field -->
<dt>{!! Form::label('mac', 'MAC Address:') !!}</dt>
<dd>{!! $client->mac !!}</dd>

<!-- License Field -->
<dt>{!! Form::label('license', 'License:') !!}</dt>
<dd>{!! $client->license !!}</dd>

<!-- Status Field -->
<dt>{!! Form::label('connection_status', 'Connection Status:') !!}</dt>
<dd>{!! '<span class="label label-' . \App\Helper\Util::getBoolCss($client->connection_status) . '">' . \App\Helper\Util::getBoolText($client->connection_status, "Connected", "Disconnected") . '</span>'; !!}</dd>

<!-- Status Field -->
<dt>{!! Form::label('status', 'Status:') !!}</dt>
<dd>{!! '<span class="label label-' . \App\Helper\Util::getBoolCss($client->status) . '">' . \App\Helper\Util::getBoolText($client->status) . '</span>'; !!}</dd>

<!-- Status Message Field -->
<dt>{!! Form::label('status_message', 'Status Message:') !!}</dt>
<dd>{!! $client->status_message !!}</dd>

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $client->created_at !!}</dd>

<!-- Updated At Field -->
<dt>{!! Form::label('updated_at', 'Updated At:') !!}</dt>
<dd>{!! $client->updated_at !!}</dd>
