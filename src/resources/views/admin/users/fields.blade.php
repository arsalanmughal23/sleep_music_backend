@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@endpush

<!-- Name Field -->
<div class="form-group col-sm-6 hidden">
    {!! Form::label('name', 'Full Name:') !!}
    {!! Form::text('name', $user->name?? null, ['class' => 'form-control' , 'maxlength' => 50]) !!}
</div>

<!-- First Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('first_name', 'First Name:', ['class' => 'required']) !!}
    {!! Form::text('first_name', $user->details->first_name ?? null, ['class' => 'form-control','required' , 'maxlength' => 50]) !!}
</div>
<!-- Last Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('last_name', 'Last Name:', ['class' => 'required']) !!}
    {!! Form::text('last_name', $user->details->last_name ?? null, ['class' => 'form-control','required' , 'maxlength' => 50]) !!}
</div>


<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', 'Email:', ['class' => 'required']) !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'id' => 'email', isset($user)?'readonly':'' ,'required' , 'maxlength' => 250]) !!}
</div>

@if (strpos(Request::url(), 'users') !== false)
    <!-- Roles Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('roles', 'Roles:', ['class' => 'required']) !!}
        {!! Form::select('roles[]', $roles, null, ['class' => 'form-control select2', isset($user) ? 'disabled' : 'required']) !!}
    </div>

@endif

<!-- Gender Field -->
@if(\Auth::user()->hasRole('admin') || \Auth::user()->hasRole('super-admin'))

@else
    <div class="form-group col-sm-6">
        {!! Form::label('gender', 'Gender:') !!}
        {!! Form::select('gender', $gender, null, ['class' => 'form-control select2']) !!}
    </div>
@endif

<div class="clearfix"></div>

<!-- Image Field -->
<div class="form-group col-sm-3">
    {!! Form::label('image', 'Image:') !!}
    {!! Form::file('', ['id' => 'input_image', 'class' => 'form-control' ,'accept'=>
    "image/jpg,image/png,image/jpeg"]) !!}
</div>

<img style="width:100px;" src="{{ $user->details->image_url ?? '' }}"  />

<div class="clearfix"></div>
<!-- Email Field -->
{{--<div class="form-group col-sm-3">--}}
{{--{!! Form::label('email_updates', 'Receive Updates On Emails:') !!}--}}
{{--<div class="clearfix"></div>--}}
{{--{!! Form::hidden('email_updates', 0) !!}--}}
{{--{!! Form::checkbox('email_updates', 1,  true, ['data-toggle'=>'toggle']) !!}--}}
{{--</div>--}}

<!-- Email Field -->
{{--<div class="form-group col-sm-3">--}}
{{--{!! Form::label('push_notification', 'Receive Push Notification:') !!}--}}
{{--<div class="clearfix"></div>--}}
{{--{!! Form::hidden('push_notification', 0) !!}--}}
{{--{!! Form::checkbox('push_notification', 1,  true, ['data-toggle'=>'toggle']) !!}--}}
{{--</div>--}}
{{--<div class="clearfix"></div>--}}


<!-- Password Field -->
<div class="form-group col-sm-6">
    {!! Form::label('password', 'Password:', ['class' => (isset($user) ? '' : 'required')]) !!}
    {!! Form::password('password', ['class' => 'form-control show_password', 'minlength'=> 6,'maxlength'=> 250 , isset($user)?'':'required', 'autocomplete'=>'new-password']) !!}
</div>

{{--@if(isset($user) && $user->details->gender)--}}
{{--<!-- Image Field -->--}}
{{--<div class="form-group col-sm-3">--}}
{{--<img src="{{ $user->details->gender }}">--}}
{{--</div>--}}
{{--@endif--}}

<!-- Password Field -->
<div class="form-group col-sm-6">
    {!! Form::label('password_confirmation', 'Confirm Password:', ['class' => (isset($user) ? '' : 'required')]) !!}
    {!! Form::password('password_confirmation', ['class' => 'form-control show_password']) !!}
</div>

<!-- Show Password Field -->
<div class="form-group col-sm-12">
    <button type="button" id="show_password" data-show="false" onclick="showPasswordToggle()">
        <i class="fa fa-eye-slash"></i>
    </button>
    <label for="show_password">Show Password</label>
</div>


@if(strpos(request()->url(), 'user/profile') == false)
    <br>
    <!-- Status Field -->
    <div class="form-group col-sm-6">
        <label for="status" class="required">Status</label>
        <select name="status" class="form-control" required="required">
            <option selected disabled value="">Select Status</option>
            <option value="1" {{isset($user) && $user->status == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{isset($user) && $user->status == 0 ? 'selected' : '' }}>In Active</option>
        </select>
    </div>
@endif

<input name="image" type="text" class="hidden" id="image" value="{{ $user->details->image_url ?? '' }}" />

<!-- Submit Field -->
<div class="form-group col-sm-12">
    @if(isset($user))
        {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
    @else
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    @endif
    
    @php 
        $currentUrl = request()->url() 
    @endphp
    @if(strpos($currentUrl, 'user/profile') == false)
        <a href="{!! route('admin.users.index') !!}" class="btn btn-default">Cancel</a>
    @endif
</div>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>

    var inputImageElement = document.getElementById("input_image")
    inputImageElement.onchange = checkImageIsValid
    
    function checkImageIsValid(files){
        let file = files.target.files[0];
        let isImageValid = validateImage(file);
        if(file && !isImageValid.status){
            console.log('isImageValid', isImageValid);
            inputImageElement.setCustomValidity(isImageValid.message);
        }else{
            inputImageElement.setCustomValidity('');
        }
    }

    var showPasswordElement = $("#show_password");

    function showPasswordToggle(){
        let show = showPasswordElement.data('show') ?? 0;
        show = !show;
        showPasswordElement.data('show', show);
        $('input.show_password').prop('type', show ? 'text' : 'password');

        if (show) {
            showPasswordElement.find('i.fa').removeClass('fa-eye-slash').addClass('fa-eye')
        }else{
            showPasswordElement.find('i.fa').removeClass('fa-eye').addClass('fa-eye-slash')
        }
    }

    var password = document.getElementById("password")
        , confirm_password = document.getElementById("confirm-password");

    function validatePassword() {
        if (password.value != password_confirmation.value) {
            password_confirmation.setCustomValidity("Password do not match");
        } else {
            password_confirmation.setCustomValidity('');
        }
    }

    var emailElement = document.getElementById("email")
    function validateEmail() {
        // const regex = /^[a-zA-Z0-9._%+-]+@.*\.com$/;
        const regex = /^[a-zA-Z0-9._%+-]+@.*\..+$/;
        if (regex.test(emailElement.value)) {
            emailElement.setCustomValidity('');
        } else {
            emailElement.setCustomValidity('Email Format is Invalid');
        }
    }
    emailElement.onkeyup = validateEmail;

    password.onchange = validatePassword;
    password_confirmation.onkeyup = validatePassword;


    $("form").on("submit", async function(e) {
        e.preventDefault();
        var form = $(this);
        $('input[type="submit"]').attr('disabled', 'disabled');

        let fname = $('input[name="first_name"]').val();
        let lname = $('input[name="last_name"]').val();
        let name = `${fname} ${lname}`.trim().replace(/\s+/g, ' ')
        $('input[name="name"]').val(name);

        var images = document.getElementById('input_image').files;
        var image = images[0];

        if (image) {
            var imageUrl = await s3upload(image);
            if(imageUrl){
                $('#image').val(imageUrl).attr('type','text');
            }
        }

        var dataString = $(this).serialize();

        $("#target").submit();
    })
</script>