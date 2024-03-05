<style>
table tbody {display: block;max-height: 150px;overflow-y: scroll;}
table thead, table tbody tr {display: table;width: 100%;table-layout: fixed;}
.clock {font-weight: bold;}
.small-box p {font-size: 13px;}
.share-button{display:inline-block;display: flex;justify-content: space-between;}
.order-status {border: 2px solid none;}
.order-status-pending {border: 2px solid red;}
.order-status-servied {border: 2px solid green;}

.navbar {
  position: relative;
  overflow: hidden;
  min-height: 5px;
}

.navbar span {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 2px;
  width: 100px;
  background-color: red;
  animation: animate 6s linear infinite;
}

@keyframes animate {0% {left: 0;transform: translate(-30%);}50% {left: 0;transform: translate(-50%);}75% {left: 0;transform: translate(-75%);}100% {left: 100%;transform: translate(0);}}
</style>
@forelse($orders as $order)

        @php
            $count_sell_line = count($order->sell_lines);
            $count_cooked = count($order->sell_lines->where('res_line_order_status', 'cooked'));
            $count_served = count($order->sell_lines->where('res_line_order_status', 'served'));
            $order_status =  'received';
            if($count_cooked == $count_sell_line) {
                    $order_status =  'cooked';
            } else if($count_served == $count_sell_line) {
                    $order_status =  'served';
            } else if ($count_served > 0 && $count_served < $count_sell_line) {
                    $order_status =  'partial_served';
            } else if ($count_cooked > 0 && $count_cooked < $count_sell_line) {
                    $order_status =  'partial_cooked';
            }
            
        @endphp
	<div class="col-md-3 col-xs-6 order_div">
		<div class="small-box bg-gray">
            <div class="inner">
            	<table class="table no-margin no-border table-slim" style="width: 100%;">
                    <thead class=" {{$order_status  == 'served' ? "order-status-servied" : "order-status-pending" }}" >
                    @if($order_status  != 'served')
                    <nav class="navbar"><span></span></nav>
                    @endif
                             
                        <tr>
                            <td>{{ __('restaurant.table_no') }}{{ $order->table_name }} </td>
                            <td>#{{$order->invoice_no}}</td>
                            <td> <div class="clock"><p class="clock-time"></p></div> </td>
                        </tr>
                    </thead>
                    <thead>
                        <tr @if(empty($for_ledger)) class="bg-green" @endif>
                            <th>{{ __('sale.product') }}</th>
                            <th>{{ __('sale.qty') }}</th>
                            <th>{{ __('restaurant.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->sell_lines as $sell_line)
                            <tr> 
                                <td>{{ $sell_line->product->name }}</td>
                                <td>{{ $sell_line->quantity }}</td>
                                <td>{{ $sell_line->sell_line_note }}</td>
                            </tr>
                        @endforeach
                    </tbody>
            	</table> 
            </div>
                <div class='share-button'>
                    <a href="#" class="btn btn-sm small-box-footer bg-yellow mark_as_cooked_btn" 
                        data-href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'markAsCooked'], [$order->id])}}">
                            <i class="fa fa-check-square-o"></i> @lang('restaurant.mark_as_cooked')</a>
                    
                    <a href="#" class="btn btn-sm small-box-footer bg-info text-white btn-modal" 
                        data-href="{{ action([\App\Http\Controllers\SellController::class, 'show'], [$order->id])}}" 
                            data-container=".view_modal">@lang('restaurant.order_details') 
                                <i class="fa fa-arrow-circle-right"></i></a>    
                    @if ($order_status  != 'served')    
                        <a href="#" class="btn btn-sm small-box-footer bg-primary mark_as_served_btn" 
                            data-href="{{action([\App\Http\Controllers\Restaurant\OrderController::class, 'markAsServed'], [$order->id])}}">
                                <i class="fa fa-check-square-o"></i> @lang('restaurant.order_as_served')</a> 
                    @endif
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
<div class="col-md-12">
	<h4 class="text-center">@lang('restaurant.no_orders_found')</h4>
</div>
@endforelse


 
<!-- <script>
    function updateClockTime() {
        // Get all elements with the class "clock-time"
        const clockElements = document.querySelectorAll('.clock-time');
        // Update the time for each element
        clockElements.forEach((element) => {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            element.textContent = timeString;
        });
    }
    // Update the clock time every second
    setInterval(updateClockTime, 1000);
    // Call the function immediately to set the initial time
    updateClockTime();
</script>
  -->