<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\GlobalCurrencyController::class, 'store']), 'method' => 'post', 'id' => 'galobal_currencies_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_galobal_currencies' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'lang_v1.global_currency_name' ) . ':*') !!}
          {!! Form::text('global_currency_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.global_currency_name' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('global_currency_value', __( 'lang_v1.global_currency_value' ) . ':*') !!} @show_tooltip(__('lang_v1.global_currency_exempt_help'))
          {!! Form::text('global_currency_value', null, ['class' => 'form-control ', 'required' , 'placeholder' => __( 'lang_v1.global_currency_value' )]); !!}
      </div>

      <!-- <div class="form-group">
        {!! Form::label('name', __( 'lang_v1.local_currency_name' ) . ':*') !!}
          {!! Form::text('local_currency_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.local_currency_name' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('local_currency_value', __( 'lang_v1.local_currency_value' ) . ':*') !!} @show_tooltip(__('lang_v1.local_currency_exempt_help'))
          {!! Form::text('local_currency_value', null, ['class' => 'form-control ', 'required' , 'placeholder' => __( 'lang_v1.local_currency_value' )]); !!}
      </div> -->
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->