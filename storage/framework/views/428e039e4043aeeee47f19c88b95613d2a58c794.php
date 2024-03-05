<div class="pos-tab-content">
    <div class="row">
        <?php $__env->startComponent('components.filters', ['title' => __('report.filters')]); ?>
            <div class="col-md-3">
                <div class="form-group">
                    <?php echo Form::label('purchase_list_filter_location_id',  __('purchase.business_location') . ':'); ?>

                    <?php echo Form::select('purchase_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); ?>

                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?php echo Form::label('purchase_list_filter_supplier_id',  __('purchase.supplier') . ':'); ?>

                    <?php echo Form::select('purchase_list_filter_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); ?>

                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?php echo Form::label('purchase_list_filter_status',  __('purchase.purchase_status') . ':'); ?>

                    <?php echo Form::select('purchase_list_filter_status', $orderStatuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); ?>

                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?php echo Form::label('purchase_list_filter_payment_status',  __('purchase.payment_status') . ':'); ?>

                    <?php echo Form::select('purchase_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); ?>

                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?php echo Form::label('purchase_list_filter_date_range', __('report.date_range') . ':'); ?>

                    <?php echo Form::text('purchase_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); ?>

                </div>
            </div>
        <?php echo $__env->renderComponent(); ?>
    </div>

    <div class="row">
        <div class="col-md-12">
            

        <table class="table table-bordered table-striped" id="purchase_table">
            <thead>
                <tr>
                    <th><?php echo app('translator')->get('messages.action'); ?></th>
                    <th><?php echo app('translator')->get('messages.date'); ?></th>
                    <th><?php echo app('translator')->get('purchase.ref_no'); ?></th>
                    <th><?php echo app('translator')->get('purchase.location'); ?></th>
                    <th><?php echo app('translator')->get('purchase.supplier'); ?></th>
                    <th><?php echo app('translator')->get('purchase.purchase_status'); ?></th>
                    <th><?php echo app('translator')->get('purchase.payment_status'); ?></th>
                    <th><?php echo app('translator')->get('purchase.grand_total'); ?></th>
                    <th><?php echo app('translator')->get('purchase.payment_due'); ?> &nbsp;&nbsp;<i class="fa fa-info-circle text-info no-print" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="<?php echo e(__('messages.purchase_due_tooltip'), false); ?>" aria-hidden="true"></i></th>
                    <th><?php echo app('translator')->get('lang_v1.added_by'); ?></th>
                </tr>
            </thead>
        </table>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\pos\Modules\Accounting\Providers/../Resources/views/transactions/partials/purchases.blade.php ENDPATH**/ ?>