    <?php echo e($account->name_ar, false); ?>   
    <?php if(!empty($account->account_number)): ?>(<?php echo e($account->account_number, false); ?>) <?php endif; ?>
    <?php echo e(session("currency")["symbol"], false); ?>  <?php echo e(number_format(-1 * $account->totalBalance,3,".",""), false); ?>  
      
     <?php if($account->status == 'active'): ?> 
         <span style="margin-right: 5px;"><i class="fas fa-check text-success" title="<?php echo app('translator')->get( 'accounting::lang.active' ); ?>"></i></span>
     <?php elseif($account->status == 'inactive'): ?> 
         <span style="margin-right: 5px;"><i class="fas fa-times text-danger" title="<?php echo app('translator')->get( 'lang_v1.inactive' ); ?>" style="font-size: 14px;"></i></span>
     <?php endif; ?>
     <span class="tree-actions" style="margin-right: 5px;">
         <a class="btn btn-xs btn-default text-success ledger-link" style="margin-right: 5px;"
             title="<?php echo app('translator')->get( 'accounting::lang.ledger' ); ?>"
             href="<?php echo e(action([\Modules\Accounting\Http\Controllers\CoaController::class, 'ledger'], $account->id), false); ?>">
             <i class="fas fa-file-alt"></i>
         </a>
         <a class="btn-modal btn-xs btn-default text-primary" style="margin-right: 5px;" title="<?php echo app('translator')->get('messages.edit'); ?>"
             href="<?php echo e(action([\Modules\Accounting\Http\Controllers\CoaController::class, 'edit'], $account->id), false); ?>" 
             data-href="<?php echo e(action([\Modules\Accounting\Http\Controllers\CoaController::class, 'edit'], $account->id), false); ?>" 
             data-container="#create_account_modal">
             <i class="fas fa-edit"></i>
         </a>
         <a class="activate-deactivate-btn text-warning  btn-xs btn-default" style="margin-right: 5px;"
             title="<?php if($account->status=='active'): ?> <?php echo app('translator')->get('messages.deactivate'); ?> <?php else: ?> 
             <?php echo app('translator')->get('messages.activate'); ?> <?php endif; ?>"
             href="<?php echo e(action([\Modules\Accounting\Http\Controllers\CoaController::class, 'activateDeactivate'], $account->id), false); ?>">
             <i class="fas fa-power-off"></i>
         </a>
         <?php if($account->isParent->count() == 0 && $account->balance == null && $account->sumBalanceOfChildren() == null): ?>
             <a href="#" class="delete-delete-btn text-danger btn-xs btn-default" style="margin-right: 5px;"
                 title="<?php echo app('translator')->get('messages.delete'); ?>"  
                     data-href="<?php echo e(action([\Modules\Accounting\Http\Controllers\CoaController::class, 'destroy'], [$account->id]), false); ?>">
                 <i class="fas fa-trash"></i>
             </a>
         <?php endif; ?>
     </span>
 
     <ul>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.accounts','data' => ['accounts' => $account->children]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('accounts'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['accounts' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($account->children)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
     </ul><?php /**PATH C:\wamp64\www\pos\resources\views/components/account.blade.php ENDPATH**/ ?>