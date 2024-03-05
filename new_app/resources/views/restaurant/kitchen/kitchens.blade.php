<style>
    .text-center{
        text-align: center;
    }
    .small-box{
        padding: 6px;
        box-shadow: 0 1px 8px 4px rgb(9 9 9 / 10%);
    }
</style>
@forelse($kitchens as $kitchen)
	<div class="col-md-3 col-xs-6 order_div">
		<div class="small-box">
            <a href="{{route('kitchen.show',$kitchen->id)}}">
                <div class="inner text-center">
                    <div @if(empty($for_ledger)) class="bg-green  text-center" @endif style="padding: 5px;">{{ __('restaurant.kitchen_name') }}</div>
                    <span  style="padding: 20px;">{{ $kitchen->name }}   {{$kitchen->sell_lines
                                                                            ->where('res_line_order_status','!=','cooked')
                                                                            ->where('res_line_order_status','!=','delivered')
                                                                            ->where('res_line_order_status','!=','done')->count()
                                                                            }}</span> 
                </div>
            </a>
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
	<h4 class="text-center">@lang('restaurant.no_kitchen_found')</h4>
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
 