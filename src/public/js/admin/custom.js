setTimeout(function(){
    $('div.alert').attr('style', 'display:flex; justify-content:space-between;');
    $('div.alert').append('<span class="close-alert" style="padding:0px 8px; cursor:pointer;">x</span>');

    $('ul.alert').attr('style', 'list-style-type:none; position:relative;');
    $('ul.alert').append('<span class="close-alert" style="padding:0px 8px; cursor:pointer; position:absolute; right:1%; top:40%;">x</span>');
});

const maxFileSizesMeta = $('meta[name="max_file_sizes"]');

function validateImage(file) {
    try {
        const allowedFormats = ['image/png', 'image/jpeg', 'image/jpg'];

        if (!file) {
            throw new Error('Please select a file.');
        }

        if (!allowedFormats.includes(file.type)) {
            throw new Error('Unsupported file format. Please select a PNG, JPG, or JPEG image.');
        }

        const maxImageSizeInKB = maxFileSizesMeta.data('max_image_size_in_kb');
        const maxImageSizeInBytes = 1024 * maxImageSizeInKB;

        if (file.size > maxImageSizeInBytes) {
            throw new Error(`Image must be ${maxImageSizeInKB} KB or less.`);
        }

        return {
            status: true,
            message: 'File format is valid',
        };
    } catch (error) {
        return {
            status: false,
            message: error.message,
        };
    }
}

function validateAudio(file) {
    try {
        const allowedFormats = ['audio/mpeg', 'audio/x-m4a', 'audio/wav'];
        // const allowedFormats = ['audio/mpeg'];

        if (!file) {
            throw new Error('Please select a file.');
        }

        if (!allowedFormats.includes(file.type)) {
            throw new Error('Unsupported file format. Please select a MP3 audio.');
        }

        const maxAudioSizeInKB = maxFileSizesMeta.data('max_audio_size_in_kb');
        const maxAudioSizeInBytes = 1024 * maxAudioSizeInKB;

        if (file.size > maxAudioSizeInBytes) {
            throw new Error(`Audio file must be ${maxAudioSizeInKB} KB or less.`);
        }

        return {
            status: true,
            message: 'File format is valid',
        };
    } catch (error) {
        return {
            status: false,
            message: error.message,
        };
    }
}

function confirmDelete(form) {
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this record!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function (willDelete) {
        if (willDelete) {
            $(form).submit();
        }
    });
}

function formatFaIcon(state) {
    if (!state.id) return state.text; // optgroup
    return "<i class='fa fa-" + state.id + "'></i> " + state.text;
}

function defaultFormat(state) {
    return state.text;
}

