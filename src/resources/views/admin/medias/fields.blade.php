@push('scripts')
{{--
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>--}}
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.410.0.min.js"></script>
{{--
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/npm/html-duration-picker/dist/html-duration-picker.min.js"></script>--}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    /* bucket region and pool id region must be same */
    var BucketName = "{{ env('AWS_BUCKET') }}";
    var bucketRegion = "{{ env('AWS_DEFAULT_REGION') }}";
    var IdentityPoolId = "us-east-2:400643db-d6c6-4f17-a3c1-87d71449af7e";

    AWS.config.update({
        region: bucketRegion,
        credentials: new AWS.CognitoIdentityCredentials({
            IdentityPoolId: IdentityPoolId
        })
    });

    var s3 = new AWS.S3({
        apiVersion: "2006-03-01",
        params: {
            Bucket: BucketName
        }
    });
</script>

@endpush

@php
    $maxSoundsLimit = config('constants.each_category_sounds_max_limit');
@endphp

<div class="form-group col-sm-6">
    {!! Form::label('category_id', 'Category:', ['class' => 'required']) !!}
    <!-- {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2']) !!} -->
    <select name="category_id" id="" class="form-control select2">
        <option value="" selected disabled title="Each category have only {{$maxSoundsLimit}} sounds">Select Category</option>
        @foreach($categories as $category)
            @php
                $soundVacancy = $category->media_count < $maxSoundsLimit;
                $isSelected = $category->id == (isset($media) ? $media->category_id : null); 
            @endphp
            <option value="{{ $category->id }}" {{ ($isSelected || $soundVacancy) ? '' : 'disabled' }} {{ $isSelected ? 'selected' : '' }} >{{ $category->name }}</option>
        @endforeach
    </select>
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Sound Name:', ['class' => 'required']) !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder'=>'Enter name' ,'maxlength'=>25 , 'required' ]) !!}
</div>


<div class="form-group col-sm-6">
    {!! Form::label('image', 'Image:') !!}
    <div class="clearfix"></div>
    <progress max="100" value="0" id="progress_image" style="display: none;"></progress>
    <span id="percentage_image" style="display: none;"></span>
    {!! Form::file('input_image' ,['id' => 'input_image', 'required' => (Route::currentRouteName() == 'admin.medias.create') ? 'required' : false, 'accept'=>
    "image/jpg,image/png,image/jpeg" ,isset($media)?'':'','class' => 'form-control' ]) !!}
    <br>
    @if(isset($media->image_url))
    <div class="col-sm-6">
        <img id="fimg" class="bg-black-gradient" style="padding:8px;" src="{{$media->image_url}}" alt="">
    </div>
    @endif
</div>
{{--<div class="clearfix"></div>--}}


<div class="form-group col-sm-6">
    {!! Form::label('file', 'Audio File:') !!}
    <div class="clearfix"></div>
    <progress max="100" value="0" id="progress_file" style="display: none;"></progress>
    <span id="percentage_file" style="display: none;"></span>
    {!! Form::file('input_file' ,['id' => 'input_file', 'required' => (Route::currentRouteName() == 'admin.medias.create') ? 'required' : false, 'accept'=>
    "audio/x-m4a, audio/mpeg, audio/wav" ,isset($media)?'':'','class' => 'form-control' ]) !!}
    <br>
    @if(isset($media->file_url))
    <div class="col-sm-6">
        <audio controls src="{{$media->file_url}}" style="width: 100%" id="media_preview"></audio>
    </div>
    @endif
</div>
<div class="clearfix"></div>

<!-- Is Premium Field -->
<div class="form-group col-sm-6">
    {!! Form::label('is_premium', 'Is Premium:') !!}
    {!! Form::hidden('is_premium', 0) !!}
    {!! Form::checkbox('is_premium', 1, null, ['class' => 'form-control', 'data-toggle'=>'toggle']) !!}
</div>

<input name="image" type="text" class="hidden" id="image" value="{{isset($media->image_url)?$media->image_url:''}}" />
<input name="file" type="text" class="hidden" id="file" value="{{isset($media->file_url)?$media->file_url:''}}" />


<!-- Submit Field -->
<div class="form-group col-sm-12">
    @if(isset($media))
        {!! Form::submit('Update', ['class' => 'btn btn-primary' ,'id' => 'save']) !!}
        <a href="{!! route('admin.medias.index') !!}" class="btn btn-default">Cancel</a>
    @else
        {!! Form::submit('Save', ['class' => 'btn btn-primary' ,'id' => 'save']) !!}
        <a href="{!! route('admin.medias.index') !!}" class="btn btn-default">Cancel</a>
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

    
    var inputAudioElement = document.getElementById("input_file")
    inputAudioElement.onchange = checkAudioIsValid

    function checkAudioIsValid(files){
        let file = files.target.files[0];
        let isAudioValid = validateAudio(file);
        if(file && !isAudioValid.status){
            console.log('isAudioValid', isAudioValid);
            inputAudioElement.setCustomValidity(isAudioValid.message);
        }else{
            inputAudioElement.setCustomValidity('');
        }
    }

    // $("form").on("submit", async function(e) {
    //     e.preventDefault();
    //     var form = $(this);
    //     $('input[type="submit"]').attr('disabled', 'disabled');
    //     var images = document.getElementById('input_image').files;
    //     var image = images[0];
    //     var audios = document.getElementById('input_file').files;
    //     var audio = audios[0];

    //     if (image) {
    //         var imageUrl = await s3upload(image);
    //         if(imageUrl){
    //             $('#image').val(imageUrl).attr('type','text');
    //         }
    //     }
    //     if (audio) {
    //         var audioUrl = await s3upload(audio);
    //         if(audioUrl){
    //             $('#file').val(audioUrl).attr('type','text');
    //         }
    //     }

    //     var dataString = $(this).serialize();

    //     $("#target").submit();
    // })
</script>

<!-- <script>
    var myVideos = [];
    window.URL = window.URL || window.webkitURL;

    document.getElementById('file').onchange = setFileInfo;

    function setFileInfo() {
        var fileInput =
            document.getElementById('file');

        var filePath = fileInput.value;
        // Allowing file type
        var allowedExtensions =
            /(\.mp3|\.webm)$/i;

        if (!allowedExtensions.exec(filePath)) {

            alert('Invalid audio type  must be Mp3 ');
            $('#media_length').val(0);
            fileInput.value = '';

            return false;
        } else {

            var files = this.files;
            let file = files[0];
            var video = document.createElement('audio');
            video.preload = 'metadata';

            video.onloadedmetadata = function() {
                window.URL.revokeObjectURL(video.src);
                var duration = video.duration;
                file.duration = duration;

                const sec = parseInt(duration, 10); // convert value to number if it's string

                $('#media_length').val(sec);

            }


        }
        video.src = URL.createObjectURL(files[0]);
    }
</script> -->