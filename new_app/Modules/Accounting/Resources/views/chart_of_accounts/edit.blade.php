<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\Modules\Accounting\Http\Controllers\CoaController::class, 'update'], $account->id), 
        'method' => 'put', 'id' => 'create_client_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'accounting::lang.edit_account' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name_ar', __( 'user.name_ar' ) . ':*') !!}
                            {!! Form::text('name_ar', $account->name_ar, ['class' => 'form-control', 'required', 'placeholder' => __( 'user.name_ar' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name_en', __( 'user.name_en' ) . ':*') !!}
                            {!! Form::text('name_en', $account->name_en, ['class' => 'form-control', 'placeholder' => __( 'user.name_en' ) ]); !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('account_parent', __( 'accounting::lang.account_parent' ) . ':*') !!}
                    <select class="form-control accounts-dropdown select2"   name="account_parent_id" id="account_sub_type">
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($account_types as $account_type)
                            <option value="{{$account_type->id}}" 
                            data-show_balance="{{$account_type->show_balance}}"
                             @if($account_type->id == $account->parent_id) selected @endif>
                            {{$account_type->name_ar}} {{$account_type->name_en}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    {!! Form::label('financial_statement', __( 'accounting::lang.financial_statement' ) . ':*') !!}
                    <select class="form-control accounts-dropdown select2" name="financial_statement" id="account_sub_type">
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($financialStatements as $financialStatement)
                            <option value="{{$financialStatement->id}}" @if($financialStatement->id == $account->financial_statement_id) selected @endif>{{$financialStatement->name_ar}} {{$financialStatement->name_en}}</option>
                        @endforeach
                         
                    </select>
                </div>

                <div class="form-group">
                    {!! Form::label('account_category', __( 'accounting::lang.account_category' ) . ':*') !!}
                    <select class="form-control accounts-dropdown select2" name="account_category" id="account_sub_type">
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($accountCategories as $accountCategory)
                            <option value="{{$accountCategory->id}}" @if($accountCategory->id == $account->account_category_id) selected @endif>{{$accountCategory->name_ar}} {{$accountCategory->name_en}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    {!! Form::label('account_number', __( 'accounting::lang.account_number' ) . ':') !!}
                    {!! Form::text('account_number', $account->account_number, ['class' => 'form-control', 'placeholder' => __( 'accounting::lang.account_number' ) ]); !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('description', __( 'lang_v1.description' ) . ':') !!}
                    {!! Form::textarea('description', $account->description, ['class' => 'form-control', 
                        'placeholder' => __( 'lang_v1.description' ) ]); !!}
                </div>
            </div>
        </div> 
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->