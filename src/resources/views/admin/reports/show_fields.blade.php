<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $report->id !!}</dd>

<dt>User</dt>
<dd><a href="{{ route('admin.users.show', $report->user_id) }}">{{ $report->users->details->full_name ?? '-' }}</a></dd>

<dt>Report Against</dt>
<dd><a href="{{ route('admin.medias.show', $report->instance_id) }}">{{ $report->media->name ?? '-' }}</a></dd>

<dt>Status</dt>
<dd><span class="label label-{{ $report->status ? 'success' : 'danger' }}">{{ $report->status ? 'Resolved' : 'Under Investigation' }}</span></dd>

<!-- Music Id Field -->
<dt>Report Type</dt>
<dd>
    @if(count($report->types) > 0)
        @foreach($report->types as $type)
            <kbd>{{ $type->name }}</kbd>
        @endforeach
    @else
        <kbd>No Report Type</kbd>
    @endif
</dd>

<dt>Description</dt>
<dd>{{$report->description??'N/A'}}</dd>

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $report->created_at !!}</dd>
