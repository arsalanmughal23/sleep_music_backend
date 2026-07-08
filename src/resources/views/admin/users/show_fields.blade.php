<!-- Id Field -->
<dt>{!! Form::label('id', 'Id:') !!}</dt>
<dd>{!! $user->id !!}</dd>

<!-- First Name Field -->
<dt>{!! Form::label('first_name', 'First Name:') !!}</dt>
<dd>{!! $user->details->first_name !!}</dd>

<!-- Last Name Field -->
<dt>{!! Form::label('last_name', 'Last Name:') !!}</dt>
<dd>{!! $user->details->last_name !!}</dd>

<!-- Email Field -->
<dt>{!! Form::label('email', 'Email:') !!}</dt>
<dd>{!! $user->email !!}</dd>

<!-- Is Subscriber Field -->
<dt>{!! Form::label('is_subscriber', 'Is Subscriber:') !!}</dt>
<dd>
    @if($user->is_subscriber)
        <span class="label label-success">Yes</span>
    @else
        <span class="label label-danger">No</span>
    @endif
</dd>

<dt>Status</dt>
<dd><span class="label label-{{ $user->status ? 'success' : 'danger' }}">{{ $user->status ? 'Active' : 'In-Active' }}</span></dd>

<!-- Email Field -->
<dt>{!! Form::label('roles', 'Roles:') !!}</dt>
<dd>{!! $user->rolesCsv !!}</dd>


<!-- Image Field -->
<dt>{!! Form::label('image', 'Image:') !!}</dt>
<dd style="margin-top:10px;"><img style="width:100px;" src="{!! $user->details->image_url !!}"/></dd>


{{--<!-- Password Field -->--}}
{{--<dt>{!! Form::label('password', 'Password:') !!}</dt>--}}
{{--<dd>{!! $user->password !!}</dd>--}}

{{--<!-- Remember Token Field -->--}}
{{--<dt>{!! Form::label('remember_token', 'Remember Token:') !!}</dt>--}}
{{--<dd>{!! $user->remember_token !!}</dd>--}}

<!-- Created At Field -->
<dt>{!! Form::label('created_at', 'Created At:') !!}</dt>
<dd>{!! $user->created_at !!}</dd>

<!-- Updated At Field -->
<dt>{!! Form::label('updated_at', 'Updated At:') !!}</dt>
<dd>{!! $user->updated_at !!}</dd>



