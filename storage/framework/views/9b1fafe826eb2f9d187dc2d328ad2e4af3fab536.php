

<?php $__env->startSection('title', __('accounting::lang.journal_entry')); ?>

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('accounting::layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <?php if(request()->get('type') == 'journal-entry'): ?>
        <h1><?php echo app('translator')->get( 'accounting::lang.journal_entry' ); ?></h1>

        <?php elseif(request()->get('type') == 'journal-opening'): ?>
        <h1><?php echo app('translator')->get( 'accounting::lang.journal_entry_2' ); ?></h1>
        <?php else: ?>
        <h1><?php echo app('translator')->get( 'accounting::lang.journal_entry_3' ); ?></h1>
    <?php endif; ?>
    
</section>
<section class="content no-print">
<div class="row">
        <div class="col-md-12"> 
            <?php $__env->startComponent('components.filters', ['title' => __('report.filters')]); ?>
                <div class="col-md-4">
                    <div class="form-group">
                        <?php echo Form::label('journal_entry_date_range_filter', __('report.date_range') . ':'); ?>

                        <?php echo Form::text('journal_entry_date_range_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); ?>

                    </div>
                </div>
            <?php echo $__env->renderComponent(); ?>
        </div>
    </div>
	<?php $__env->startComponent('components.widget', ['class' => 'box-solid']); ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('accounting.add_journal')): ?>
            <?php $__env->slot('tool'); ?>
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" 
                        href="<?php echo e(action([\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'create'],['type' => request()->get('type')]), false); ?>">
                        <i class="fas fa-plus"></i> <?php echo app('translator')->get( 'messages.add' ); ?></a>
                </div>
            <?php $__env->endSlot(); ?>
        <?php endif; ?>
        
        <table class="table table-bordered table-striped" id="journal_table">
            <thead>
                <tr>
                    <th><?php echo app('translator')->get('messages.action'); ?></th>
                    <th><?php echo app('translator')->get('accounting::lang.journal_date'); ?></th>
                    <th><?php echo app('translator')->get('purchase.ref_no'); ?></th>
                    <th><?php echo app('translator')->get('lang_v1.added_by'); ?></th>
                    <th><?php echo app('translator')->get('lang_v1.additional_notes'); ?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        
    <?php echo $__env->renderComponent(); ?>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('javascript'); ?>
<script type="text/javascript">

    $(document).ready( function(){
        let params = new URLSearchParams(window.location.search);
        let type = params.get('type');
        console.log(type);
        if (type) {
            
        } else {
            
        }
        //Journal table
        journal_table = $('#journal_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/accounting/journal-entry/journal_openning',
                data: {
                    type: type
                },
                data: function(d) {
                    var start = '';
                    var end = '';
                    if ($('#journal_entry_date_range_filter').val()) {
                        start = $('input#journal_entry_date_range_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        end = $('input#journal_entry_date_range_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    }
                    d.start_date = start;
                    d.end_date = end;
                },
            },
            aaSorting: [[1, 'desc']],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'operation_date', name: 'operation_date' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'added_by', name: 'added_by' },
                { data: 'note', name: 'note' }
            ]
        });

        $('#journal_entry_date_range_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#journal_entry_date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                journal_table.ajax.reload();
            }
        );
        $('#journal_entry_date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#journal_entry_date_range_filter').val('');
            journal_table.ajax.reload();
        });

        //Delete Sale
        $(document).on('click', '.delete_journal_button', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).attr('data-href');
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                journal_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });

	});

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pos\Modules\Accounting\Providers/../Resources/views/journal_entry/journal_openning.blade.php ENDPATH**/ ?>