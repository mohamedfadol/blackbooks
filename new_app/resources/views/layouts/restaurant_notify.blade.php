@inject('request', 'Illuminate\Http\Request')

@php
    $pos_layout = true;
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ Session::get('business.name') }}</title> 

        @include('layouts.partials.css')

        @yield('css')
        @yield('js')

        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;
            var pusher = new Pusher('be6bac61ce5c670ed4b2', {cluster: 'eu'});
            var channel = pusher.subscribe('my-channel');
            channel.bind('my-event', function(data) {
            alert(JSON.stringify(data));
            });
        </script>
    </head>

    <body class="hold-transition lockscreen">
        <div class="wrapper">
            <script type="text/javascript">
                if(localStorage.getItem("upos_sidebar_collapse") == 'true'){
                    var body = document.getElementsByTagName("body")[0];
                    body.className += " sidebar-collapse";
                }
            </script>
        
        
        <audio id="newOrder">
            <source src="{{asset('audio/ordercome.mp3')}}" type="audio/mpeg">
        </audio>

        <script src="{{asset('js/notifications.js')}}"></script>
        <!-- Modal -->
        <div class="modal fade notify" id="notifyModel" tabindex="-1" role="dialog" aria-labelledby="notifyModelLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="notifyModelLabel">{{ __('restaurant.new_order_coming') }}</h5>
                </div>
                <div class="modal-body">
                    <!-- <input name="mo" id="mo" class="form-control"> -->
                </div>
                <div class="modal-footer">
                    <button type="button" onClick="window.location.href=window.location.href" class="btn btn-primary btn-block">Ok</button>
                </div>
                </div>
            </div>
        </div>


            <!-- Content Wrapper. Contains page content -->
            <div class="container-fluid">
             @include('layouts.partials.header-restaurant')

                <!-- Add currency related field-->
                <input type="hidden" id="__code" value="{{session('currency')['code']}}">
                <input type="hidden" id="__symbol" value="{{session('currency')['symbol']}}">
                <input type="hidden" id="__thousand" value="{{session('currency')['thousand_separator']}}">
                <input type="hidden" id="__decimal" value="{{session('currency')['decimal_separator']}}">
                <input type="hidden" id="__symbol_placement" value="{{session('business.currency_symbol_placement')}}">

                <input type="hidden" id="__orders_refresh_interval" value="{{config('constants.orders_refresh_interval', 600)}}">
                <!-- End of currency related field-->

                @if (session('status'))
                    <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
                @endif
                @yield('content')
                @if(config('constants.iraqi_selling_price_adjustment'))
                    <input type="hidden" id="iraqi_selling_price_adjustment">
                @endif

                <!-- This will be printed -->
                <section class="invoice print_section" id="receipt_section">
                </section>
                
            </div>
            @include('home.todays_profit_modal')
            <!-- /.content-wrapper -->

            @include('layouts.partials.footer-restaurant')

        </div>

        @include('layouts.partials.javascripts')
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
        <div class="modal fade view_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>

        

    </body>
</html>