<div>
    <div class="body">
        <div class="row">
        {!! Form::open([
        'id' => 'add_to_playlist',
        'route' => ['admin.add_to_playlist.media', $media->id],
        'files' => true,
        'class'=> 'ajaxsubmit',
        'data-callback' => 'afterAddToPlaylist'
        ]) !!}

        <!-- playlist Field -->
            <div class="form-group col-sm-12">
                {!! Form::label('playlist[]', 'Playlist:') !!}
                {!! Form::select('playlist[]',$playlists, $media->playlist()->pluck('id')->all(), ['class' => 'form-control select2', 'multiple'=>'multiple', 'style'=>'width:100%']) !!}
            </div>


            {!! Form::close() !!}

        </div>
    </div>
    <div class="footer">
        {!! Form::submit('Save Changes',  ['class'=>'btn btn-primary', 'form'=>'add_to_playlist']) !!}
        {!! Form::button('Close',  ['class'=>'btn btn-danger modal_close', "data-dismiss"=>"modal"]) !!}
    </div>
</div>