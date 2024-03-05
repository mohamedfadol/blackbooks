<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\GlobalCurrencyController::class, 'update'], [$galobal_currency->id]), 'method' => 'PUT', 'id' => 'galobal_currencies_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'tax_rate.edit_galobal_currency' )</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            {!! Form::label('name', __( 'lang_v1.global_currency_name' ) . ':*') !!}
            {!! Form::text('global_currency_name', $galobal_currency->global_currency_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.global_currency_name' )]); !!}
        </div>

        <div class="form-group">
            {!! Form::label('global_currency_value', __( 'lang_v1.global_currency_value' ) . ':*') !!} @show_tooltip(__('lang_v1.global_currency_exempt_help'))
            {!! Form::text('global_currency_value', $galobal_currency->global_currency_value, ['class' => 'form-control ', 'required' , 'placeholder' => __( 'lang_v1.global_currency_value' )]); !!}
        </div>
<!-- 
        <div class="form-group">
            {!! Form::label('name', __( 'lang_v1.local_currency_name' ) . ':*') !!}
            {!! Form::text('local_currency_name', $galobal_currency->local_currency_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.local_currency_name' )]); !!}
        </div>

        <div class="form-group">
            {!! Form::label('local_currency_value', __( 'lang_v1.local_currency_value' ) . ':*') !!} @show_tooltip(__('lang_v1.local_currency_exempt_help'))
            {!! Form::text('local_currency_value', $galobal_currency->local_currency_value, ['class' => 'form-control ', 'required' , 'placeholder' => __( 'lang_v1.local_currency_value' )]); !!}
        </div> -->
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->