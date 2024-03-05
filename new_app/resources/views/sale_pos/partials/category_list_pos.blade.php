@if(!empty($categories))
		<!-- <div class="col-md-4" id="product_category_div">
			<select class="select2" id="product_category" style="width:100% !important">

				<option value="all">@lang('lang_v1.all_category')</option>

				@foreach($categories as $category)
					<option value="{{$category['id']}}">{{$category['name']}}</option>
				@endforeach

				@foreach($categories as $category)
					@if(!empty($category['sub_categories']))
						<optgroup label="{{$category['name']}}">
							@foreach($category['sub_categories'] as $sc)
								<i class="fa fa-minus"></i> <option value="{{$sc['id']}}">{{$sc['name']}}</option>
							@endforeach
						</optgroup>
					@endif
				@endforeach
			</select>
		</div> -->
	<div  id="product_category_div">
		<div class="category-container">
			<ul class="category-list">
				<button type="button" style="margin: 2px;" data-category="all" class="product_category">
					<li value="all">@lang('lang_v1.all_category')</li>
				</button>
				@foreach($categories as $category)
					<button type="button"  style="margin: 2px;" data-category="{{$category['id']}}" class="product_category">
						<li>{{$category['name']}}</li>
					</button> 
					@if(!empty($category['sub_categories']))
						@foreach($category['sub_categories'] as $sc)
						<button type="button" style="margin: 2px;" data-category="{{$sc['id']}}" class="product_category">
							<li>-- {{$sc['name']}}</li> 
						</button>
						@endforeach
					@endif
				@endforeach
				<!-- @foreach($categories as $category)
					@if(!empty($category['sub_categories']))
						@foreach($category['sub_categories'] as $sc)
						<button style="margin: 2px;" data-category="{{$sc['id']}}" class="product_category">
							<li>-- {{$sc['name']}}</li> 
						</button>
						@endforeach
					@endif
				@endforeach -->
			
			</ul>
		</div>
	</div>
	@endif