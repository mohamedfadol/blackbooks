

<?php $__env->startSection('title', __('accounting::lang.trial_balance')); ?>
<?php $__env->startSection('css'); ?>
    <style>
        .hidden {
    display: none !important;
}
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php echo $__env->make('accounting::layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<section class="content">
        
    <div class="col-md-3">
        <div class="form-group">
            <?php echo Form::label('date_range_filter', __('report.date_range') . ':'); ?>

            <?php echo Form::text('date_range_filter', null, 
                ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'date_range_filter']); ?>

        </div>
    </div>

    <div class="col-md-2">
         
            <button type="button" class="btn btn-info toggle-columns" data-target="debit_credit"><?php echo Form::label('debit_credit_filter', __('report.debit_credit')); ?></button>
       
    </div>

    <div class="col-md-2"> 
            <button type="button" class="btn btn-info toggle-columns" data-target="debit_credit_balance"><?php echo Form::label('debit_credit_balance_filter', __('report.debit_credit_balance')); ?></button>
           
    </div>

    

    <div class="col-md-8 col-md-offset-2">
        
        <div class="box box-warning">
            <div class="box-header with-border text-center">
                <h2 class="box-title"><?php echo app('translator')->get( 'accounting::lang.trial_balance'); ?></h2>
                <p><?php echo e(\Carbon::createFromTimestamp(strtotime($start_date))->format(session('business.date_format')), false); ?> ~ <?php echo e(\Carbon::createFromTimestamp(strtotime($end_date))->format(session('business.date_format')), false); ?></p>
            </div>

            <div class="box-body">
                <table class="table table-bordered" border="1" id="trialBalanceTable">
                    <thead>
                        <tr>
                            <th><?php echo app('translator')->get('accounting::lang.account_name'); ?></th>
                            <th class="debit_credit"><?php echo app('translator')->get( 'accounting::lang.debit'); ?></th>
                            <th class="debit_credit"><?php echo app('translator')->get( 'accounting::lang.credit'); ?></th>
                            <th class="debit_credit_balance"><?php echo app('translator')->get( 'accounting::lang.total_debit_balance'); ?></th>
                            <th class="debit_credit_balance"><?php echo app('translator')->get( 'accounting::lang.total_credit_balance'); ?></th>
                        </tr>
                    </thead>

                    <?php
                        $total_debit = 0;
                        $total_credit = 0;
                        $final_balance1 =0;
                        $final_balance2 =0;
                    ?>

                    <tbody>
                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php
                            $total_debit += $account->debit_balance;
                            $total_credit += $account->credit_balance;
                           $final_balance1 += ($account->debit_balance - $account->credit_balance < 0) ? 0 : ($account->debit_balance - $account->credit_balance );
                           $final_balance2 += ($account->debit_balance - $account->credit_balance < 0) ? ($account->debit_balance - $account->credit_balance ) : 0;
                        ?>

                            <tr class="<?php echo e($account->parent_id != null ? "text-primary" : "text-success", false); ?>">
                                <td> <a  class="<?php echo e($account->parent_id != null ? "text-primary" : "text-success", false); ?>" href="<?php echo e(route("accounting.ledger",$account->id), false); ?>"> <?php echo e($account->account_number, false); ?> - <?php echo e($account->name_ar ?? $account->name_en, false); ?> </a> </td>
                                <td>
                                    <?php if($account->debit_balance != 0): ?>
                                        <?php echo e(number_format($account->debit_balance,3,".",""), false); ?>

                                    <?php endif; ?>    
                                </td>
                                <td>
                                    <?php if($account->credit_balance != 0): ?>
                                        <?php echo e(number_format($account->credit_balance,3,".",""), false); ?>

                                    <?php endif; ?>
                                </td>
                                <?php
                                    $db1 = ($account->debit_balance - $account->credit_balance < 0) ? 0 : ($account->debit_balance - $account->credit_balance );
                                    $db2 = ($account->debit_balance - $account->credit_balance < 0) ? ($account->debit_balance - $account->credit_balance ) : 0;
                                ?>
                                <td><?php echo e(number_format($db1,3,".",""), false); ?></td>
                                <td><?php echo e(number_format($db2,3,".",""), false); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th><?php echo app('translator')->get('accounting::lang.total'); ?> </th>
                            <th class="total_debit"><?php echo e(session("currency")["symbol"], false); ?> <?php echo e(number_format($total_debit,3,".",""), false); ?></th>
                            <th class="total_credit"><?php echo e(session("currency")["symbol"], false); ?> <?php echo e(number_format($total_credit,3,".",""), false); ?></th>
                            <th class="total_debit_balance"><?php echo e(session("currency")["symbol"], false); ?> <?php echo e(number_format($final_balance1,3,".",""), false); ?></th>
                            <th class="total_credit_balance"><?php echo e(session("currency")["symbol"], false); ?> <?php echo e(number_format($final_balance2,3,".",""), false); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

</section>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('javascript'); ?>
 
<script type="text/javascript">
    $(document).ready(function(){
        dateRangeSettings.startDate = moment('<?php echo e($start_date, false); ?>');
        dateRangeSettings.endDate = moment('<?php echo e($end_date, false); ?>');
        $('#date_range_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                apply_filter();
            }
        );
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range_filter').val('');
            apply_filter();
        });

        function apply_filter(){
            var start = '';
            var end = '';

            if ($('#date_range_filter').val()) {
                start = $('input#date_range_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                end = $('input#date_range_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            }

            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('start_date', start);
            urlParams.set('end_date', end);
            window.location.search = urlParams;
        }


 
         
            var table = document.getElementById('trialBalanceTable');
            document.querySelectorAll('.toggle-columns').forEach(function (button) {
                button.addEventListener('click', function () {
                    var target = $(this).data('target');
                    console.log(target);
                    toggleColumns(target);
                });
            });

            function toggleColumns(target) {
                var columnIndexStart, columnIndexEnd;

                if (target === 'debit_credit') {
                    columnIndexStart = 1;
                    columnIndexEnd = 2;
                } else if (target === 'debit_credit_balance') {
                    columnIndexStart = 3;
                    columnIndexEnd = 4;
                }

                for (var i = columnIndexStart; i <= columnIndexEnd; i++) {
                    toggleColumnVisibility(table, i);
                }
            }

            function toggleColumnVisibility(table, columnIndex) {
                for (var row of table.rows) {
                    row.cells[columnIndex].classList.toggle('hidden');
                }
            }
        
    });

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pos\Modules\Accounting\Providers/../Resources/views/report/trial_balance.blade.php ENDPATH**/ ?>