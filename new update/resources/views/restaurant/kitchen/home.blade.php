@extends('layouts.app')
@section('title', __('kitchen.home_kitchen'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'restaurant.home_kitchen' ) @show_tooltip(__('restaurant.home_kitchen_help_long'))
    </h1>
</section>
 
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
        @can('restaurant.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'create'])}}" 
                    data-container=".creat_kitchen_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
        @endcan
        @endslot  
            <div class="table-responsive">
            @can('restaurant.view')
                <table class="table table-bordered table-striped" id="home_kitchen_ta">
                    <thead>
                        <tr>
                            <th>@lang('restaurant.kitchen_name')</th>
                            <th>@lang('restaurant.category_name')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead> 
                </table>
                @endcan
            </div> 
    @endcomponent

    <div class="modal fade creat_kitchen_modal contains_select2" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection






@section('javascript')
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('submit', 'form#table_add_form', function(e){
                e.preventDefault();
                var data = $(this).serialize();

                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result){
                        if(result.success == true){
                            $('div.creat_kitchen_modal').modal('hide');
                            toastr.success(result.msg);
                            home_kitchen_ta.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            //kitchen table
            var home_kitchen_ta = $('#home_kitchen_ta').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/kitchen-home',
                    columnDefs: [ {
                        // "targets": [0],
                        // "targets": [1,2, 3],
                        "orderable": false,
                        "searchable": false 
                    } ],
                    columns: [ 
                        { data: 'name', name: 'name'  },
                        { data: 'category.name', name: 'category' },
                        { data: 'action', name: 'action'}
                    ],
                });

            $(document).on('click', 'button.edit_kitchen_button', function(){

                $( "div.creat_kitchen_modal" ).load( $(this).data('href'), function(){

                    $(this).modal('show');

                    $('form#edit_form').submit(function(e){
                        e.preventDefault();
                        var data = $(this).serialize();

                        $.ajax({
                            method: "POST",
                            url: $(this).attr("action"),
                            dataType: "json",
                            data: data,
                            success: function(result){
                                if(result.success == true){
                                    $('div.creat_kitchen_modal').modal('hide');
                                    toastr.success(result.msg);
                                    home_kitchen_ta.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    });
                });
            });

            $(document).on('click', 'button.delete_kitchen_button', function(){
                swal({
                  title: LANG.sure,
                  text: LANG.confirm_delete_table,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).data('href');
                        var data = $(this).serialize();

                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            data: data,
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    home_kitchen_ta.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

             
            $(document).on('click', 'button.remove_modifier_product', function(){
                swal({
                  title: LANG.sure,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $(this).closest('tr').remove();
                    }
                });
            });
            
        });
    </script>
@endsection