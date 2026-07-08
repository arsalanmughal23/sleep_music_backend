<!-- Type Field -->
<div class="form-group col-sm-12">
    {!! Form::label('name', 'Name:', ['class' => 'required']) !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder'=>'Enter name']) !!}
</div>

<div class="form-group col-sm-12 hidden">
    {!! Form::label('type', 'Type:') !!}
    <select name="type" class="form-control">
        <option value="10">Account</option>
        <option value="20" @if($reportType->type == 20) selected @endif>Content</option>
    </select>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    @if(isset($reportType->id))
        {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.report-types.index') !!}" class="btn btn-default">Cancel</a>
    @else
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('admin.report-types.index') !!}" class="btn btn-default">Cancel</a>
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