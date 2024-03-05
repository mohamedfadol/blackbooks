@extends('layouts.restaurant_notify')
@section('css')
    <style>
        .no-print  {
            display: none;
        }
        
    </style>
@endsection
@section('title', __( 'restaurant.hole_views' ))

@section('content')
<!-- Main content -->
<section class="content">
<div class="row col-lg-12" style=" ">
    <div class="col-lg-6">
        <img src="{{asset('uploads/business_logos/'.session()->get('business')->logo)}}" style="width=80; float: inline-start;" height="90" alt="{{env('APP_NAME')}}">
    </div>

     
    <div class="col-lg-6">
        <img style="float: left !important;" src="{{asset('img/logo33.PNG')}}" style="width=90; float: inline-start;" height="90" alt="{{env('APP_NAME')}}">
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-center">
        <h1>@lang( 'restaurant.hole_views' ) - @lang( 'restaurant.orders' )</h1>
    </div>
</div>
	<div class="box" style="height: 100%;">
        <div class="box-body">
            <input type="hidden" id="orders_for" value="kitchen">
        	<div class="row" id="orders_div"> 
             @include('restaurant.partials.hole_view_details', array('orders_for' => 'kitchen'))   
            </div>
        </div>
        <div class="overlay hide">
          <i class="fas fa-sync fa-spin"></i>
        </div>
    </div> 
</section>
<!-- /.content -->
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click', 'a.mark_as_cooked_btn', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "info",
                  buttons: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var _this = $(this);
                        var href = _this.data('href');
                        $.ajax({
                            method: "GET",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    _this.closest('.order_div').remove();
                                    location.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });


            $(document).on('click', 'a.back_to_kitchen_btn', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "info",
                  buttons: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var _this = $(this);
                        var href = _this.data('href');
                        $.ajax({
                            method: "GET",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    _this.closest('.order_div').remove();
                                    location.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection