<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action([\App\Http\Controllers\FinancialStatementController::class,  'update'], $financial_statement->id), 'method' => 'put', 'id' => 'financial_statement_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_financial_statement' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-md-12">
          {!! Form::label('name_ar', __( 'lang_v1.name_ar' ) . ':*') !!}
            {!! Form::text('name_ar', $financial_statement->name_ar, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name_ar' )]); !!}
        </div>

        <div class="form-group col-md-12">
          {!! Form::label('name_en', __( 'lang_v1.name_en' ) . ':') !!}
            {!! Form::text('name_en', $financial_statement->name_en, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.name_en' )]); !!}
        </div>
      </div>
    </div>

    <div class="form-group">
        {!! Form::label('acc_category_parent', __( 'account.acc_category_parent' ) .":") !!}
        <select name="parent_id" class="form-control select2">
            <option>@lang('messages.please_select')</option>
            @foreach($financial_statements as $financial_statem)
                    <option value="{{$financial_statem->id}}" @if($financial_statement->parent_id == $financial_statem->id) selected @endif >{{$financial_statem->name_ar}} - {{$financial_statem->name_en}}</option>
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