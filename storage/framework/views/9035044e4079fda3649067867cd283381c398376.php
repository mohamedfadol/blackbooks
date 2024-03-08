

<?php $__env->startSection('title', __('accounting::lang.ledger')); ?>

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('accounting::layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><?php echo app('translator')->get( 'accounting::lang.ledger' ); ?> - <?php echo e($account->name, false); ?></h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-5">
            <div class="box box-solid">
                <div class="box-body">
                    <table class="table table-condensed">
                        <tr>
                            <th><?php echo app('translator')->get( 'user.name' ); ?>:</th>
                            <td>
                                <?php echo e($account->name_ar ?? $account->name_en, false); ?>


                                <?php if(!empty($account->account_number)): ?>
                                    (<?php echo e($account->account_number, false); ?>)
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo app('translator')->get('accounting::lang.account_type' ); ?>:</th>
                            <td>
                                <?php if(!$account->isChild()): ?>
                                    <?php echo e($account->name_ar, false); ?>

                                    <?php else: ?>
                                    <?php echo e(\App\MainAccount::find($account->parent_id)->name_ar, false); ?>

                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- <tr>
                            <th><?php echo app('translator')->get( 'accounting::lang.account_sub_type' ); ?>:</th>
                            <td>
                                <?php if($account->isChild()): ?>
                                    <?php echo e(\App\MainAccount::find($account->parent_id)->name_ar, false); ?>

                                    <?php else: ?>
                                    <?php echo e($account->name_ar, false); ?>

                                <?php endif; ?>
                            </td>
                        </tr> -->
 
                        <tr>
                            <th><?php echo app('translator')->get( 'accounting::lang.detail_type' ); ?>:</th>
                            <td>
                                <?php if(!empty($account->description)): ?>
                                    <?php echo e(__('accounting::lang.' . $account->description), false); ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo app('translator')->get( 'lang_v1.balance' ); ?>:</th>
                            <td><?php echo e(session("currency")["symbol"], false); ?>  <?php echo e(number_format($current_bal,3,".",""), false); ?> </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-7">
        
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title"> <i class="fa fa-filter" aria-hidden="true"></i> <?php echo app('translator')->get('report.filters'); ?>:</h3>
                </div>
                <div class="box-body">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php echo Form::label('transaction_date_range', __('report.date_range') . ':'); ?>

                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <?php echo Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]); ?>

                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php echo Form::label('all_accounts', __( 'accounting::lang.account' ) . ':'); ?>

                            <?php echo Form::select('account_filter', [$account->id => $account->name_ar ?? $account->name_en], $account->id,
                                ['class' => 'form-control accounts-dropdown', 'style' => 'width:100%', 
                                'id' => 'account_filter', 'data-default' => $account->id]); ?>

                        </div>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-sm-12">
        	<div class="box">
                <div class="box-body">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('account.access')): ?>
                        <div class="table-responsive">
                    	<table class="table table-bordered table-striped" id="ledger">
                    		<thead>
                    			<tr>
                                    <th><?php echo app('translator')->get( 'messages.date' ); ?></th>
                                    <th><?php echo app('translator')->get( 'lang_v1.original' ); ?></th>
                                    <th><?php echo app('translator')->get( 'lang_v1.ref_no' ); ?></th>
                                    <th><?php echo app('translator')->get( 'brand.note' ); ?></th>
                                    <th><?php echo app('translator')->get( 'lang_v1.added_by' ); ?></th>
                                    <th><?php echo app('translator')->get('account.debit'); ?></th>
                                    <th><?php echo app('translator')->get('account.credit'); ?></th>
                    				<th><?php echo app('translator')->get( 'lang_v1.balanceT' ); ?></th>
                    			</tr>
                    		</thead>
                            <tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    <td colspan="5"><strong><?php echo app('translator')->get('sale.total'); ?>:</strong></td>
                                    <td class="footer_total_debit"></td>
                                    <td class="footer_total_credit"></td>
                                    
                                    <td class="footer_total_credit_and_debit"></td>
                                </tr>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    
                                    <td></td>
                                    <td></td>
                                    <td colspan="5"><strong><?php echo app('translator')->get('lang_v1.total_balance'); ?>:</strong></td>
                                    <td class="footer_total_balance"></td>
                                    
                                </tr>
                            </tfoot>
                    	</table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('javascript'); ?>
<?php echo $__env->make('accounting::accounting.common_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
    $(document).ready(function(){        
        $('#account_filter').change(function(){
            account_id = $(this).val();
            url = base_path + '/accounting/ledger/' + account_id;
            window.location = url;
        })

        dateRangeSettings.startDate = moment().subtract(6, 'days');
        dateRangeSettings.endDate = moment();
        $('#transaction_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                
                ledger.ajax.reload();
            }
        );
        
        // Account Book
        ledger = $('#ledger').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: '<?php echo e(action([\Modules\Accounting\Http\Controllers\CoaController::class, 'ledger'],[$account->id]), false); ?>',
                                data: function(d) {
                                    var start = '';
                                    var end = '';
                                    if($('#transaction_date_range').val()){
                                        start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                        end = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                    }
                                    var transaction_type = $('select#transaction_type').val();
                                    d.start_date = start;
                                    d.end_date = end;
                                    d.type = transaction_type;
                                }
                            },
                            "ordering": false,
                            columns: [
                                {data: 'operation_date', name: 'operation_date'},
                                {data: 'original', name: 'original'},
                                {data: 'ref_no', name: 'ref_no'},
                                {data: 'note', name: 'note'},
                                {data: 'added_by', name: 'added_by'},
                                {data: 'debit', name: 'amount', searchable: false},
                                {data: 'credit', name: 'amount', searchable: false},
                                {data: 'balance', name: 'balanceT', searchable: false},
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#ledger'));
                            },
                            "footerCallback": function ( row, data, start, end, display ) {
                                var footer_total_debit = 0;
                                var footer_total_credit = 0;
                                var footer_total_credit_and_debit = 0;

                                for (var r in data){
                                    footer_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;
                                    footer_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;
                                    footer_total_credit_and_debit += $(data[r].balance).data('orig-value') ? parseFloat($(data[r].balance).data('orig-value')) : 0;
                                }

                                var api = this.api();

                                // Sum the balanceT column
                                var totalBalance = api.column(7, { page: 'current' }).data().reduce(function (a, b) {
                                    return a + parseFloat(b);
                                }, 0);

                                // Update the footer
                                $(api.column(7).footer()).text(__currency_trans_from_en(totalBalance));

                                var rows = api.rows({ page: 'current' }).nodes();
                                var lastBalance = 0;
                                api.column(7, { page: 'current' }).data().each(function(value, index) {
                                    lastBalance += parseFloat(value || 0);
                                    // Update the 'balanceT' column in the current row
                                    $(rows[index]).find('td:eq(7)').text(__currency_trans_from_en(lastBalance.toFixed(2)) );
                                });

                                $('.footer_total_debit').text(__currency_trans_from_en(footer_total_debit));
                                $('.footer_total_credit').text(__currency_trans_from_en(footer_total_credit));
                                $('.footer_total_balance').text(__currency_trans_from_en(footer_total_debit - footer_total_credit));
                                
                            }
                        });
        $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#transaction_date_range').val('');
            ledger.ajax.reload();
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\pos\Modules\Accounting\Providers/../Resources/views/chart_of_accounts/ledger.blade.php ENDPATH**/ ?>