<style>
table tbody {display: block;max-height: 150px;overflow-y: scroll;}
table thead, table tbody tr {display: table;width: 100%;table-layout: fixed;}
.clock {font-weight: bold;}
.small-box p {font-size: 13px;}
.share-button{display:inline-block;display: flex;justify-content: space-between;}
</style>
@forelse($orders as $order)
	<div class="col-md-3 col-xs-6 order_div">
		<div class="small-box bg-gray">
            <div class="inner">
            	<table class="table no-margin no-border table-slim" style="width: 100%;">
                    <thead style="border: 2px solid red;">
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
            @if($orders_for == 'kitchen')
            	<!-- <a href="#" class="btn btn-flat small-box-footer bg-yellow mark_as_cooked_btn" 
                    data-href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'markAsCooked'], [$order->id])}}">
                        <i class="fa fa-check-square-o"></i>finsh @lang('restaurant.mark_as_cooked')</a> -->
            @elseif($orders_for == 'waiter' && $order->res_order_status != 'served')
            	<a href="#" class="btn btn-flat small-box-footer bg-yellow mark_as_served_btn" 
                    data-href="{{action([\App\Http\Controllers\Restaurant\OrderController::class, 'markAsServed'], [$order->id])}}">
                        <i class="fa fa-check-square-o"></i> @lang('restaurant.mark_as_served')</a>
            @else
            	<div class="small-box-footer bg-gray">&nbsp;</div>
            @endif
            <a href="#" class="btn btn-flat small-box-footer bg-yellow mark_as_served_btn" 
                    data-href="{{action([\App\Http\Controllers\Restaurant\OrderController::class, 'markAsServed'], [$order->id])}}">
                        <i class="fa fa-check-square-o"></i> @lang('restaurant.mark_as_served')</a>


                <div class='share-button'>
                    <a href="#" class="btn btn-sm small-box-footer bg-yellow mark_as_cooked_btn" 
                        data-href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'markAsCooked'], [$order->id])}}">
                            <i class="fa fa-check-square-o"></i> @lang('restaurant.mark_as_cooked')</a>
                    
                    <a href="#" class="btn btn-sm small-box-footer bg-info text-white btn-modal" 
                        data-href="{{ action([\App\Http\Controllers\SellController::class, 'show'], [$order->id])}}" 
                            data-container=".view_modal">@lang('restaurant.order_details') 
                                <i class="fa fa-arrow-circle-right"></i></a>

                    <a href="#" class="btn btn-sm small-box-footer bg-primary back_to_kitchen_btn" 
                        data-href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'backToKitchen'], [$order->id])}}">
                            <i class="fa fa-check-square-o"></i> @lang('restaurant.order_back_to_kitchen')</a>      
                                 
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


 
<script>
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
 