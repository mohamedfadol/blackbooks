

<?php $__env->startSection('title', __('accounting::lang.accounting')); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('accounting::layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group pull-right">
                        <div class="input-group">
                        <button type="button" class="btn btn-primary" id="dashboard_date_filter">
                            <span>
                            <i class="fa fa-calendar"></i> <?php echo e(__('messages.filter_by_date'), false); ?>

                            </span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                        </div>
                </div>
            </div>
        </div>
        <div class="row">
            
        </div>

        <div class="row">
            <?php $__currentLoopData = $all_charts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $chart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6">
                <?php $__env->startComponent('components.widget', ['class' => 'box-primary', 
                'title' => __('accounting::lang.' . $key)]); ?>
                <?php echo $chart->container(); ?>

                <?php echo $__env->renderComponent(); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('javascript'); ?>
<?php echo $coa_overview_chart->script(); ?>

<?php $__currentLoopData = $all_charts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $chart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php echo $chart->script(); ?>


<script type="text/javascript">
    $(document).ready( function(){
        dateRangeSettings.startDate = moment('<?php echo e($start_date, false); ?>', 'YYYY-MM-DD');
        dateRangeSettings.endDate = moment('<?php echo e($end_date, false); ?>', 'YYYY-MM-DD');
        $('#dashboard_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#dashboard_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );  
            
            var start = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');

            var end = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            var url = "<?php echo e(action([\Modules\Accounting\Http\Controllers\AccountingController::class, 'dashboard']), false); ?>?start_date=" + start + '&end_date=' + end;

            window.location.href = url;
        });
    });
</script>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pos\Modules\Accounting\Providers/../Resources/views/accounting/dashboard.blade.php ENDPATH**/ ?>