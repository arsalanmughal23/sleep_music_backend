{!! Form::open(['route' => ['admin.medias.destroy', $row->id], 'method' => 'delete']) !!}
<div class='btn-group'>
    @if(isset($row) && isset($category))
        @if($row->file_url && $category->type===\App\Models\Category::TYPE_AUDIO)
            <button type="button" class="btn btn-default btn-xs audiocontrol" data-id="audio_{{$row->id}}">
                <i class="glyphicon glyphicon-play"></i>
            </button>
            <div class="hidden">
                <audio src="{{$row->file_url}}" id="audio_{{$row->id}}"></audio>
            </div>
        @endif
    @endif
    {{--<button type="button" class="btn btn-default btn-xs ajaxmodal"--}}
    {{--data-url="{{route('admin.add_to_playlist.media', $row->id)}}"--}}
    {{--data-title="Add to Playlist">--}}
    {{--<i class="glyphicon glyphicon-plus"></i>--}}
    {{--</button>--}}
    @ability('super-admin' ,'medias.show')
    <a href="{{ route('admin.medias.show', $row->id) }}" class='btn btn-default btn-xs'>
        <i class="glyphicon glyphicon-eye-open"></i>
    </a>
    @endability
    @ability('super-admin' ,'medias.edit')
    <a href="{{ route('admin.medias.edit', $row->id) }}" class='btn btn-default btn-xs'>
        <i class="glyphicon glyphicon-edit"></i>
    </a>
    @endability
    @ability('super-admin' ,'medias.destroy')
    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => "confirmDelete($(this).parents('form')[0]); return false;"
    ]) !!}
    @endability
</div>
{!! Form::close() !!}
