<style>
table tbody {display: block;}
table thead, table tbody tr {display: table;width: 100%;table-layout: fixed;}
.clock {font-weight: bold;}
.small-box p {font-size: 13px;}
.share-button{display:inline-block;display: flex;justify-content: space-between;}
.order-status {border: 2px solid none;}
.order-status-pending {border: 2px solid red;}
.order-status-servied {border: 2px solid green;}
.light-gray{background-color: #6d97ea!important;}
.white {color: #fff;}
.light-orang {color: #000;background-color: #c4bc6c!important;}
.new {
    padding: 5px;
    background-color: #d73434;
    border-radius: 16px;
    border: 1px dashed;
}
.t-number {
    padding: 5px;
    background-color: #5EA85E;
    border-radius: 13px;
    border: 1px dashed;
}
.heart {width: 20px;animation: animateHeart 1.2s infinite;}
@keyframes animateHeart {0% {transform: scale(0.5); }50% {transform: scale(0.5);}100% {transform: scale(1);}}

</style>

 
    @forelse($orders as $index => $order)
        @php
            if ($order->sell_lines->where('kitchen_id',$kitchen->id)->where('res_line_order_status','!=','cooked')->count() > 0) {
        @endphp
        @php 
            $kitchen_id = $kitchen->id;
            $status =  $order->lineDetails?->where('kitchen_id', $kitchen->id)->where('transaction_id', $order->id)->first()->status ?? null;
            
            $count_sell_line = count($order->sell_lines);
            $count_cooked = count($order->sell_lines->where('res_line_order_status', 'cooked'));
            $count_served = count($order->sell_lines->where('res_line_order_status', 'served'));
            $count_returned = count($order->sell_lines->where('res_line_order_status', 'returned'));
            $count_null = count($order->sell_lines->where('res_line_order_status', null));
            $order_status =  $count_null;
            if($count_cooked == $count_sell_line) {
                    $order_status =  'cooked';
            } else if($count_served == $count_sell_line) {
                    $order_status =  'served';
            } else if ($count_served > 0 && $count_served < $count_sell_line) {
                    $order_status =  'partial_served';
            } else if ($count_returned) {
                    $order_status =  'returned';
            }
            else if ($count_null) {
                $order_status =  'new';
            }
        @endphp
        
        <div class="col-md-3 col-xs-6 order_div" >
            <div class="small-box 
                @if($status == 'returned') light-orang white @endif 
                    @if($status == 'served') bg-gray @else light-gray white @endif ">
                <div class="inner text-center" style="text-align: -webkit-center;">
                    @if($status  == null)
                        <div class="heart">
                            <span class="new white">@lang('restaurant.new')</span> 
                        </div>
                    @endif
                    @if($status  == 'returned') 
                        <div class="heart">
                            <span class="new white">@lang('restaurant.returned')</span> 
                        </div>
                    @endif
                <table class="table no-margin table-bordered table-slim" style="width: 100%;">
                    <thead class=" {{$status  == 'served' ? "order-status-servied" : "order-status-pending" }}" >
                        <tr>
                            <td>{{ __('restaurant.table_no') }}  <span @if(!empty($order->table_name)) class="t-number" @endif >{{ $order->table_name }}</span> </td>
                            <td>#{{$order->invoice_no}}</td>
                            <td class="text-center"> 
                            <div id="countdown_<?php echo $index; ?>">Loading...</div> 
                            </td>
                        </tr>
                    </thead>
                    <thead>
                        <tr @if(empty($for_ledger)) class="bg-green" @endif>
                            <th>{{ __('sale.product') }}</th>
                            <th>{{ __('sale.qty') }}</th>
                            <th>{{ __('restaurant.notes') }}</th>
                            @if ($order_status == 'new')
                            @else
                                <th>{{ __('restaurant.status') }} </th> 
                            @endif
                            
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->sell_lines->whereNull('parent_sell_line_id') as $sell_line)
                            <tr> 
                                <td>{{ $sell_line->product->name }}</td>
                                <td>{{ $sell_line->quantity }}</td>
                                 <td>
                                    {{ $sell_line->sell_line_note }} ,   
                                    @if(!empty($sell_line->modifiers))
                                        @foreach($sell_line->modifiers as $modifier)
                                            {{ $modifier->variations->name ?? ''}} &nbsp;|                                        
                                        @endforeach
                                    @endif
                                 </td>
                                @if ($status != null)
                                    <td style="padding: 2px;">
                                        @if ($sell_line->res_line_order_status == "cooked")
                                        <a href="#" class="btn btn-sm small-box-footer btn-danger white mark_as_cooked_btn_not" 
                                                data-href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'markOrderCompleteDone'], [$sell_line->id])}}">
                                                    <i class="fa fa-check white"></i> @lang('restaurant.not_done')</a>
                                        @else
                                        <a href="#" class="btn btn-sm small-box-footer bg-info white mark_as_cooked_btn" 
                                                    data-href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'markAsCooked'], [$sell_line->id])}}">
                                                        <i class="fa fa-check white"></i> @lang('restaurant.done')</a>
                                        @endif
                                    </td>
                                @else
                                @endif
                            </tr> 
                            @empty 
                        @endforelse 
                    </tbody>
                </table>  
                </div>
                    <div class='share-button'>
                        @if ($status == null)     
                            <a href="#" class="btn btn-sm small-box-footer bg-primary mark_as_served_btn" 
                                data-href="{{action([\App\Http\Controllers\Restaurant\OrderController::class, 'markAsServed'], [$order->id,$kitchen_id])}}">
                                    <i class="fa fa-check"></i> @lang('restaurant.order_as_served')</a> 
                        @else 
                        <a href="#" class="btn btn-sm small-box-footer bg-info white mark_as_all_cooked_btn" 
                                data-href="{{action([\App\Http\Controllers\Restaurant\OrderController::class, 'markAsAllCooked'], [$order->id,$kitchen_id])}}">
                                    <i class="fa fa-check"></i> @lang('restaurant.mark_as_cooked')</a>
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

        @php
         }
        @endphp
    @empty
    <div class="col-md-12">
        <h4 class="text-center">@lang('restaurant.no_orders_found')</h4>
    </div>
    @endforelse
 
    <script>
    <?php foreach ($orders as $index => $order): ?>
        var laravelCreatedAt_<?php echo $index; ?> = new Date("{{ $order->created_at->toIso8601String() }}");
        var countdownInterval_<?php echo $index; ?> = setInterval(function () {
            // Get the current system time
            var now = new Date();
            // Calculate the difference in milliseconds
            var difference = now - laravelCreatedAt_<?php echo $index; ?>;
            // Convert milliseconds to seconds and minutes
            var seconds = Math.floor((difference / 1000) % 60);
            var minutes = Math.floor((difference / (1000 * 60)) % 60);
            document.getElementById('countdown_<?php echo $index; ?>').innerHTML = minutes + 'm ' + seconds + 's';
        }, 1000);
    <?php endforeach; ?>
</script>

 
