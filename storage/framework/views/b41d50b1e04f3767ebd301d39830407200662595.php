

<?php $__env->startSection('title', __('accounting::lang.journal_entry')); ?>

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('accounting::layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><?php echo app('translator')->get( 'accounting::lang.journal_entry' ); ?></h1>
</section>
<section class="content">

<?php echo Form::open(['url' => action([\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'store']), 
    'method' => 'post', 'id' => 'journal_add_form']); ?>

 
	<?php $__env->startComponent('components.widget', ['class' => 'box-primary']); ?>
         
        <input type="hidden" name="type" value="<?php echo e(request()->query('type'), false); ?>">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <?php echo Form::label('ref_no', __('purchase.ref_no').':'); ?>

                    <?php
                if(session('business.enable_tooltip')){
                    echo '<i class="fa fa-info-circle text-info hover-q no-print " aria-hidden="true" 
                    data-container="body" data-toggle="popover" data-placement="auto bottom" 
                    data-content="' . __('lang_v1.leave_empty_to_autogenerate') . '" data-html="true" data-trigger="hover"></i>';
                }
                ?>
                    <?php echo Form::text('ref_no', null, ['class' => 'form-control']); ?>

                </div>
            </div>

            <div class="col-sm-3">
				<div class="form-group">
					<?php echo Form::label('journal_date', __('accounting::lang.journal_date') . ':*'); ?>

					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						<?php echo Form::text('journal_date', \Carbon::createFromTimestamp(strtotime('now'))->format(session('business.date_format') . ' ' . 'h:i A'), ['class' => 'form-control datetimepicker', 'readonly', 'required']); ?>

					</div>
				</div>
			</div>

        </div>

        <!-- <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?php echo Form::label('note', __('lang_v1.additional_notes')); ?>

                    <?php echo Form::textarea('note', null, ['class' => 'form-control', 'rows' => 3]); ?>

                </div>
            </div>
        </div> -->

        <div class="row">
            <div class="col-sm-12">

            <table class="table table-bordered table-striped hide-footer" id="journal_table">
                <thead>
                    <tr>
                        <th class="col-md-1">#</th>
                        <th class="col-md-3"><?php echo app('translator')->get( 'accounting::lang.account' ); ?></th>
                        <th class="col-md-2"><?php echo app('translator')->get( 'accounting::lang.debit' ); ?></th>
                        <th class="col-md-2"><?php echo app('translator')->get( 'accounting::lang.credit' ); ?></th>
                        <th class="col-md-3"><?php echo app('translator')->get( 'accounting::lang.note' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i = 1; $i <= 10; $i++): ?>
                        <tr>
                            <td><?php echo e($i, false); ?></td>
                            <td>
                                <?php echo Form::select('account_id[' . $i . ']', [], null, 
                                            ['class' => 'form-control accounts-dropdown account_id', 
                                            'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); ?>

                            </td>

                            <td>
                                <?php echo Form::text('debit[' . $i . ']', null, ['class' => 'form-control input_number debit']); ?>

                            </td>

                            <td>
                                <?php echo Form::text('credit[' . $i . ']', null, ['class' => 'form-control input_number credit']); ?>

                            </td>
                            <td>
                                <?php echo Form::textarea('note[' . $i . ']', null, ['class' => 'form-control note', 'rows' => 2, 'cols'=> 50]); ?>

                            </td>
                        </tr>
                    <?php endfor; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th></th>
                        <th class="text-center"><?php echo app('translator')->get( 'accounting::lang.total' ); ?></th>
                        <th><input type="hidden" class="total_debit_hidden"><span class="total_debit"></span></th>
                        <th><input type="hidden" class="total_credit_hidden"><span class="total_credit"></span></th>
                    </tr>
                </tfoot>
            </table>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-primary pull-right btn-flat journal_add_btn"><?php echo app('translator')->get('messages.save'); ?></button>
            </div>
        </div>
        
    <?php echo $__env->renderComponent(); ?>

    <?php echo Form::close(); ?>

</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('javascript'); ?>
<?php echo $__env->make('accounting::accounting.common_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script type="text/javascript">
    $(document).ready(function(){ 
        $('.journal_add_btn').click(function(e){
            //e.preventDefault();
            calculate_total();
            
            var is_valid = true;

            //check if same or not
            if($('.total_credit_hidden').val() != $('.total_debit_hidden').val()){
                is_valid = false;
                alert("<?php echo app('translator')->get('accounting::lang.credit_debit_equal'); ?>");
            }

            //check if all account selected or not
            $('table > tbody  > tr').each(function(index, tr) { 
                var credit = __read_number($(tr).find('.credit'));
                var debit = __read_number($(tr).find('.debit'));

                if(credit != 0 || debit != 0){
                    if($(tr).find('.account_id').val() == ''){
                        is_valid = false;
                        alert("<?php echo app('translator')->get('accounting::lang.select_all_accounts'); ?>");
                    }
                }
            });

            if(is_valid){
                $('form#journal_add_form').submit();
            }

            return is_valid;
        });

        $('.credit').change(function(){
            if($(this).val() > 0){
                $(this).parents('tr').find('.debit').val('');
            }
            calculate_total();
        });
        $('.debit').change(function(){
            if($(this).val() > 0){
                $(this).parents('tr').find('.credit').val('');
            }
            calculate_total();
        });
	});

 



    function calculate_total(){
        var total_credit = 0;
        var total_debit = 0;
        $('table > tbody  > tr').each(function(index, tr) { 
            var credit = __read_number($(tr).find('.credit'));
            total_credit += credit;

            var debit = __read_number($(tr).find('.debit'));
            total_debit += debit;
        });

     
        $('.total_credit_hidden').val(total_credit);
        $('.total_debit_hidden').val(total_debit);

        $('.total_credit').text(__number_format(total_credit, 3, '.', ''));
        $('.total_debit').text(__number_format(total_debit, 3, '.', ''));
    }

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\pos\Modules\Accounting\Providers/../Resources/views/journal_entry/create.blade.php ENDPATH**/ ?>