<div class="modal fade" id="create_account_type_modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    <?php echo Form::open(['url' => action([\Modules\Accounting\Http\Controllers\AccountTypeController::class, 'store']), 
        'method' => 'post', 'id' => 'create_account_type_form' ]); ?>

    <?php echo Form::hidden('account_type', null, ['id' => 'account_type']); ?>

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="account_type_title"></h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?php echo Form::label('name', __( 'user.name' ) . ':*'); ?>

                    <?php echo Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'user.name' ) ]); ?>

                </div>
            </div>
        </div>

        <div class="row" id="account_type_div">
            <div class="col-md-12">
              <div class="form-group">
                <?php echo Form::label('parent_id', __( 'accounting::lang.account_type' ) . ':*'); ?>

                  <select class="form-control" style="width: 100%;" name="account_primary_type" id="account_primary_type">
                   <option value=""><?php echo app('translator')->get('messages.please_select'); ?></option>
                        <?php $__currentLoopData = $account_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k, false); ?>"><?php echo e($v['label'], false); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
              </div>
            </div>
        </div>

        <div class="row" id="parent_id_div">
            <div class="col-md-12">
              <div class="form-group">
                <?php echo Form::label('parent_id', __( 'accounting::lang.parent_type' ) . ':*'); ?>

                  <select class="form-control" style="width: 100%;" name="parent_id" id="parent_id">
                   <option value=""><?php echo app('translator')->get('messages.please_select'); ?></option>
                     <?php $__currentLoopData = $account_sub_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <option value="<?php echo e($account_type->id, false); ?>">
                      <?php echo e($account_type->account_type_name, false); ?></option>
                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
              </div>
            </div>
        </div>
        <div class="row" id="description_div">
            <div class="col-md-12">
                <div class="form-group">
                    <?php echo Form::label('description', __( 'lang_v1.description' ) . ':'); ?>

                    <?php echo Form::textarea('description', null, ['class' => 'form-control', 
                        'placeholder' => __( 'lang_v1.description' ), 'rows' => 3 ]); ?>

                </div>
            </div>
        </div> 
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary"><?php echo app('translator')->get( 'messages.save' ); ?></button>
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo app('translator')->get( 'messages.close' ); ?></button>
    </div>

    <?php echo Form::close(); ?>


  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><?php /**PATH C:\xampp\htdocs\pos\Modules\Accounting\Providers/../Resources/views/account_type/create.blade.php ENDPATH**/ ?>