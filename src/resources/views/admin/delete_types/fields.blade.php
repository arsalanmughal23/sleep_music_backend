<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:', ['class' => 'required']) !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder'=>'Enter name']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:', ['class' => 'required']) !!}
    <select name="status" class="form-control">
        <option value="1" @if($deleteType->status == 1) selected @endif>Active</option>
        <option value="0" @if($deleteType->status == 0) selected @endif>Inactive</option>
    </select>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    @if(isset($deleteType->id))
        {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.delete-types.index') !!}" class="btn btn-default">Cancel</a>
    @else
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.delete-types.index') !!}" class="btn btn-default">Cancel</a>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $("form").on("submit", async function(e) {
        e.preventDefault();
        var form = $(this);
        $('input[type="submit"]').attr('disabled', 'disabled');
        
        $("#target").submit();
    })
</script>