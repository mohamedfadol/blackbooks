<table class="table table-bordered table-striped">
    <thead>
        <tr> 
            <th><?php echo app('translator')->get( 'user.name' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $account_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="bg-gray">
                
                
                <td><?php echo e($account->name_ar, false); ?></td>
                 
               
            </tr> 
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if(!$account_exist): ?>
            <tr>
                <td colspan="10" class="text-center">
                    <h3><?php echo app('translator')->get( 'accounting::lang.no_accounts' ); ?></h3>
                    <p><?php echo app('translator')->get( 'accounting::lang.add_default_accounts_help' ); ?></p>
                    <a href="<?php echo e(route('accounting.create-default-accounts'), false); ?>" class="btn btn-success btn-xs"><?php echo app('translator')->get( 'accounting::lang.add_default_accounts' ); ?> <i class="fas fa-file-import"></i></a>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table><?php /**PATH C:\xampp\htdocs\pos\Modules\Accounting\Providers/../Resources/views/chart_of_accounts/accounts_table.blade.php ENDPATH**/ ?>