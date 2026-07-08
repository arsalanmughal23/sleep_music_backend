@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@endpush

<!-- Parent Id Field -->
<div class="form-group col-sm-6 hidden">
    {!! Form::label('parent_id', 'Parent Category:') !!}
    {!! Form::hidden('parent_id', 0, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Category Name:', ['class' => 'required']) !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder'=>'Enter name','required' ,'maxlength'=>25]) !!}
</div>

<!-- Type Field -->
<div class="form-group col-sm-6 hidden">
    {!! Form::label('type', 'Type:') !!}
    {!! Form::hidden('type', \App\Models\Category::TYPE_AUDIO, null, ['class' => 'form-control select2']) !!}
</div>

<!-- Image Field -->
<div class="form-group col-sm-3">
    {!! Form::label('image', 'Image:') !!}
    {!! Form::file('', ['id' => 'input_image', 'class' => 'form-control', 'required' => (Route::currentRouteName() == 'admin.categories.create') ? 'required' : false, 'accept'=>
    "image/jpg,image/png,image/jpeg" ,isset($category)?'':'']) !!}
</div>

<!-- Image Field -->
@if(isset($category) && $category->image_url)
    <img id="fimg" src="{{ $category->image_url }}">
@endif

<input name="image" type="text" class="hidden" id="image" value="{{isset($category->image_url)?$category->image_url:''}}" />

<!-- Is Premium Field -->
<div class="form-group col-sm-2">
    {!! Form::label('is_premium', 'Is Premium:', ['style'=>'display:block']) !!}
    {!! Form::hidden('is_premium', 0) !!}
    {!! Form::checkbox('is_premium', 1, null, ['data-toggle'=>'toggle']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    @if(isset($category))
        {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.categories.index') !!}" class="btn btn-default">Cancel</a>
    @else
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.categories.index') !!}" class="btn btn-default">Cancel</a>
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

    $("form").on("submit", async function(e) {
        e.preventDefault();
        var form = $(this);
        $('input[type="submit"]').attr('disabled', 'disabled');
        var images = document.getElementById('input_image').files;
        var image = images[0];

        if (image) {
            var imageUrl = await s3upload(image);
            if(imageUrl){
                console.log('imageUrl', imageUrl);
                $('#image').val(imageUrl).attr('type','text');
            }
        }

        var dataString = $(this).serialize();

        $("#target").submit();
    })
</script>