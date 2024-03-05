<style>
table tbody {display: block;max-height: 150px;overflow-y: scroll;}
table thead, table tbody tr {display: table;width: 100%;table-layout: fixed;}
.clock {font-weight: bold; background-color: #8B8DE7; padding: 5px; font-size: 20px}
.small-box p {font-size: 12px;font-size: 12px;text-wrap: nowrap;display: inline;padding: 5px;background-color: lightskyblue;border-radius: 3px;}
.small-box .ready{background-color: #60c9a6;}
.share-button{padding: 5px;}
.vl{border-left: 2px solid black;height: -webkit-fill-available;position: absolute;left: 50%;top: 0;} 
</style>

<div class="row">
    <div class="col-lg-6 col-md-4 col-xs-6 order_div">
        <h1 class="ready_order text-center">
            @lang('restaurant.ready_orders')
        </h1>
        <hr style="border-bottom: 2px solid black; ">
        @forelse($orders as $order)
            <div class="col-md-3 col-xs-3">
                <div class="small-box bg-gray">
                    <div class="inner">
                    <table>
                            <thead>
                            <tr class="text-center clock-time">
                                <td class="clock"> #{{$order->invoice_no}} </td>
                            </tr>
                            </thead>
                        </table>
                    </div> 
                </div>
            </div>
            @if($loop->iteration % 4 == 0)
                <div class="hidden-xs">
                    <div class="clearfix"></div>
                </div>
            @endif
            @if($loop->iteration % 2 == 0)
                <div class="visible-xs">
                    <div class="clearfix"></div>
                </div>
            @endif
        @empty
        @endforelse
    </div>

    <div class="vl"></div>

    <div class="col-lg-6 col-md-4 col-xs-6 order_div">
        <h1 class="ready_order text-center">@lang('restaurant.orders_are_being_prepared')</h1>
        <hr style="border-bottom: 2px solid black; ">
        @forelse($resrvedOrders as $order)
            <div class="col-md-3 col-xs-3">
                <div class="small-box bg-gray">
                    <div class="inner">
                        <table>
                            <thead>
                            <tr class="text-center clock-time ">
                                <td class="clock">  #{{$order->invoice_no}} </td>
                            </tr>
                            </thead>
                        </table>
                    </div> 
                </div>
            </div>
            @if($loop->iteration % 4 == 0)
                <div class="hidden-xs">
                    <div class="clearfix"></div>
                </div>
            @endif
            @if($loop->iteration % 2 == 0)
                <div class="visible-xs">
                    <div class="clearfix"></div>
                </div>
            @endif
        @empty
        @endforelse
    </div>
</div>



 

 