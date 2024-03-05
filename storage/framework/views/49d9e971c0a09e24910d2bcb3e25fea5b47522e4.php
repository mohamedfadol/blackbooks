<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo e(action([\Modules\Accounting\Http\Controllers\AccountingController::class, 'dashboard']), false); ?>"><i class="fas fa fa-broadcast-tower"></i> <?php echo e(__('accounting::lang.accounting'), false); ?></a>
            </div>
 
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php if(auth()->user()->can('accounting.manage_accounts')): ?>
                        <li <?php if(request()->segment(2) == 'chart-of-accounts'): ?> class="active" <?php endif; ?>><a href="<?php echo e(action([\Modules\Accounting\Http\Controllers\CoaController::class, 'index']), false); ?>"><?php echo app('translator')->get('accounting::lang.chart_of_accounts'); ?></a></li>
                    <?php endif; ?>
                     
                    <?php if(auth()->user()->can('accounting.view_journal')): ?>
                        <li <?php if(request()->segment(2) == 'journal-entry' && request()->get('type') == 'journal_entry'): ?>  class="active" <?php endif; ?>>
                        <a href="<?php echo e(action([\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'index'], ['type' => 'journal_entry']), false); ?>"><?php echo app('translator')->get('accounting::lang.journal_entry_1'); ?></a></li>
                    <?php endif; ?>
 
                    <?php if(auth()->user()->can('accounting.view_journal')): ?>
                        <li <?php if(request()->segment(2) == 'journal-entry' && request()->get('type') == 'journal_openning'): ?> class="active" <?php endif; ?>>
                        <a href="<?php echo e(action([\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'journalOpenning'], ['type' => 'journal_openning']), false); ?>"><?php echo app('translator')->get('accounting::lang.journal_entry_2'); ?></a></li>
                    <?php endif; ?>

                    <?php if(auth()->user()->can('accounting.view_journal')): ?>
                        <li <?php if(request()->segment(2) == 'journal-entry' && request()->get('type') == 'journal_company_general'): ?> class="active" <?php endif; ?>>
                        <a href="<?php echo e(action([\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'journalCompanyGeneral'], ['type' => 'journal_company_general']), false); ?>"><?php echo app('translator')->get('accounting::lang.journal_entry_3'); ?></a></li>
                    <?php endif; ?>
                    
                    <!-- <?php if(auth()->user()->can('accounting.view_transfer')): ?>
                        <li <?php if(request()->segment(2) == 'transfer'): ?> class="active" <?php endif; ?>>
                            <a href="<?php echo e(action([\Modules\Accounting\Http\Controllers\TransferController::class, 'index']), false); ?>">
                                <?php echo app('translator')->get('accounting::lang.transfer'); ?>
                            </a>
                        </li>
                    <?php endif; ?> -->

                    <li <?php if(request()->segment(2) == 'transactions'): ?> class="active" <?php endif; ?>><a href="<?php echo e(action([\Modules\Accounting\Http\Controllers\TransactionController::class, 'index']), false); ?>"><?php echo app('translator')->get('accounting::lang.transactions'); ?></a></li>

                    <?php if(auth()->user()->can('accounting.manage_budget')): ?>
                        <li <?php if(request()->segment(2) == 'budget'): ?> class="active" <?php endif; ?>>
                            <a href="<?php echo e(action([\Modules\Accounting\Http\Controllers\BudgetController::class, 'index']), false); ?>">
                                <?php echo app('translator')->get('accounting::lang.budget'); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(auth()->user()->can('accounting.view_reports')): ?>
                    <li <?php if(request()->segment(2) == 'reports'): ?> class="active" <?php endif; ?>><a href="<?php echo e(action([\Modules\Accounting\Http\Controllers\ReportController::class, 'index']), false); ?>">
                        <?php echo app('translator')->get('accounting::lang.reports'); ?>
                    </a></li>
                    <?php endif; ?>

                    <li <?php if(request()->segment(2) == 'settings'): ?> class="active" <?php endif; ?>><a href="<?php echo e(action([\Modules\Accounting\Http\Controllers\SettingsController::class, 'index']), false); ?>"><?php echo app('translator')->get('messages.settings'); ?></a></li>
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section><?php /**PATH C:\xampp\htdocs\pos\Modules\Accounting\Providers/../Resources/views/layouts/nav.blade.php ENDPATH**/ ?>