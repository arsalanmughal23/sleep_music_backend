@push('css')
    @include('admin.layouts.datatables_css')

    <style>
        #dataTableBuilder tbody tr {
            cursor: move;
        }
    </style>
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts')
    @include('admin.layouts.datatables_js')
    {!! $dataTable->scripts() !!}
@endpush


{{--Code For Table Row Positioning--}}

@push('scripts')

    <!-- jQuery UI -->
    <script type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- Datatables Js-->
    <script type="text/javascript" src="//cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>

    <script type="text/javascript">
        $(function () {

            // $("#dataTableBuilder tbody").sortable({
            //     items: "tr",
            //     cursor: 'move',
            //     opacity: 0.6,
            //     update: function () {
            //         sendOrderToServer();
            //     }
            // });

            // function sendOrderToServer() {

            //     var order = [];
            //     $('#dataTableBuilder tbody tr').each(function (index, element) {
            //         order.push({
            //             id: $(this).children('.reorder').children('.position').data('id'),
            //             position: index + 1
            //         });
            //     });
            //     $.ajax({
            //         type: "POST",
            //         dataType: "json",
            //         url: "{{URL::to('category/swape')}}",
            //         data: {
            //             order: order,
            //             _token: '{{csrf_token()}}'
            //         },
            //         success: function (response) {
            //             $('.buttons-reload').click();
            //             swal({
            //                 title: "Success",
            //                 text: response.message,
            //                 icon: "success",
            //             });
            //         }
            //     });

            // }
        });

    </script>

@endpush