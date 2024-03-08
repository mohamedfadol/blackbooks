
<?php if(!$account_exist): ?>
<table class="table table-bordered table-striped">
    <tr>
        <td colspan="10" class="text-center">
            <h3><?php echo app('translator')->get( 'accounting::lang.no_accounts' ); ?></h3>
            <p><?php echo app('translator')->get( 'accounting::lang.add_default_accounts_help' ); ?></p>
            <a href="<?php echo e(route('accounting.create-default-accounts'), false); ?>" class="btn btn-success btn-xs"><?php echo app('translator')->get( 'accounting::lang.add_default_accounts' ); ?> <i class="fas fa-file-import"></i></a>
        </td>
    </tr>
</table>
<?php else: ?>
<div class="row">
    <div class="col-md-4 mb-12 col-md-offset-4">
        <div class="input-group">
            <input type="input" class="form-control" id="accounts_tree_search">
            <span class="input-group-addon">
                <i class="fas fa-search"></i>
            </span>
        </div> 
    </div>
    <div class="col-md-4">
        <button class="btn btn-primary btn-sm" id="expand_all"><?php echo app('translator')->get('accounting::lang.expand_all'); ?></button>
        <button class="btn btn-primary btn-sm" id="collapse_all"><?php echo app('translator')->get('accounting::lang.collapse_all'); ?></button>
    </div>
    <div class="col-md-12" id="accounts_tree_container">
        <ul>
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.accounts','data' => ['accounts' => $account_types]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('accounts'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['accounts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($account_types)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        </ul>
    </div>
</div>
<?php endif; ?><?php /**PATH C:\wamp64\www\pos\Modules\Accounting\Providers/../Resources/views/chart_of_accounts/accounts_tree.blade.php ENDPATH**/ ?>