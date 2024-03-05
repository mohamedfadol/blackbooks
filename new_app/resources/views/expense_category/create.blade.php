<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\ExpenseCategoryController::class, 'store']), 'method' => 'post', 'id' => 'expense_category_add_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'expense.add_expense_category' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'expense.category_name' ) . ':*') !!}
          {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'expense.category_name' )]); !!}
      </div>

       
      @if(!empty($accounts))
				<div class="form-group">
                {!! Form::label('account_parent', __( 'accounting::lang.account_parent' ) . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fas fa-money-bill-alt"></i>
						</span>
						{!! Form::select("account_id", $accounts, 'account_id', ['class' => 'form-control select2', 'required', 'id' => 'main_account', 'style' => 'width:100%;']); !!}
					</div>
				</div>
			@endif
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->