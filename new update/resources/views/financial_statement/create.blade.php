<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action([\App\Http\Controllers\FinancialStatementController::class, 'store']), 'method' => 'post', 'id' => 'financial_statement_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_financial_statement' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-md-12">
          {!! Form::label('name_ar', __( 'lang_v1.name_ar' ) . ':*') !!}
            {!! Form::text('name_ar', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name_ar' )]); !!}
        </div>

        <div class="form-group col-md-12">
          {!! Form::label('name_en', __( 'lang_v1.name_en' ) . ':') !!}
            {!! Form::text('name_en', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.name_en' )]); !!}
        </div>
      </div>
    </div>

    <div class="form-group">
        {!! Form::label('parent_id', __( 'lang_v1.parent_id' ) .":") !!}
        <select name="parent_id" class="form-control select2">
            <option>@lang('messages.please_select')</option>
            @foreach($financial_statements as $financial_state)
                    <option value="{{$financial_state->id}}">{{$financial_state->name_ar}} - {{$financial_state->name_en}}</option>
            @endforeach
        </select>
    </div>


    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
    {!! Form::close() !!}
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->