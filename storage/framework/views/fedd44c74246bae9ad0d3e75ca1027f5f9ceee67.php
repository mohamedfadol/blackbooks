<?php if($__is_essentials_enabled && $is_employee_allowed): ?>
	<button 
		type="button" 
		class="btn bg-blue btn-flat 
		pull-left m-8 btn-sm mt-10 
		clock_in_btn
		<?php if(!empty($clock_in)): ?>
	    	hide
	    <?php endif; ?>
		"
	    data-type="clock_in"
	    data-toggle="tooltip"
	    data-placement="bottom"
	    title="<?php echo app('translator')->get('essentials::lang.clock_in'); ?>" 
	    >
	    <i class="fas fa-arrow-circle-down"></i>
	</button>

	<button 
		type="button" 
		class="btn bg-yellow btn-flat pull-left m-8 
		 btn-sm mt-10 clock_out_btn
		<?php if(empty($clock_in)): ?>
	    	hide
	    <?php endif; ?>
		" 	
	    data-type="clock_out"
	    data-toggle="tooltip"
	    data-placement="bottom"
	    data-html="true"
	    title="<?php echo app('translator')->get('essentials::lang.clock_out'); ?> <?php if(!empty($clock_in)): ?>
                    <br>
                    <small>
                    	<b><?php echo app('translator')->get('essentials::lang.clocked_in_at'); ?>:</b> <?php echo e(\Carbon::createFromTimestamp(strtotime($clock_in->clock_in_time))->format(session('business.date_format') . ' ' . 'h:i A'), false); ?>

                    </small>
                    <br>
                    <small><b><?php echo app('translator')->get('essentials::lang.shift'); ?>:</b> <?php echo e(ucfirst($clock_in->shift_name), false); ?></small>
                    <?php if(!empty($clock_in->start_time) && !empty($clock_in->end_time)): ?>
                    	<br>
                    	<small>
                    		<b><?php echo app('translator')->get('restaurant.start_time'); ?>:</b> <?php echo e(\Carbon::createFromTimestamp(strtotime($clock_in->start_time))->format('h:i A'), false); ?><br>
                    		<b><?php echo app('translator')->get('restaurant.end_time'); ?>:</b> <?php echo e(\Carbon::createFromTimestamp(strtotime($clock_in->end_time))->format('h:i A'), false); ?>

                    	</small>
                    <?php endif; ?>
                <?php endif; ?>" 
	    >
	    <i class="fas fa-hourglass-half fa-spin"></i>
	</button>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\pos\Modules\Essentials\Providers/../Resources/views/layouts/partials/header_part.blade.php ENDPATH**/ ?>