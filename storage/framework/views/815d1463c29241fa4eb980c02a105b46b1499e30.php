

<?php $__env->startSection('title', __('accounting::lang.journal_entry')); ?>

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('accounting::layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><?php echo app('translator')->get( 'accounting::lang.reports' ); ?></h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo app('translator')->get( 'accounting::lang.trial_balance'); ?></h3>
                </div>

                <div class="box-body">
                    <?php echo app('translator')->get( 'accounting::lang.trial_balance_description'); ?>
                    <br/>
                    <a href="<?php echo e(route('accounting.trialBalance'), false); ?>" class="btn btn-primary btn-sm pt-2"><?php echo app('translator')->get( 'accounting::lang.view_report'); ?></a>
                </div>

            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo app('translator')->get( 'accounting::lang.ledger_report'); ?></h3>
                </div>

                <div class="box-body">
                    <?php echo app('translator')->get( 'accounting::lang.ledger_report_description'); ?>
                    <br/>
                    <a <?php if($ledger_url): ?> href="<?php echo e($ledger_url, false); ?>" <?php else: ?> onclick="alert(' <?php echo app('translator')->get( 'accounting::lang.ledger_add_account'); ?> ')" <?php endif; ?> class="btn btn-primary btn-sm pt-2"><?php echo app('translator')->get( 'accounting::lang.view_report'); ?></a>
                </div>

            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo app('translator')->get( 'accounting::lang.balance_sheet'); ?></h3>
                </div>

                <div class="box-body">
                    <?php echo app('translator')->get( 'accounting::lang.balance_sheet_description'); ?>
                    <br/>
                    <a href="<?php echo e(route('accounting.balanceSheet'), false); ?>" class="btn btn-primary btn-sm pt-2"><?php echo app('translator')->get( 'accounting::lang.view_report'); ?></a>
                </div>

            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo app('translator')->get( 'accounting::lang.account_recievable_ageing_report'); ?></h3>
                </div>
                <div class="box-body">
                    <?php echo app('translator')->get( 'accounting::lang.account_recievable_ageing_report_description'); ?>
                    <br/>
                    <a href="<?php echo e(route('accounting.account_receivable_ageing_report'), false); ?>" 
                    class="btn btn-primary btn-sm pt-2"><?php echo app('translator')->get( 'accounting::lang.view_report'); ?></a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo app('translator')->get( 'accounting::lang.account_payable_ageing_report'); ?></h3>
                </div>
                <div class="box-body">
                    <?php echo app('translator')->get( 'accounting::lang.account_payable_ageing_report_description'); ?>
                    <br/>
                    <a href="<?php echo e(route('accounting.account_payable_ageing_report'), false); ?>" 
                    class="btn btn-primary btn-sm pt-2"><?php echo app('translator')->get( 'accounting::lang.view_report'); ?></a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo app('translator')->get( 'accounting::lang.account_receivable_ageing_details'); ?></h3>
                </div>
                <div class="box-body">
                    <?php echo app('translator')->get( 'accounting::lang.account_receivable_ageing_details_description'); ?>
                    <br/>
                    <a href="<?php echo e(route('accounting.account_receivable_ageing_details'), false); ?>" 
                    class="btn btn-primary btn-sm pt-2"><?php echo app('translator')->get( 'accounting::lang.view_report'); ?></a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo app('translator')->get( 'accounting::lang.account_payable_ageing_details'); ?></h3>
                </div>
                <div class="box-body">
                    <?php echo app('translator')->get( 'accounting::lang.account_payable_ageing_details_description'); ?>
                    <br/>
                    <a href="<?php echo e(route('accounting.account_payable_ageing_details'), false); ?>" 
                    class="btn btn-primary btn-sm pt-2"><?php echo app('translator')->get( 'accounting::lang.view_report'); ?></a>
                </div>
            </div>
        </div>

    </div>
</section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\pos\Modules\Accounting\Providers/../Resources/views/report/index.blade.php ENDPATH**/ ?>