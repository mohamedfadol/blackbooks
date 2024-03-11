<?php

namespace App;

use App\Utils\Util;
use App\BusinessLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MainAccount extends Model
{
    use HasFactory;

    protected $table = 'main_accounts';
    protected $guarded = ['id'];


    public static function forDropdownAcc($business_id, $prepend_none, $closed = false, $show_balance = false)
    {
        $query = MainAccount::where('business_id', $business_id)->whereNotNull('parent_id')
                                ->whereHas('child_accounts');

        $permitted_locations = auth()->user()->permitted_locations();
        $account_ids = [];
        if ($permitted_locations != 'all') {
            $locations = BusinessLocation::where('business_id', $business_id)
                            ->whereIn('id', $permitted_locations)
                            ->get();

            foreach ($locations as $location) {
                if (! empty($location->default_payment_accounts)) {
                    $default_payment_accounts = json_decode($location->default_payment_accounts, true);
                    foreach ($default_payment_accounts as $key => $account) {
                        if (! empty($account['is_enabled']) && ! empty($account['account'])) {
                            $account_ids[] = $account['account'];
                        }
                    }
                }
            }

            $account_ids = array_unique($account_ids);
        }

        if ($permitted_locations != 'all') {
            $query->whereIn('main_accounts.id', $account_ids);
        }

        $can_access_account = auth()->user()->can('account.access');
        if ($can_access_account && $show_balance) {
            // $query->leftjoin('account_transactions as AT', function ($join) {
            //     $join->on('AT.account_id', '=', 'accounts.id');
            //     $join->whereNull('AT.deleted_at');
            // })
            $query->select('main_accounts.name_ar',
                    'main_accounts.id',
                    DB::raw("(SELECT SUM( IF(account_transactions.type='credit', amount, -1*amount) ) as balance from account_transactions where account_transactions.account_id = main_accounts.id AND deleted_at is NULL) as balance")
                );
        }

        if (! $closed) {
            $query->where('is_closed', 0);
        }

        $accounts = $query->get();

        $dropdown = [];
        if ($prepend_none) {
            $dropdown[''] = __('lang_v1.none');
        }

        $commonUtil = new Util;
        foreach ($accounts as $account) {
            $name = $account->name_ar;

            if ($can_access_account && $show_balance) {
                $name .= ' ('.__('lang_v1.balance').': '.$commonUtil->num_f($account->balance).')';
            }

            $dropdown[$account->id] = $name;
        }

        return $dropdown;
    }

    
    // get all parent account and get root 
    public static function tree()
    {
        $business_id = request()->session()->get('user.business_id');
        $allaccounts = MainAccount::where('business_id',$business_id)
        ->withCount(['accountingAccountsTransactions as balance' => function($query) {
            $query->select(DB::raw("SUM(IF(type='credit', amount, -1 * amount)) AS balance"));
        }
        ])->get();
        $rootAccounts = $allaccounts->whereNull('parent_id');
        self::formatTree($rootAccounts, $allaccounts);
        return $rootAccounts;
    }

    // loop through root of childern
    private static function formatTree($accounts, $allaccounts)
    {
        foreach ($accounts as $account) {
            $account->children = $allaccounts->where('parent_id', $account->id)->values();
            $account->totalBalance = $account->balance;
            if ($account->children->isNotEmpty()) {
                self::calculateTotalBalance($account->children, $allaccounts);
                self::formatTree($account->children, $allaccounts);
                $account->totalBalance += $account->children->sum('totalBalance');
            }
        }
    }

    // Initialize totalBalance with the current account's balance
    private static function calculateTotalBalance($accounts, $allaccounts)
    {
        foreach ($accounts as $account) {
            $account->totalBalance = $account->balance; 

            if ($account->children) {
                self::calculateTotalBalance($account->children, $allaccounts);
                $account->totalBalance += $account->children->sum('totalBalance');
            }
        }
    }

     // Transactions relationship
    public function accountingAccountsTransactions() {
        return $this->hasMany(\Modules\Accounting\Entities\AccountingAccountsTransaction::class, 'accounting_account_id');
    }

    // Recursive function to calculate sums
    public function calculateSum(&$account) {
        // Initialize sums
        $creditSum = $account->accountingAccountsTransactions->where('type', 'credit')->sum('amount');
        $debitSum = $account->accountingAccountsTransactions->where('type', 'debit')->sum('amount');
    
        // Calculate sums for child accounts
        foreach ($account->child_accounts as $child) {
            [$childCredit, $childDebit] = self::calculateSum($child);
            $creditSum += $childCredit;
            $debitSum += $childDebit;
        }
    
        // Optional: store sums in the account object if you want to use them later
        $account->credit_balance = $creditSum;
        $account->debit_balance = $debitSum;
    
        return [$creditSum, $debitSum];
    }
 

    public function isChild(): bool
    {
            return $this->parent_id !== null;
    }

    public function isParent() {
            return $this->hasMany(MainAccount::class, 'parent_id', 'id');
    }

    public function child_accounts() {
        return $this->hasMany(MainAccount::class, 'parent_id', 'id');
    }
    

    // Relationship to child accounts
    public function childAccounts()
    {
        return $this->hasMany(MainAccount::class, 'parent_id', 'id');
    }

    // Relationship to parent account
    public function parentAccount()
    {
        return $this->belongsTo(MainAccount::class, 'parent_id', 'id');
    }

    
    public function sumBalanceOfChildren() {
        $business_id = request()->session()->get('user.business_id');
        $sum = MainAccount::where('business_id',$business_id)->where('parent_id', $this->id)
                ->with('accountingAccountsTransactions')
                ->withCount(['accountingAccountsTransactions as balance' => function($query) {
                $query->select(DB::raw("SUM(IF(type='credit', amount, -1 * amount)) AS balance"));
                }])->get();
        return $sum->sum('balance');
    }

    public function sumBalanceOfChildrenTwo() {
        $business_id = request()->session()->get('user.business_id');
        $parentsAccIds = MainAccount::with('child_accounts')->where('business_id',$business_id)->whereNull('parent_id')->pluck('id');
        $sum = MainAccount::where('business_id',$business_id)->whereIn('parent_id', $parentsAccIds)
                ->with('accountingAccountsTransactions')
                ->withCount(['accountingAccountsTransactions as balance' => function($query) {
                $query->select(DB::raw("SUM(IF(type='credit', amount, -1 * amount)) AS balance"));
                }])->get();
        return $sum->sum('balance');
    }

    /**
     * main Accounts Dropdown 
     *
     * @param  int  $business_id
     * @return array
    */
    public static function forDropdown($business_id, $with_data = false, $q = '')
    {
        $query = MainAccount::where('business_id', $business_id)->whereNotNull('parent_id')
                            ->whereDoesntHave('child_accounts')
                            ->where('status', 'active');
        // Apply search query if needed
        if ($with_data && !empty($q)) {
            $query->where(function ($query) use ($q) {
                $query->where('name_ar', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%")
                    ->orWhere('account_number', 'like', "%{$q}%");
            });
        }

        if ($with_data) {
            return $query->get();
        } else {
            // Corrected usage for collecting multiple attributes
            return $query->get()->mapWithKeys(function ($item) {
                return [$item->id => [
                    'name_ar' => $item->name_ar, 
                    'name_en' => $item->name_en, 
                    'account_number' => $item->account_number
                ]];
            });
        }
    }

    public static  function getLastDigit($serialNumber) {
        // Convert the serial number to a string
        $serialString = (string)$serialNumber;
        // Get the last character (digit)
        $lastDigit = substr($serialString, -1);
        return (int)$lastDigit;
    }
    
    public static function createNewAccount($input) {
           
            // if (! (auth()->user()->can('superadmin') ||
            //     $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            //     ! (auth()->user()->can('accounting.manage_accounts'))) {
            //     abort(403, 'Unauthorized action.');
            // }
            try {
                $business_id = $input['business_id'];
                $parentAccount = MainAccount::where('business_id',$business_id)->find($input['account_id']);
                $account = new MainAccount() ;
                $account->name_en = $input['name_en'] ?? null;
                $account->name_ar = $input['name'];
                $account->account_category_id = $parentAccount->account_category_id;
                $account->financial_statement_id = $parentAccount->financial_statement_id;
                $account->parent_id = $input['account_id'];
                $account->contact_id = $input['contact_id_for_account'];
                $account->created_by = auth()->user()->id;
                $account->business_id = $input['business_id'];
                $account->status = 'active';
     
                if(!empty($parentAccount->account_number) ){
                    $parentAccountCildren = MainAccount::where('parent_id',$input['account_id'])->where('business_id',$business_id)->count();
                    if ($parentAccountCildren >= 1) {
                        $parentChildren =  MainAccount::where('parent_id',$parentAccount->id)->where('business_id',$business_id)->latest('account_number')->first();
                        $parent_children_account_number = $parentChildren->account_number;
                        $lastDigit = MainAccount::getLastDigit($parent_children_account_number);
                        $plusOldNumber = (int) ($lastDigit + 1);
                        $account->account_number =  "$parentAccount->account_number$plusOldNumber";
                        // dd('yes');
                    }else{
                        $parent_children_account_number = $parentAccount->account_number;
                        $plusOldNumber = 1;
                        $account->account_number =  "$parent_children_account_number$plusOldNumber";
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
            } catch (\Exception $e) {
    
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            }
    }


}