$(function () {
    
    $('.close-alert').on('click', function(e){
        e.preventDefault();
        $(e.target).parent('.alert').remove();
    })

    $('input:checkbox, input:radio').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });

    /* $('.select2').each(function () {
         var format = $(this).data('format') ? $(this).data('format') : "defaultFormat";
         $(this).select2({
             theme: "bootstrap",
             templateResult: window[format],
             templateSelection: window[format],
             escapeMarkup: function (m) {
                 return m;
             }
         });
     });*/

    $('input:checkbox.checkall').on('ifToggled', function (event) {
        var newState = $(this).is(":checked") ? 'check' : 'uncheck';
        var css = $(this).data('check');
        $('input:checkbox.' + css).iCheck(newState);
    });

    //bootstrap WYSIHTML5 - text editor
    if($('.textarea').length > 0){
        $('.textarea').wysihtml5();
    }

    $('.select2').css('width', '100%');

    // dependent select 2
    $.fn.customLoad = function () {
        //Timepicker
        // $('.timepicker').timepicker({
        //     showInputs: false,
        //     containerClass: 'bootstrap-timepicker',
        //     timeFormat: 'HH:mm:ss p'
        // });

        $('.select2', $(this)).each(function () {
            var format = $(this).data('format') ? $(this).data('format') : "defaultFormat";
            var thisSelectElement = this;
            var options = {
                theme: "bootstrap",
                templateResult: window[format],
                templateSelection: window[format],
                escapeMarkup: function (m) {
                    return m;
                }
            };

            if ($(thisSelectElement).data('url')) {
                var depends;
                if ($(thisSelectElement).data('depends')) {
                    depends = $('[name=' + $(thisSelectElement).data('depends') + ']');
                    depends.on('change', function () {
                        $(thisSelectElement).val(null).trigger('change')
                        // $(thisSelectElement).trigger('change');
                    });
                }
                var url = $(thisSelectElement).data('url');

                options.ajax = {
                    url: url,
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term,
                            locale: 'en',
                            depends: $('option:selected', depends).val()
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.data, function (obj, id) {
                                return {id: obj.id, text: obj.name};
                            })
                        };
                    }

                }
            }

            var tabindex = $(thisSelectElement).attr('tabindex');

            $(thisSelectElement).select2(options);

            $(thisSelectElement).attr('tabindex', tabindex);
            $(thisSelectElement).on(
                'select2:select', (
                    function () {
                        $(this).focus();
                    }
                )
            );
        });

        $(this).on('click', '.audiocontrol', function () {
            var button = $(this), id = button.data('id'), audio = $('audio#' + id), audioJS = audio[0];
            if (audioJS.paused) {
                audioJS.play();
            } else {
                audioJS.pause();
            }
            audioJS.addEventListener('ended', function () {
                button
                    .find('i')
                    .removeClass("glyphicon-pause")
                    .addClass("glyphicon-play");
            });
            button
                .find('i')
                .toggleClass('glyphicon-play glyphicon-pause');
        });


        $(this).on('click', '.ajaxmodal', function () {
            var btn = $(this), url = btn.data('url'), title = btn.data('title');
            $.ajax(url, {
                success: function (response) {
                    $.updateModal(response, title);
                }
            })
        });

        $(this).on('submit', '.ajaxsubmit', function (e) {
            var form = $(this), url = form.attr('action'), method = form.attr('method'),
                callback = form.data('callback');
            $.ajax(url, {
                method: method,
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function (response) {
                    if (!!callback) {
                        window[callback](response);
                    } else {
                        $.updateModal(response, "");
                    }
                }
            });

            e.preventDefault();
            return false;
        });

        $(this).on('click', '.modal_close', function () {
            $(this).parents('.modal').hide();
        })
    };

    $.updateModal = function (response, title) {
        var modal = $("#showModal");
        response = $(response)
        modal.find('.modal-title').html(title);
        modal.find('.modal-body').html(response.find('.body').html());
        modal.find('.modal-footer').html(response.find('.footer').html());
        modal.customLoad();
        modal.show();
    };

    $(document).customLoad();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });

    $(document).on('click', '.btn-up-ajax', function () {

        var url = $(this).data('url');
        var token = $(this).data('token');
        var tr = $(this).parents('tr');
        var trPrev = tr.prev('tr');

        if (trPrev.length != 0) {
            var prevRowPos = $('input.inputSort', trPrev).val();
            var prevRowId = $('input.inputSort', trPrev).data('id');
            var rowPos = $('input.inputSort', tr).val();
            var rowId = $('input.inputSort', tr).data('id');

            // Handle UI
            trPrev.before(tr.clone());
            tr.remove();

            // Init Ajax to send sort values.
            var result = swappingRequest(prevRowPos, prevRowId, rowPos, rowId, url, token);

            if (result) {
                // Update chanel position - UI
                $('input.inputSort', tr).val('');
                $('input.inputSort', tr).val(prevRowPos);

                $('input.inputSort', trPrev).val('');
                $('input.inputSort', trPrev).val(RowPos);
            }
        }
    });

    $(document).on('click', '.btn-down-ajax', function () {

        var url = $(this).data('url');
        var token = $(this).data('token');
        var tr = $(this).parents('tr');
        var trPrev = tr.next('tr');
        if (trPrev.length != 0) {
            var prevRowPos = $('input.inputSort', trPrev).val();
            var prevRowId = $('input.inputSort', trPrev).data('id');
            var rowPos = $('input.inputSort', tr).val();
            var rowId = $('input.inputSort', tr).data('id');


            // Init Ajax to send sort values.
            swappingRequest(prevRowPos, prevRowId, rowPos, rowId, url, token, function (response) {
                var result = response.data.msg;
                if (result) {
                    // Update chanel position - UI
                    $('input.inputSort', tr).val(prevRowPos);
                    $('input.inputSort', trPrev).val(rowPos);

                    // Handle UI
                    tr.next('tr').after(tr.clone());
                    tr.remove();
                }
            });

        }
    });
});

function swappingRequest(prevRowPos, prevRowId, rowPos, rowId, url, token, cb) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + token
        }
    });
    $.ajax({
        method: "PUT",
        url: url,
        type: "JSON",
        async: false,
        data: {
            rowId: rowId,
            rowPosition: rowPos,
            prevRowId: prevRowId,
            prevRowPosition: prevRowPos
        },
        success: cb
    });
}

function afterAddToPlaylist(response) {
    if (response.success) {
        window.location.reload();
    }
}