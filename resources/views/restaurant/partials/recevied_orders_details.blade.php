<style>
table tbody {}
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
@forelse($orders as $index => $order)

        @php
            $status =  $order->lineDetails->where('transaction_id', $order->id)->first()->status ?? null;
        @endphp
	<div class="col-md-3 col-xs-6 order_div">
		<div class="small-box bg-gray">
            <div class="inner">
            	<table class="table no-margin table-bordered table-slim" style="width: 100%;">
                    <thead>
                        
                        <tr>
                            <td>{{ __('restaurant.table_no') }}{{ $order->table_name }} </td>
                            <td>#{{$order->invoice_no}}</td>
                            <td>OR{{$order->id}}</td>
                        </tr>
                    </thead>
                    <thead> 
                        <tr @if(empty($for_ledger)) class="bg-green" @endif>
                            <th>{{ __('sale.product') }}</th>
                            <th>{{ __('sale.qty') }}</th>
                            <th>{{ __('restaurant.notes') }}</th>
                        <th>{{ __('restaurant.status') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->sell_lines->whereNull('parent_sell_line_id') as $sell_line)
                            <tr> 
                                <td>{{ $sell_line->product->name }}</td>
                                <td>{{ $sell_line->quantity }}</td>
                                <td>
                                    {{ $sell_line->sell_line_note }}  ,

                                    @if(!empty($sell_line->modifiers))
                                        @foreach($sell_line->modifiers as $modifier)
                                            {{ $modifier->variations->name ?? ''}} &nbsp;|                                        
                                        @endforeach
                                    @endif
                                </td>
                                <td style="padding: 2px;">
                                    @if ($sell_line->res_line_order_status  != 'cooked' && $sell_line->res_line_order_status  != 'done')     
                                        <span class="btn btn-danger" style="padding: 2px;" ><i class="fa fa-check"></i> @lang('restaurant.not_done')</span>
                                    @else
                                        <span class="btn btn-primary" style="padding: 2px;" ><i class="fa fa-check"></i> @lang('restaurant.done')</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
            	</table> 
            </div>  
                @if($status == "done")
                <div class='share-button'>
                    <a href="#" class="btn btn-sm small-box-footer bg-primary mark_as_received_btn" 
                        data-href="{{action([\App\Http\Controllers\Restaurant\OrderController::class, 'markAsReceived'], [$order->id])}}">
                            <i class="fa fa-check"></i> @lang('restaurant.order_as_received')</a> 
                    
                    <a href="#" class="btn btn-sm small-box-footer bg-yellow back_to_kitchen_btn" 
                        data-href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'backToKitchen'], [$order->id])}}">
                            <i class="fa fa-check"></i> @lang('restaurant.order_back_to_kitchen')</a>
                     
                    <a href="#" class="btn btn-sm small-box-footer bg-info text-white btn-modal" 
                        data-href="{{ action([\App\Http\Controllers\SellController::class, 'show'], [$order->id])}}" 
                            data-container=".view_modal">@lang('restaurant.order_details') 
                                <i class="fa fa-arrow-circle-right"></i></a>  
                </div> 
                @endif
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


