

<?php $__env->startSection('title', __('accounting::lang.budget')); ?>

<?php $__env->startSection('css'); ?>
<style>
.table-sticky thead,
.table-sticky tfoot {
  position: sticky;
}
.table-sticky thead {
  inset-block-start: 0; /* "top" */
}
.table-sticky tfoot {
  inset-block-end: 0; /* "bottom" */
}
.collapsed .collapse-tr {
    display: none;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('accounting::layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><?php echo app('translator')->get( 'accounting::lang.budget' ); ?></h1>
</section>
<section class="content">
	<?php $__env->startComponent('components.widget', ['class' => 'box-solid']); ?>
        <?php $__env->slot('tool'); ?>
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary" data-toggle="modal"  
                    data-target="#add_budget_modal">
                    <i class="fas fa-plus"></i> <?php echo app('translator')->get( 'messages.add' ); ?></button>
            </div>
        <?php $__env->endSlot(); ?>
        <div class="card-body">
            <div class="row mb-10">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fiscal_year_picker"><?php echo app('translator')->get( 'accounting::lang.financial_year_for_the_budget' ); ?></label>
                        <input type="text" class="form-control" id="fiscal_year_picker" value="<?php echo e($fy_year, false); ?>" readonly>
                    </div>
                </div>
            </div>
            <?php if(count($budget)!=0): ?>
                <?php echo $__env->make('accounting::budget.budget_table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h4><?php echo app('translator')->get( 'accounting::lang.select_a_financial_year' ); ?></h4>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php echo $__env->renderComponent(); ?>
</section>
<div class="modal fade" id="add_budget_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <?php echo Form::open(['url' => action([\Modules\Accounting\Http\Controllers\BudgetController::class, 'create']), 
            'method' => 'get', 'id' => 'add_budget_form' ]); ?>

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo app('translator')->get( 'accounting::lang.financial_year_for_the_budget' ); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php echo Form::number('financial_year', null, ['class' => 'form-control', 
                                'required', 'placeholder' => 
                                __( 'accounting::lang.financial_year_for_the_budget' ), 'id' => 'financial_year' ]); ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><?php echo app('translator')->get( 'accounting::lang.continue' ); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo app('translator')->get( 'messages.close' ); ?></button>
            </div>
            <?php echo Form::close(); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('javascript'); ?>
<script type="text/javascript">
	$(document).ready( function(){
        $('#fiscal_year_picker').datepicker({
            format: "yyyy",
            viewMode: "years", 
            minViewMode: "years"
        }).on('changeDate', function(e){
            window.location.href = 
            "<?php echo e(action([\Modules\Accounting\Http\Controllers\BudgetController::class, 'index']), false); ?>?financial_year=" + $('#fiscal_year_picker').val();
        });

        $('#financial_year').datepicker({
            format: "yyyy",
            viewMode: "years", 
            minViewMode: "years"
        })
	});
    $(document).on('click', '.toggle-tr', function(){
        $(this).closest('tbody').toggleClass('collapsed');
        var html = $(this).closest('tbody').hasClass('collapsed') ? 
        '<i class="fas fa-arrow-circle-right"></i>' : '<i class="fas fa-arrow-circle-down"></i>';
        $(this).find('.collapse-icon').html(html);
    })
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pos\Modules\Accounting\Providers/../Resources/views/budget/index.blade.php ENDPATH**/ ?>