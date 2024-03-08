<?php

namespace Modules\Accounting\Http\Controllers;

use DB;
use App\MainAccount;
use App\AccountCategory;
use App\Utils\ModuleUtil;
use App\FinancialStatement;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Accounting\Utils\AccountingUtil;
use Modules\Accounting\Entities\AccountingAccount;
use Modules\Accounting\Entities\AccountingAccountType;
use Modules\Accounting\Entities\AccountingAccountsTransaction;

class CoaController extends Controller
{
    protected $accountingUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(AccountingUtil $accountingUtil, ModuleUtil $moduleUtil)
    {
        $this->accountingUtil = $accountingUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }

        $balance_formula = $this->accountingUtil->balanceFormula('AA');
        $account_types = MainAccount::tree();
        
        if (request()->ajax()) {
            
            $account_exist = MainAccount::where('business_id', $business_id)->exists();

            if (request()->input('view_type') == 'table') {
                return view('accounting::chart_of_accounts.accounts_table')
                        ->with(compact('account_types', 'account_exist'));
            } else {
                
                return view('accounting::chart_of_accounts.accounts_tree')
                ->with(compact('account_exist', 'account_types'));
            }
        }

        return view('accounting::chart_of_accounts.index')->with(compact('account_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            // $account_types = AccountingAccountType::accounting_primary_type();
            $account_types = MainAccount::where('business_id',$business_id)->whereDoesntHave('accountingAccountsTransactions')->orderBy('id','DESC')->get();
            $accountCategories = AccountCategory::where('business_id',$business_id)->orderBy('id','DESC')->get();
            $financialStatements = FinancialStatement::where('business_id',$business_id)->orderBy('id','DESC')->get();
            return view('accounting::chart_of_accounts.create')->with(compact('account_types','accountCategories','financialStatements'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function createDefaultAccounts()
    {
        //check no accounts
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = request()->session()->get('user.id');

        $default_accounts = [
            0 => [
                'name' => 'Accounts Payable (A/P)',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 6,
                'detail_type_id' => 58,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            1 => [
                'name' => 'Credit Card',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 7,
                'detail_type_id' => 59,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            2 => [
                'name' => 'Wage expenses',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 140,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            3 => [
                'name' => 'Utilities',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 149,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            4 => [
                'name' => 'Unrealised loss on securities, net of tax',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 12,
                'detail_type_id' => 113,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            5 => [
                'name' => 'Undeposited Funds',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 2,
                'detail_type_id' => 29,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            6 => [
                'name' => 'Uncategorised Income',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 11,
                'detail_type_id' => 103,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            7 => [
                'name' => 'Uncategorised Expense',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 138,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            8 => [
                'name' => 'Uncategorised Asset',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 2,
                'detail_type_id' => 26,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            9 => [
                'name' => 'Unapplied Cash Payment Income',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 11,
                'detail_type_id' => 105,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            10 => [
                'name' => 'Travel expenses - selling expense',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => '147',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            11 => [
                'name' => 'Travel expenses - general and admin expenses',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => '146',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            12 => [
                'name' => 'Supplies',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 145,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            13 => [
                'name' => 'Subcontractors - COS',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 13,
                'detail_type_id' => '114',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            14 => [
                'name' => 'Stationery and printing',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => '137',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            15 => [
                'name' => 'Short-term debit',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 8,
                'detail_type_id' => 69,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            16 => [
                'name' => 'Shipping and delivery expense',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 143,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            17 => [
                'name' => 'Share capital',
                'business_id' => $business_id,
                'account_primary_type' => 'equity',
                'account_sub_type_id' => 10,
                'detail_type_id' => 95,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            18 => [
                'name' => 'Sales of Product Income',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 11,
                'detail_type_id' => 103,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            19 => [
                'name' => 'Sales - wholesale',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 11,
                'detail_type_id' => '102',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            20 => [
                'name' => 'Sales - retail',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 11,
                'detail_type_id' => '101',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            21 => [
                'name' => 'Sales',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 11,
                'detail_type_id' => 103,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            22 => [
                'name' => 'Revenue - General',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 11,
                'detail_type_id' => '100',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            23 => [
                'name' => 'Retained Earnings',
                'business_id' => $business_id,
                'account_primary_type' => 'equity',
                'account_sub_type_id' => 10,
                'detail_type_id' => 94,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            24 => [
                'name' => 'Repair and maintenance',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 142,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            25 => [
                'name' => 'Rent or lease payments',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 141,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            26 => [
                'name' => 'Reconciliation Discrepancies',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 15,
                'detail_type_id' => 153,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            27 => [
                'name' => 'Purchases',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 144,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            28 => [
                'name' => 'Property, plant and equipment',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 4,
                'detail_type_id' => 42,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            29 => [
                'name' => 'Prepaid Expenses',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 2,
                'detail_type_id' => 27,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            30 => [
                'name' => 'Payroll liabilities',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 8,
                'detail_type_id' => 71,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            31 => [
                'name' => 'Payroll Expenses',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 140,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            32 => [
                'name' => 'Payroll Clearing',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 8,
                'detail_type_id' => 70,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            33 => [
                'name' => 'Overhead - COS',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 13,
                'detail_type_id' => '114',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            34 => [
                'name' => 'Other Types of Expenses-Advertising Expenses',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => '119',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            35 => [
                'name' => 'Other selling expenses',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 139,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            36 => [
                'name' => 'Other operating income (expenses)',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 12,
                'detail_type_id' => 111,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            37 => [
                'name' => 'Other general and administrative expenses',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => '137',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            38 => [
                'name' => 'Other comprehensive income',
                'business_id' => $business_id,
                'account_primary_type' => 'equity',
                'account_sub_type_id' => 10,
                'detail_type_id' => 87,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            39 => [
                'name' => 'Other - COS',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 13,
                'detail_type_id' => '114',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            40 => [
                'name' => 'Office expenses',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => '137',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            41 => [
                'name' => 'Meals and entertainment',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 137,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            42 => [
                'name' => 'Materials - COS',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 13,
                'detail_type_id' => '114',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            43 => [
                'name' => 'Management compensation',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 135,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            44 => [
                'name' => 'Loss on disposal of assets',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 12,
                'detail_type_id' => 108,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            45 => [
                'name' => 'Loss on discontinued operations, net of tax',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 134,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            46 => [
                'name' => 'Long-term investments',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 5,
                'detail_type_id' => 54,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            47 => [
                'name' => 'Long-term debt',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 9,
                'detail_type_id' => 78,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            48 => [
                'name' => 'Liabilities related to assets held for sale',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 9,
                'detail_type_id' => 77,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            49 => [
                'name' => 'Legal and professional fees',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 133,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            50 => [
                'name' => 'Inventory Asset',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 2,
                'detail_type_id' => 21,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            51 => [
                'name' => 'Inventory',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 2,
                'detail_type_id' => 21,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            52 => [
                'name' => 'Interest income',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 12,
                'detail_type_id' => 107,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            53 => [
                'name' => 'Interest expense',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 132,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            54 => [
                'name' => 'Intangibles',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 5,
                'detail_type_id' => 51,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            55 => [
                'name' => 'Insurance - Liability',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 131,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            56 => [
                'name' => 'Insurance - General',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 131,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            57 => [
                'name' => 'Insurance - Disability',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 131,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            58 => [
                'name' => 'Income tax payable',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 8,
                'detail_type_id' => 65,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            59 => [
                'name' => 'Income tax expense',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 130,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            60 => [
                'name' => 'Goodwill',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 5,
                'detail_type_id' => 50,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            61 => [
                'name' => 'Freight and delivery - COS',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 13,
                'detail_type_id' => '114',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            62 => [
                'name' => 'Equity in earnings of subsidiaries',
                'business_id' => $business_id,
                'account_primary_type' => 'equity',
                'account_sub_type_id' => 10,
                'detail_type_id' => 84,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            63 => [
                'name' => 'Equipment rental',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 128,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            64 => [
                'name' => 'Dues and Subscriptions',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 127,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            65 => [
                'name' => 'Dividends payable',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 8,
                'detail_type_id' => 64,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            66 => [
                'name' => 'Dividend income',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 12,
                'detail_type_id' => 106,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            67 => [
                'name' => 'Dividend disbursed',
                'business_id' => $business_id,
                'account_primary_type' => 'equity',
                'account_sub_type_id' => 10,
                'detail_type_id' => 83,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            68 => [
                'name' => 'Discounts given - COS',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 13,
                'detail_type_id' => '114',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            69 => [
                'name' => 'Direct labour - COS',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 13,
                'detail_type_id' => '114',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            70 => [
                'name' => 'Deferred tax assets',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 5,
                'detail_type_id' => 49,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            71 => [
                'name' => 'Cost of sales',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 13,
                'detail_type_id' => '118',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            72 => [
                'name' => 'Commissions and fees',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 125,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            73 => [
                'name' => 'Change in inventory - COS',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 13,
                'detail_type_id' => '114',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            74 => [
                'name' => 'Cash and cash equivalents',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 3,
                'detail_type_id' => 31,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            75 => [
                'name' => 'Billable Expense Income',
                'business_id' => $business_id,
                'account_primary_type' => 'income',
                'account_sub_type_id' => 11,
                'detail_type_id' => 103,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            76 => [
                'name' => 'Bank charges',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 123,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            77 => [
                'name' => 'Bad debts',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 122,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            78 => [
                'name' => 'Available for sale assets (short-term)',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 2,
                'detail_type_id' => 18,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            79 => [
                'name' => 'Assets held for sale',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 5,
                'detail_type_id' => 48,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            80 => [
                'name' => 'Amortisation expense',
                'business_id' => $business_id,
                'account_primary_type' => 'expenses',
                'account_sub_type_id' => 14,
                'detail_type_id' => 120,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            81 => [
                'name' => 'Allowance for bad debts',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 2,
                'detail_type_id' => 17,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            82 => [
                'name' => 'Accumulated depreciation on property, plant and equipment',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 4,
                'detail_type_id' => 38,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            83 => [
                'name' => 'Accrued non-current liabilities',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 9,
                'detail_type_id' => 76,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            84 => [
                'name' => 'Accrued liabilities',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 8,
                'detail_type_id' => 60,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            85 => [
                'name' => 'Accrued holiday payable',
                'business_id' => $business_id,
                'account_primary_type' => 'liability',
                'account_sub_type_id' => 9,
                'detail_type_id' => 75,
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
            86 => [
                'name' => 'Accounts Receivable (A/R)',
                'business_id' => $business_id,
                'account_primary_type' => 'asset',
                'account_sub_type_id' => 1,
                'detail_type_id' => '16',
                'status' => 'active',
                'created_by' => $user_id,
                'created_at' => \Carbon::now(),
                'updated_at' => \Carbon::now(),
            ],
        ];

        if (AccountingAccount::where('business_id', $business_id)->doesntExist()) {
            AccountingAccount::insert($default_accounts);
        }

        //redirect back
        $output = ['success' => 1,
            'msg' => __('lang_v1.added_success'),
        ];

        return redirect()->back()->with('status', $output);
    }


    public function getLastDigit($serialNumber) {
        // Convert the serial number to a string
        $serialString = (string)$serialNumber;
        // Get the last character (digit)
        $lastDigit = substr($serialString, -1);
        return (int)$lastDigit;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $business_id = $request->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }
        try {
            // DB::beginTransaction();
            $input = $request->only(['name_en','name_ar', 'account_parent', 
                                        'account_number', 'description','account_category','financial_statement']);
            $account = new MainAccount;
            $account->name_en = $input['name_en'] ?? null;
            $account->name_ar = $input['name_ar'];
            $account->account_category_id = $input['account_category'];
            $account->financial_statement_id = $input['financial_statement'];
            
            $account->description = $input['description'];
            $account->created_by = auth()->user()->id;
            $account->business_id = $request->session()->get('user.business_id');
            $account->status = 'active';

            if (empty($input['account_number']) && empty($input['account_parent'])) {
                $nullParentAccountNumber = MainAccount::whereNull('parent_id')->where('business_id',$business_id)->latest('account_number')->first();
                if (!is_null($nullParentAccountNumber)) {
                    $account->account_number =  (int) (++$nullParentAccountNumber->account_number);
                }else{
                    $account->account_number = 1;
                }

            }else if( !empty($input['account_parent']) && empty($input['account_number']) ){
                $account->parent_id = $input['account_parent'];
                
                $parentAccount = MainAccount::where('id',$input['account_parent'])->where('business_id',$business_id)->latest('account_number')->first();
                $parentAccountCildren = MainAccount::where('parent_id',$input['account_parent'])->where('business_id',$business_id)->count();
                if ($parentAccountCildren >= 1) {
                    $parentChildren =  MainAccount::where('parent_id',$parentAccount->id)->where('business_id',$business_id)->latest('account_number')->first();
                    $parent_children_account_number = $parentChildren->account_number;
                    $lastDigit = $this->getLastDigit($parent_children_account_number);
                    $plusOldNumber = (int) ($lastDigit + 1);
                    $account->account_number =  "$parentAccount->account_number$plusOldNumber";
                    // dd('yes');
                }else{
                    $parent_children_account_number = $parentAccount->account_number;
                    $plusOldNumber = 1;
                    $account->account_number =  "$parent_children_account_number$plusOldNumber";
                }                 
            }else if( empty($input['account_parent']) && !empty($input['account_number']) ){
                
                $account_number_count = MainAccount::whereNull($input['account_parent'])
                                                    ->where('business_id',$business_id)
                                                    ->where('account_number','=',$input['account_number'])->count();
                if ($account_number_count >= 1) {
                    return back();
                }else{
                    $account->account_number = $input['account_number'];
                }

            }else{
                
                $nullParentAccountNumber = MainAccount::whereNull('parent_id')->where('business_id',$business_id)->latest('account_number')->first();
                if (!is_null($nullParentAccountNumber)) {
                    $account->account_number =  (int) (++$nullParentAccountNumber->account_number);
                }else{
                    $account->account_number = 1;
                }
            }
            $account->save();
            // DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
        }

        return redirect()->back();
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $account = MainAccount::where('business_id', $business_id)->find($id);
            // $account_types = MainAccount::where('business_id', $business_id)->whereNull('parent_id')->orderBy('id', 'ASC')->get();
            $account_types = MainAccount::where('business_id',$business_id)->whereDoesntHave('accountingAccountsTransactions')->orderBy('id','DESC')->get();
            $accountCategories = AccountCategory::where('business_id',$business_id)->orderBy('id','DESC')->get();
            $financialStatements = FinancialStatement::where('business_id',$business_id)->orderBy('id','DESC')->get();
            return view('accounting::chart_of_accounts.edit')->with(compact('account_types', 'account','accountCategories','financialStatements'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name_en','name_ar', 'account_number','description','account_parent_id','account_category','financial_statement']);
            $account = MainAccount::where('business_id',$business_id)->find($id);
            $account->name_ar = $input['name_ar'];
            $account->name_en = $input['name_en'] ?? null;

            $account->account_category_id = $input['account_category'];
            $account->financial_statement_id = $input['financial_statement'];

            // edit 
            if ($input['account_parent_id'] == $account->id ) {
                return back();
            // end edit
            }
                if (empty($input['account_number']) && empty($input['account_parent_id'])) {
                    $nullParentAccountNumber = MainAccount::whereNull('parent_id')->where('business_id',$business_id)->latest('account_number')->first();
                    if (!is_null($nullParentAccountNumber)) {
                        $account->account_number =  (int) (++$nullParentAccountNumber->account_number);
                        $account->parent_id = null;
                    }else{
                        $account->account_number = 1;
                    }
                }

                if(!empty($input['account_number']) && !empty($input['account_parent_id'])){
                    // dd('kkkkk');
                    $account->parent_id = $input['account_parent_id'];
                    $parentAccount = MainAccount::where('id',$input['account_parent_id'])
                                                ->where('business_id',$business_id)->latest('account_number')->first();
                    $parentAccountCildren = MainAccount::where('parent_id',$input['account_parent_id'])
                                                        ->where('business_id',$business_id)->count();
                    if ($parentAccountCildren >= 1) {
                        $parentChildren =  MainAccount::where('parent_id',$parentAccount->id)
                                                        ->where('business_id',$business_id)->latest('account_number')->first();
                        $parent_children_account_number = $parentChildren->account_number;
                        $lastDigit = $this->getLastDigit($parent_children_account_number);
                        $plusOldNumber = (int) ($lastDigit + 1);
                        $account->account_number =  "$parentAccount->account_number$plusOldNumber";
                    }else{
                        $parent_children_account_number = $parentAccount->account_number;
                        $plusOldNumber = 1;
                        $account->account_number =  "$parent_children_account_number$plusOldNumber";
                    } 
                }

                if( !empty($input['account_parent_id']) && empty($input['account_number']) ){
                    $account->parent_id = $input['account_parent_id'];
                    $parentAccount = MainAccount::where('id',$input['account_parent_id'])
                                                ->where('business_id',$business_id)->latest('account_number')->first();
                    $parentAccountCildren = MainAccount::where('parent_id',$input['account_parent_id'])
                                                        ->where('business_id',$business_id)->count();
                    if ($parentAccountCildren >= 1) {
                        $parentChildren =  MainAccount::where('parent_id',$parentAccount->id)
                                                        ->where('business_id',$business_id)->latest('account_number')->first();
                        $parent_children_account_number = $parentChildren->account_number;
                        $lastDigit = $this->getLastDigit($parent_children_account_number);
                        $plusOldNumber = (int) ($lastDigit + 1);
                        $account->account_number =  "$parentAccount->account_number$plusOldNumber";
                    }else{
                        $parent_children_account_number = $parentAccount->account_number;
                        $plusOldNumber = 1;
                        $account->account_number =  "$parent_children_account_number$plusOldNumber";
                    }                 
                }

                if(empty($input['account_parent_id']) && !empty($input['account_number']) ){
                    $account_number_count = MainAccount::where('business_id',$business_id)->whereNull('parent_id')
                                                        ->where('account_number','=',$input['account_number'])->first();
                    if ($input['account_number'] != $account->account_number) {
                        
                        $nullParentAccountNumber = MainAccount::whereNull('parent_id')->where('business_id',$business_id)->latest('account_number')->first();
                        if (!is_null($nullParentAccountNumber)) {
                            $exsit_number = (int)  $input['account_number'];
                            $account->account_number =  (int) ("$exsit_number$nullParentAccountNumber->account_number");
                        }
                    }    
                }
                $account->save();
        } catch (\Exception $e) {
             
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        // dd('yes');
        $business_id = $request->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $account = MainAccount::where('business_id',$business_id)->where('id',$id)->delete();
            $msg = trans('account.account_delete_successfully');
            $output = ['success' => 1,'msg' => $msg];
        } catch (\Exception $th) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }
        return $output;
    }

    public function activateDeactivate($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $account = MainAccount::where('business_id', $business_id)
                                    ->find($id);

            $account->status = $account->status == 'active' ? 'inactive' : 'active';
            $account->save();

            $msg = $account->status == 'active' ? __('accounting::lang.activated_successfully') :
            __('accounting::lang.deactivated_successfully');
            $output = ['success' => 1,
                'msg' => $msg,
            ];

            return $output;
        }
    }

    /**
     * Displays the ledger of the account
     *
     * @param  int  $account_id
     * @return Response
     */
    public function ledger($account_id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }

        $account = MainAccount::where('business_id', $business_id)
                        ->findorFail($account_id);

        if (request()->ajax()) {
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $transactions = AccountingAccountsTransaction::where('accounting_account_id', $account->id)
                            ->leftjoin('accounting_acc_trans_mappings as ATM', 'accounting_accounts_transactions.acc_trans_mapping_id', '=', 'ATM.id')
                            ->leftjoin('transactions as T', 'accounting_accounts_transactions.transaction_id', '=', 'T.id')
                            ->leftjoin('users AS U', 'accounting_accounts_transactions.created_by', 'U.id')
                            ->select('accounting_accounts_transactions.operation_date','accounting_accounts_transactions.type',
                            'ATM.original',   'ATM.ref_no', 'accounting_accounts_transactions.note', 'accounting_accounts_transactions.amount',
                                DB::raw("CONCAT(COALESCE(U.surname, ''),' ',COALESCE(U.first_name, ''),' ',COALESCE(U.last_name,'')) as added_by"),
                                'T.invoice_no', 
                                //DB::raw("SUM( IF(accounting_accounts_transactions.type='debit' OR (accounting_accounts_transactions.type='debit'), amount, -1*amount)) as balanceT")
                            ) ;
            if (! empty($start_date) && ! empty($end_date)) {
                $transactions->whereDate('accounting_accounts_transactions.operation_date', '>=', $start_date)
                        ->whereDate('accounting_accounts_transactions.operation_date', '<=', $end_date);
            }

            return DataTables::of($transactions)
                    ->editColumn('operation_date', function ($row) {
                        return $this->accountingUtil->format_date($row->operation_date, true);
                    })
                     
                    ->addColumn('debit', function ($row) {
                        if ($row->type == 'debit') {
                            return '<span class="debit" data-orig-value="'.$row->amount.'"> '. session("currency")["symbol"] .' '. number_format($row->amount,3,".","").'</span>';
                        }

                        return '';
                    })
                    ->addColumn('credit', function ($row) {
                        if ($row->type == 'credit') {
                            return '<span class="credit"  data-orig-value="'.$row->amount.'"> '. session("currency")["symbol"] .' '. number_format($row->amount,3,".","").'</span>';
                        }

                        return '';
                    })
                    ->addColumn('balance', function ($row) {
                        $balanceT = 0;
                        if ($row->type == 'debit') {
                            $balanceT += $row->amount;
                        }else {
                            $balanceT +=  -1 * $row->amount;
                        }
                        return  $balanceT ;
                    })

                    
                    ->filterColumn('added_by', function ($query, $keyword) {
                        $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                    })
                    ->rawColumns(['ref_no', 'credit', 'debit', 'balance'])
                    ->make(true);
        } 

        $current_bal = MainAccount::with('child_accounts')->leftjoin('accounting_accounts_transactions as AAT',
                            'AAT.accounting_account_id', '=', 'main_accounts.id')
                        ->where('business_id', $business_id)
                        ->where('main_accounts.id', $account->id)
                        ->select([ 
                        DB::raw(
                            "SUM( IF(AAT.type='debit' OR (AAT.type='debit'), amount, -1*amount)) as balance"
                        )]);
        $current_bal = $current_bal->first()->balance;

        return view('accounting::chart_of_accounts.ledger')
            ->with(compact('account', 'current_bal'));
    }
}
