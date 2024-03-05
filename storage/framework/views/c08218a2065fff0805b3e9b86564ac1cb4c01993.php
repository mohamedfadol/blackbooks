<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="<?php echo e($id, false); ?>">
                        <thead>
                            <tr>
                                <th><?php echo app('translator')->get('messages.action'); ?></th>
                                <th><?php echo app('translator')->get('messages.date'); ?></th>
                                <th><?php echo app('translator')->get('account.payment_ref_no'); ?></th>
                                <th><?php echo app('translator')->get('account.invoice_ref_no'); ?></th>
                                <th><?php echo app('translator')->get('sale.amount'); ?></th>
                                <th><?php echo app('translator')->get('lang_v1.payment_type'); ?></th>
                                <th><?php echo app('translator')->get( 'lang_v1.description' ); ?></th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><?php /**PATH C:\xampp\htdocs\pos\Modules\Accounting\Providers/../Resources/views/transactions/partials/payments.blade.php ENDPATH**/ ?>