<div class="modal-dialog" role="document">
  <div class="modal-content">

    <?php echo Form::open(['url' => action([\App\Http\Controllers\TransactionPaymentController::class, 'postPayContactDue']), 'method' => 'post', 'id' => 'pay_contact_due_form', 'files' => true ]); ?>


    <?php echo Form::hidden("contact_id", $contact_details->contact_id); ?>

    <?php echo Form::hidden("due_payment_type", $due_payment_type); ?>

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title"><?php echo app('translator')->get( 'purchase.add_payment' ); ?></h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <?php if($due_payment_type == 'purchase'): ?>
        <div class="col-md-6">
          <div class="well">
            <strong><?php echo app('translator')->get('purchase.supplier'); ?>: </strong><?php echo e($contact_details->name, false); ?><br>
            <strong><?php echo app('translator')->get('business.business'); ?>: </strong><?php echo e($contact_details->supplier_business_name, false); ?><br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong><?php echo app('translator')->get('report.total_purchase'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_purchase, false); ?></span><br>
            <strong><?php echo app('translator')->get('contact.total_paid'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_paid, false); ?></span><br>
            <strong><?php echo app('translator')->get('contact.total_purchase_due'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_purchase - $contact_details->total_paid, false); ?></span><br>
             <?php if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00'): ?>
                  <strong><?php echo app('translator')->get('lang_v1.opening_balance'); ?>: </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  <?php echo e($contact_details->opening_balance, false); ?></span><br>
                  <strong><?php echo app('translator')->get('lang_v1.opening_balance_due'); ?>: </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  <?php echo e($ob_due, false); ?></span>
              <?php endif; ?>
          </div>
        </div>
        <?php elseif($due_payment_type == 'purchase_return'): ?>
        <div class="col-md-6">
          <div class="well">
            <strong><?php echo app('translator')->get('purchase.supplier'); ?>: </strong><?php echo e($contact_details->name, false); ?><br>
            <strong><?php echo app('translator')->get('business.business'); ?>: </strong><?php echo e($contact_details->supplier_business_name, false); ?><br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong><?php echo app('translator')->get('lang_v1.total_purchase_return'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_purchase_return, false); ?></span><br>
            <strong><?php echo app('translator')->get('lang_v1.total_purchase_return_paid'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_return_paid, false); ?></span><br>
            <strong><?php echo app('translator')->get('lang_v1.total_purchase_return_due'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_purchase_return - $contact_details->total_return_paid, false); ?></span>
          </div>
        </div>
        <?php elseif(in_array($due_payment_type, ['sell'])): ?>
          <div class="col-md-6">
            <div class="well">
              <strong><?php echo app('translator')->get('sale.customer_name'); ?>: </strong><?php echo e($contact_details->name, false); ?><br>
              <br><br>
            </div>
          </div>
          <div class="col-md-6">
            <div class="well">
              <strong><?php echo app('translator')->get('report.total_sell'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_invoice, false); ?></span><br>
              <strong><?php echo app('translator')->get('contact.total_paid'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_paid, false); ?></span><br>
              <strong><?php echo app('translator')->get('contact.total_sale_due'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_invoice - $contact_details->total_paid, false); ?></span><br>
              <?php if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00'): ?>
                  <strong><?php echo app('translator')->get('lang_v1.opening_balance'); ?>: </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  <?php echo e($contact_details->opening_balance, false); ?></span><br>
                  <strong><?php echo app('translator')->get('lang_v1.opening_balance_due'); ?>: </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  <?php echo e($ob_due, false); ?></span>
              <?php endif; ?>
            </div>
          </div>
         <?php elseif(in_array($due_payment_type, ['sell_return'])): ?>
         <div class="col-md-6">
          <div class="well">
            <strong><?php echo app('translator')->get('sale.customer_name'); ?>: </strong><?php echo e($contact_details->name, false); ?><br>
              <br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong><?php echo app('translator')->get('lang_v1.total_sell_return'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_sell_return, false); ?></span><br>
            <strong><?php echo app('translator')->get('lang_v1.total_sell_return_paid'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_return_paid, false); ?></span><br>
            <strong><?php echo app('translator')->get('lang_v1.total_sell_return_due'); ?>: </strong><span class="display_currency" data-currency_symbol="true"><?php echo e($contact_details->total_sell_return - $contact_details->total_return_paid, false); ?></span>
          </div>
        </div>
        <?php endif; ?>
      </div>
        <?php if(config('constants.show_payment_type_on_contact_pay') && ($due_payment_type == 'purchase' || $due_payment_type == 'sell')): ?>
            <?php
                $reverse_payment_types = [];

                if($due_payment_type == 'purchase') {
                    $reverse_payment_types = [
                        0 => __('lang_v1.pay_to_supplier'),
                        1 => __('lang_v1.receive_from_supplier')
                    ];
                } else if($due_payment_type == 'sell') {
                    $reverse_payment_types = [
                        0 => __('lang_v1.receive_from_customer'),
                        1 => __('lang_v1.pay_to_customer')
                    ];
                }
            ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?php echo Form::label("is_reverse" , __('lang_v1.payment_type') . ':'); ?>

                        <?php echo Form::select("is_reverse", $reverse_payment_types, 0, ['class' => 'form-control select2', 'style' => 'width:100%;']); ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>
      <div class="row payment_row">
        <div class="col-md-4">
          <div class="form-group">
            <?php echo Form::label("method" , __('purchase.payment_method') . ':*'); ?>

            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              <?php echo Form::select("method", $payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']); ?>

            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <?php echo Form::label("paid_on" , __('lang_v1.paid_on') . ':*'); ?>

            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              <?php echo Form::text('paid_on', \Carbon::createFromTimestamp(strtotime($payment_line->paid_on))->format(session('business.date_format') . ' ' . 'h:i A'), ['class' => 'form-control', 'readonly', 'required']); ?>

            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <?php echo Form::label("amount" , __('sale.amount') . ':*'); ?>

            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              <?php if(in_array($due_payment_type, ['sell_return', 'purchase_return'])): ?>
              <?php echo Form::text("amount", number_format($payment_line->amount, session('business.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']), ['class' => 'form-control input_number payment_amount', 'required', 'placeholder' => __('sale.amount'), 'data-rule-max-value' => $payment_line->amount, 'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated])]); ?>

              <?php else: ?>
                <?php echo Form::text("amount", number_format($payment_line->amount, session('business.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']), ['class' => 'form-control input_number payment_amount', 'required', 'placeholder' => __('sale.amount')]); ?>

              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php
            $pos_settings = !empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];

            $enable_cash_denomination_for_payment_methods = !empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
        ?>

        <?php if(!empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens'): ?>
            <input type="hidden" class="enable_cash_denomination_for_payment_methods" value="<?php echo e(json_encode($pos_settings['enable_cash_denomination_for_payment_methods']), false); ?>">
            <div class="clearfix"></div>
            <div class="col-md-12 cash_denomination_div <?php if(!in_array($payment_line->method, $enable_cash_denomination_for_payment_methods)): ?> hide <?php endif; ?>">
                <hr>
                <strong><?php echo app('translator')->get( 'lang_v1.cash_denominations' ); ?></strong>
                  <?php if(!empty($pos_settings['cash_denominations'])): ?>
                    <table class="table table-slim">
                      <thead>
                        <tr>
                          <th width="20%" class="text-right"><?php echo app('translator')->get('lang_v1.denomination'); ?></th>
                          <th width="20%">&nbsp;</th>
                          <th width="20%" class="text-center"><?php echo app('translator')->get('lang_v1.count'); ?></th>
                          <th width="20%">&nbsp;</th>
                          <th width="20%" class="text-left"><?php echo app('translator')->get('sale.subtotal'); ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $__currentLoopData = explode(',', $pos_settings['cash_denominations']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dnm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                          <td class="text-right"><?php echo e($dnm, false); ?></td>
                          <td class="text-center" >X</td>
                          <td><?php echo Form::number("denominations[$dnm]", null, ['class' => 'form-control cash_denomination input-sm', 'min' => 0, 'data-denomination' => $dnm, 'style' => 'width: 100px; margin:auto;' ]); ?></td>
                          <td class="text-center">=</td>
                          <td class="text-left">
                            <span class="denomination_subtotal">0</span>
                          </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="4" class="text-center"><?php echo app('translator')->get('sale.total'); ?></th>
                          <td>
                            <span class="denomination_total">0</span>
                            <input type="hidden" class="denomination_total_amount" value="0">
                            <input type="hidden" class="is_strict" value="<?php echo e($pos_settings['cash_denomination_strict_check'] ?? '', false); ?>">
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                    <p class="cash_denomination_error error hide"><?php echo app('translator')->get('lang_v1.cash_denomination_error'); ?></p>
                  <?php else: ?>
                    <p class="help-block"><?php echo app('translator')->get('lang_v1.denomination_add_help_text'); ?></p>
                  <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="clearfix"></div>
        <div class="col-md-4">
          <div class="form-group">
            <?php echo Form::label('document', __('purchase.attach_document') . ':'); ?>

            <?php echo Form::file('document', ['accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); ?>

            <p class="help-block">
            <?php if ($__env->exists('components.document_help_text')) echo $__env->make('components.document_help_text', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></p>
          </div>
        </div>
        <?php if(!empty($accounts)): ?>
          <div class="col-md-6">
            <div class="form-group">
              <?php echo Form::label("account_id" , __('lang_v1.payment_account') . ':'); ?>

              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fas fa-money-bill-alt"></i>
                </span>
                <?php echo Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;']); ?>

              </div>
            </div>
          </div>
        <?php endif; ?>
        <div class="clearfix"></div>

          <?php echo $__env->make('transaction_payment.payment_type_details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="col-md-12">
          <div class="form-group">
            <?php echo Form::label("note", __('lang_v1.payment_note') . ':'); ?>

            <?php echo Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]); ?>

          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary"><?php echo app('translator')->get( 'messages.save' ); ?></button>
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo app('translator')->get( 'messages.close' ); ?></button>
    </div>

    <?php echo Form::close(); ?>


  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog --><?php /**PATH C:\xampp\htdocs\pos\resources\views/transaction_payment/pay_supplier_due_modal.blade.php ENDPATH**/ ?>