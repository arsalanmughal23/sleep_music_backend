<div class="box">
    <div class="box-body">
        <!-- Slug Field -->
        <div class="form-group col-sm-12">
            {!! Form::label('slug', 'Slug:', ['class' => 'required']) !!}
            {!! Form::text('slug', null, [(isset($page)) ? 'disabled' : '', 'class' => 'form-control', 'placeholder'=>'Unique Slug', 'required']) !!}
        </div>

        @if(auth()->user()->hasRole('super-admin'))
            <!-- Status Field -->
            <div class="form-group col-sm-6">
                {!! Form::label('status', 'Status:') !!}
                {!! Form::hidden('status', 0) !!} <br>
                {!! Form::checkbox('status', 1, (isset($page) && $page->status) ?? 0, ['class'=> 'form-control', 'data-toggle'=>'toggle']) !!}
            </div>
        @else
            <!-- Status Field -->
            <div class="form-group col-sm-6 hidden">
                {!! Form::label('status', 'Status:') !!}
                {!! Form::hidden('status', 0) !!} <br>
                {!! Form::checkbox('status', 1, (isset($page) && $page->status) ?? 0, ['class'=> 'form-control', 'data-toggle'=>'toggle']) !!}
            </div>
        @endif

        <div class="clearfix"></div>

    </div>
    <!-- /.box-body -->
</div>
@if(isset($page))
    <div class="box">
        <div class="box-header with-border">
            Translated Attributes
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    @foreach($locales as $key=>$locale)
                        <li {{ $key==0? "class=active":"" }}>
                            <a href="#tab_{{$key+1}}"
                               data-toggle="tab">{{ ($locale->native_name===null)?$locale->title:$locale->native_name }}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($locales as $key=>$locale)
                        <div class="tab-pane {{$key==0?'active':''}} clearfix" id="tab_{{$key+1}}">
                        @php(App::setLocale($locale->code))
                        <!-- Title Field -->
                            <div class="form-group">
                                {!! Form::label('title', __('Title').':', ['class' => 'required']) !!}
                                {!! Form::text('title['.$locale->code.']', $page->translate($locale->code)['title'], ['class' => 'form-control', 'autofocus', 'style'=>'direction:'.$locale->direction]) !!}
                            </div>

                            <!-- Content Field -->
                            <div class="form-group">
                                {!! Form::label('content', __('Content').':', ['class' => 'required']) !!}
                                {!! Form::textarea('content['.$locale->code.']', $page->translate($locale->code)['content'], ['class' => 'form-control textarea', 'style'=>'direction:'.$locale->direction]) !!}
                            </div>

                            <!-- Status Field -->
                            <div class="form-group col-sm-6 hidden">
                                {!! Form::label('status', __('Status').':') !!}
                                {!! Form::hidden('translation_status['.$locale->code.']', 0) !!}
                                {!! Form::checkbox('translation_status['.$locale->code.']', 1, $page->translate($locale->code)['status'], ['class'=> 'form-control', 'data-toggle'=>'toggle']) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">

        </div>
        <!-- box-footer -->
    </div>
@endif

<!-- Submit Field -->
<div class="form-group col-sm-12">
    @if(isset($page))
        {!! Form::submit(__('Update'), ['class' => 'btn btn-primary']) !!}
    @else    
        {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
        {!! Form::submit(__('Save And Edit'), ['class' => 'btn btn-primary', 'name'=>'translation']) !!}
    @endif
    <a href="{!! route('admin.pages.index') !!}" class="btn btn-default">{{ __('Cancel') }}</a>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    setTimeout(function(){
        $('a[data-wysihtml5-dialog-action="cancel"]').remove();
        // .addClass('hidden')
    }, 500);
    
    $("form").on("submit", async function(e) {
        e.preventDefault();
        var form = $(this);
        $('input[type="submit"]').attr('disabled', 'disabled');
        
        slugElement = $('input[name="slug"]');
        slugElement.val(slugElement.val().replace(/\s+/g, '-'));

        $("#target").submit();
    })
</script>