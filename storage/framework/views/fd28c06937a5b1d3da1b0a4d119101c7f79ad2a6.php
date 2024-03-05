<?php $__env->startSection('title', __('lang_v1.manage_modules')); ?>

<?php $__env->startSection('content'); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?php echo app('translator')->get('lang_v1.manage_modules'); ?>
        <small>Only superadmin can access manage modules</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
    <button class="btn btn-sm btn-primary upload_module_btn mt-5">
        <i class="fas fa-upload"></i>
        <?php echo app('translator')->get('lang_v1.upload_module'); ?>
    </button>
</section>

<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-md-12 form_col" style="display: none;">
        <?php $__env->startComponent('components.widget'); ?>
            <?php echo Form::open(['url' => action([\App\Http\Controllers\Install\ModulesController::class, 'uploadModule']), 'id' => 'upload_module_form','files' => true, 'style' => 'display:none']); ?>

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <?php echo Form::label('module', __('lang_v1.upload_module') . ":*"); ?>


                            <?php echo Form::file('module', ['required', 'accept' => 'application/zip']); ?>

                            <p class="help-block">
                                <?php echo app('translator')->get("lang_v1.pls_upload_valid_zip_file"); ?>
                            </p>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <?php echo app('translator')->get('lang_v1.upload'); ?>
                        </button>
                        &nbsp;
                        <button type="button" class="btn btn-danger btn-sm cancel_upload_btn">
                            <?php echo app('translator')->get('messages.cancel'); ?>
                        </button>
                    </div>
                </div>
            <?php echo Form::close(); ?>

        <?php echo $__env->renderComponent(); ?>
    </div>
    <div class="col-md-12">
    <?php $__env->startComponent('components.widget'); ?>
        <table class="table">
            <tr class="success">
                <th class="col-md-1">#</th>
                <th class="col-md-4"><?php echo app('translator')->get('lang_v1.modules'); ?></th>
                <th class="col-md-7"><?php echo app('translator')->get('lang_v1.description'); ?></th>
            </tr>
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <tr>
                    <td>
                        <?php echo e($loop->iteration, false); ?>

                    </td>
                    <td>
                        <strong><?php echo e($module['name'], false); ?></strong> <br/>
                        <?php if(!$module['is_installed']): ?>
                            <a class="btn btn-success btn-xs" 
                            <?php if($is_demo): ?>
                                href="#"
                                title="<?php echo app('translator')->get('lang_v1.disabled_in_demo'); ?>"
                                disabled
                            <?php else: ?>
                                href="<?php echo e($module['install_link'], false); ?>"
                            <?php endif; ?>
                            > <?php echo app('translator')->get('lang_v1.install'); ?></a>
                        <?php else: ?>
                            <a class="btn btn-warning btn-xs"
                                <?php if($is_demo): ?>
                                    href="#"
                                    disabled
                                    title="<?php echo app('translator')->get('lang_v1.disabled_in_demo'); ?>"
                                <?php else: ?>
                                    href="<?php echo e($module['uninstall_link'], false); ?>"
                                <?php endif; ?>
                                onclick="return confirm('Do you really want to uninstall the module? Module will be uninstall but the data will not be deleted')"
                            ><?php echo app('translator')->get('lang_v1.uninstall'); ?>
                            </a>

                            
                        <?php endif; ?>

                        <form 
                            action="<?php echo e(action([\App\Http\Controllers\Install\ModulesController::class, 'destroy'], ['module_name' => $module['name']]), false); ?>"
                                style="display: inline;" 
                                method="post"
                                onsubmit="return confirm('Do you really want to delete the module? Module code will be deleted but the data will not be deleted')"
                            >
                                <?php echo method_field('DELETE'); ?>
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-danger btn-xs"
                                    <?php if($is_demo): ?>
                                    disabled="disabled" 
                                    title="<?php echo app('translator')->get('lang_v1.disabled_in_demo'); ?>"
                                    <?php endif; ?>
                                >
                                <?php echo app('translator')->get('messages.delete'); ?></button>
                            </form>
                    </td>

                    <td>
                        <?php echo e($module['description'], false); ?> <br/>
                        <?php if(isset($module['version'])): ?>
                            <small class="label bg-gray"><?php echo app('translator')->get('lang_v1.version'); ?> <?php echo e($module['version']['installed_version'], false); ?></small>
                        <?php endif; ?>

                        <?php if(!empty($module['version']) && $module['version']['is_update_available']): ?>
                            <div class="alert alert-warning mt-5">
                                <i class="fas fa-sync"></i> <?php echo app('translator')->get('lang_v1.module_new_version', ['module' => $module['name'], 'link' => $module['update_link']]); ?> 
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <?php
                $mods = unserialize($mods);
            ?>

            <?php $__currentLoopData = $mods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!isset($modules[$mod['n']])): ?>
                    <tr>
                        <td><i class="fas fa-hand-point-right fa-2x"></i></td>
                        <td>
                            <strong><?php echo e($mod['dn'], false); ?></strong> <br/>
                            <button onclick="window.open('<?php echo e($mod['u'], false); ?>', '_blank')" 
                            class="btn btn-xs btn-success"><i class="fas fa-money-bill"></i> Buy</button>
                        </td>
                        <td>
                            <?php echo e($mod['d'], false); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
        <?php echo $__env->renderComponent(); ?>
    </div>
</div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('javascript'); ?>
<script type="text/javascript">
    //show a hidden form on upload_module_btn click
    $(document).on('click', '.upload_module_btn', function(){
        $(".form_col,form#upload_module_form").fadeToggle();
    });

    //hide form on cancel_upload_btn click
    $(document).on('click', '.cancel_upload_btn', function(){
        $("form#upload_module_form")[0].reset();
        $(".form_col,form#upload_module_form").fadeOut();
    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pos\resources\views/install/modules/index.blade.php ENDPATH**/ ?>