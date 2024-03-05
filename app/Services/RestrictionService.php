<?php
namespace App\Services;

use DB;
use App\TaxRate;
use Carbon\Carbon;
use App\Utils\Util;
use App\MainAccount;
use App\Transaction;
use App\BusinessLocation;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\TransactionPayment;
use Illuminate\Http\Request;
use Modules\Accounting\Utils\AccountingUtil;
use Modules\Accounting\Entities\AccountingAccTransMapping;
use Modules\Accounting\Entities\AccountingAccountsTransaction;

class RestrictionService  {


        /**
     * All Utils instance.
     */

    /**
     * Constructor
     *
     * @return void
     */ 
    public function __construct(Util $util, AccountingUtil $accountingUtil, ContactUtil $contactUtil, ProductUtil $productUtil,)
    {
        $this->productUtil = $productUtil;
        $this->contactUtil = $contactUtil;
        $this->util = $util;
        $this->accountingUtil = $accountingUtil;
    }

    
    public function create($input, $transactionId ,$user_id, $business_id, $deposit_to, $payment_account) {
        try {
                    DB::beginTransaction();
                    $now = Carbon::now();
                    $journal_date = Carbon::createFromTimestamp(strtotime($now))->format('Y-m-d H:i:s');
                    $accounting_settings = $this->accountingUtil->getAccountingSettings($business_id);
                    $ref_no = '';
                    $ref_count = $this->util->setAndGetReferenceCount('journal_entry');
                    if (empty($ref_no)) {
                        $prefix = ! empty($accounting_settings['journal_entry_prefix']) ? $accounting_settings['journal_entry_prefix'] : '';
                        //Generate reference number
                        $ref_no = $this->util->generateReferenceNumber('journal_entry', $ref_count, $business_id, $prefix);
                    } 

                    if (empty(request('transaction_date'))) { 
                        $input['transaction_date'] = \Carbon::now();
                    } else {
                        $input['transaction_date'] = $this->productUtil->uf_date(request('transaction_date'), true);
                    }

                    $acc_trans_mapping = new AccountingAccTransMapping();
                    $acc_trans_mapping->business_id = $business_id;
                    $acc_trans_mapping->ref_no = $ref_no;
                    $acc_trans_mapping->original = $input['saleType'];
                    $acc_trans_mapping->created_by = $user_id;
                    $acc_trans_mapping->operation_date = $input['transaction_date'] ??  $journal_date;
                    $acc_trans_mapping->save();

                    $this->saveMap($input, $transactionId, $user_id, $business_id, $deposit_to, $acc_trans_mapping->id,  $payment_account);
                    DB::commit();
                    $output = ['success' => true,'msg' => __('lang_v1.updated_success'),];
            } catch (\Exception $e) {
                print_r($e->getMessage());
                exit;
                DB::rollBack();
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            }
    }

    public function calculateTax($unit_price_inc_tax, $quantity , $tax_amount) {
        
        $taxAmount = 0;
        $total_price = 0;
        if(!empty($unit_price_inc_tax)){
            $total_price +=  $unit_price_inc_tax * $quantity; 
            $taxAmount += $quantity * $tax_amount ;
        }
            // dd($taxAmount);
        return $taxAmount;
    }


    public function postExpense($input, $deposit_from, $user_id, $business_id,  $deposit_to) {
        try {
            DB::beginTransaction();
        $location = BusinessLocation::where('business_id', $business_id)->find($input['location_id']);
        $default_payment_accounts = !empty($location->default_payment_accounts) ? json_decode($location->default_payment_accounts, true) : [];
        $accounting_default_map = !empty($location->accounting_default_map) ? json_decode($location->accounting_default_map, true) : [];
        $default_payment_account = $default_payment_accounts[$input['pay_method']]['account'];
        $tax_sum = array();
        $tax_account_account = array();
        $tax_data = array();
        $net_amount_tax = 0;
        $tax_sale_sum = 0 ; 
        $sumTAX = 0 ;

        $now = Carbon::now();
        $journal_date = Carbon::createFromTimestamp(strtotime($now))->format('Y-m-d H:i:s');
        $accounting_settings = $this->accountingUtil->getAccountingSettings($business_id);
        $ref_no = '';
        $ref_count = $this->util->setAndGetReferenceCount('journal_entry');
        if (empty($ref_no)) {
            $prefix = ! empty($accounting_settings['journal_entry_prefix']) ? $accounting_settings['journal_entry_prefix'] : '';
            //Generate reference number
            $ref_no = $this->util->generateReferenceNumber('journal_entry', $ref_count, $business_id, $prefix);
        } 

        if (empty(request('transaction_date'))) { 
            $input['transaction_date'] = \Carbon::now();
        } else {
            $input['transaction_date'] = $this->productUtil->uf_date(request('transaction_date'), true);
        }

        $acc_trans_mapping = new AccountingAccTransMapping();
        $acc_trans_mapping->business_id = $business_id;
        $acc_trans_mapping->ref_no = $ref_no;
        $acc_trans_mapping->original = 'expense';
        $acc_trans_mapping->created_by = $user_id;
        $acc_trans_mapping->operation_date = $journal_date;
        $acc_trans_mapping->save();


        if (!empty($input['tax_id'])) {
            $accId =  TaxRate::find($input['tax_id']); 
            $tax_value = $accId->amount / 100 + 1;
            $tax_v = $accId->amount / 100;
            $net_amount_tax =  $input['final_total'] /  $tax_value  ;  
            $tax_sum['taxs'][] = ['tax_account_id' =>  $accId->account_id, 'tax_amount' => $accId->amount];
              
        }

        if (!empty($tax_sum['taxs'])) {
            foreach ($tax_sum['taxs'] as $value) {
                // dd($value['tax_amount']);
               // tax_account that sale
                $tax_account_account = [
                    'accounting_account_id' => $value['tax_account_id'],
                    'acc_trans_mapping_id' => $acc_trans_mapping->id,
                    'amount' => $value['tax_amount'],
                    'type' => 'debit',
                    'sub_type' => 'expense',
                    'map_type' => 'payment_account',
                    'created_by' => $user_id,
                    'operation_date' => \Carbon::now(),
                ]; 
            }

        
        }
 
       
        if (!empty($input['contact_id'])) {
            // dd($input['contact_id']);
            // customer that sale
            $employees_account = [
                'accounting_account_id' => $deposit_from,
                'acc_trans_mapping_id' => $acc_trans_mapping->id,
                'amount' => !empty($tax_sum['taxs']) ? $net_amount_tax : $input['final_total'],
                'type' => 'debit',
                'sub_type' => 'expense',
                'map_type' => 'deposit_to',
                'created_by' => $user_id,
                'operation_date' => \Carbon::now(),
            ];
            
            // like sandok
            $contact_account = [
                'accounting_account_id' => $deposit_to,
                'acc_trans_mapping_id' => $acc_trans_mapping->id,
                'amount' => $input['final_total'],
                'type' => 'credit',
                'sub_type' => 'expense',
                'map_type' => 'deposit_to',
                'created_by' => $user_id,
                'operation_date' => \Carbon::now(),
            ];

            if (!empty($input['pay_amount']) || $input['pay_amount'] > 0) {
            
                // customer that sale
                $contact_account_debit = [
                    'accounting_account_id' => $deposit_to,
                    'acc_trans_mapping_id' => $acc_trans_mapping->id,
                    'amount' => $input['pay_amount'],
                    'type' => 'debit',
                    'sub_type' => 'expense',
                    'map_type' => 'deposit_to',
                    'created_by' => $user_id,
                    'operation_date' => \Carbon::now(),
                ];
                
                // like sandok
                $default_payment_account_array = [
                    'accounting_account_id' => $default_payment_account,
                    'acc_trans_mapping_id' => $acc_trans_mapping->id,
                    'amount' => $input['pay_amount'],
                    'type' => 'credit',
                    'sub_type' => 'expense',
                    'map_type' => 'deposit_to',
                    'created_by' => $user_id,
                    'operation_date' => \Carbon::now(),
                ];
            }


        }

        if (!empty($input['contact_id'])) {
            if (!empty($tax_sum['taxs'])) {
                AccountingAccountsTransaction::create($tax_account_account);
            }
            AccountingAccountsTransaction::create($employees_account);
            AccountingAccountsTransaction::create($contact_account);
            
            if ($input['pay_amount'] > 0) {
                AccountingAccountsTransaction::create($contact_account_debit);
                AccountingAccountsTransaction::create($default_payment_account_array);
            }
        }

        DB::commit();
        $output = ['success' => true,'msg' => __('lang_v1.updated_success'),];
        } catch (\Exception $e) {
            print_r($e->getMessage());
            exit;
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
        }
                        
    }


    /** 
     * Function to save a mapping
    */   
    public function saveMap($input, $id, $user_id, $business_id, $customer, $acc_trans_mapping , $payment_account){
        try {
                DB::beginTransaction();
                $location = BusinessLocation::where('business_id', $business_id)->find($input['location_id']);
                $default_payment_accounts = !empty($location->default_payment_accounts) ? json_decode($location->default_payment_accounts, true) : [];
                $accounting_default_map = !empty($location->accounting_default_map) ? json_decode($location->accounting_default_map, true) : [];
                $transaction = Transaction::where('business_id', $business_id)->where('id', $id)->firstorFail();
                $tax_sum = array();
                $product_information = array();
                $tax_account_account = array();
                $tax_data = array();
                $sum_amount_per_line = 0;
                $tax_sale_sum = 0 ; 
                $sumTAX = 0 ; 
                $type = $input['saleType'] == 'sale' ? 'sell' : 'purchases' ;

                if ($input['saleType'] == 'sale') {
                    foreach ($input['products'] as $product) {
                        if (!empty($product['tax_id'])) {
                           $accId =  TaxRate::where('id',$product['tax_id'])->first()->account_id; 
                           $sum_amount_per_line =  $this->calculateTax($product['unit_price_inc_tax'], $product['quantity'] , $product['item_tax']);
                            $tax_sum['taxs'][] = ['tax_account_id' =>  $accId, 'tax_amount' => $sum_amount_per_line];
                            $sumTAX += $sum_amount_per_line;
                        }
                    }
                }
                
               
                if($input['saleType'] == 'purchases'){
                    foreach ($input['purchases'] as $product) {
                        if (!empty($product['purchase_line_tax_id'])) {
                           $accId =  TaxRate::where('id',$product['purchase_line_tax_id'])->first()->account_id; 
                           $sum_amount_per_line =  $this->calculateTax($product['purchase_price_inc_tax'], $product['quantity'] , $product['item_tax']);
                            $tax_sum['taxs'][] = ['tax_account_id' =>  $accId, 'tax_amount' => $sum_amount_per_line];
                            $sumTAX += $sum_amount_per_line;
                                                                
                        }
                        
                    }
                }
                
                if (!empty($tax_sum['taxs'])) {
                    foreach ($tax_sum['taxs'] as $value) {
                        $payT = ($input['saleType'] == 'sale') ? 'credit' : 'debit';
                       // tax_account that sale
                        $tax_data = [
                            'accounting_account_id' => $value['tax_account_id'],
                            'transaction_id' => $id,
                            'acc_trans_mapping_id' => $acc_trans_mapping,
                            'transaction_payment_id' => null,
                            'amount' => $value['tax_amount'],
                            'type' => $payT,
                            'sub_type' => $type,
                            'map_type' => 'deposit_to',
                            'created_by' => $user_id,
                            'operation_date' => $input['transaction_date'] ?? $input['transaction_date'] ?? \Carbon::now(),
                        ];
                        array_push($tax_account_account, $tax_data);
                    }

                    $tax_sale_sum = $transaction->final_total - $sumTAX;
                }

                // dd($tax_account_account);
                if ($input['saleType'] == 'sale' && $input['pay_method'] != 'advance') {
                    $default_payment_account = $default_payment_accounts[$input['pay_method']]['account'];
                
                    // customer that sale
                    $customer_account = [
                        'accounting_account_id' => $customer,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => $transaction->final_total,
                        'type' => 'debit',
                        'sub_type' => 'sell',
                        'map_type' => 'deposit_to',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];
                      // account that config by system
                    $sale_account = [
                        'accounting_account_id' => $payment_account,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => !empty($tax_sum['taxs']) ? $tax_sale_sum : $transaction->final_total,
                        'type' => 'credit',
                        'sub_type' => 'sell',
                        'map_type' => 'payment_account',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];

                    // like sandok
                    $default_payment_accounts = [
                        'accounting_account_id' => $default_payment_account,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => $input['pay_amount'],
                        'type' => 'debit',
                        'sub_type' => 'sell',
                        'map_type' => 'deposit_to',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];

                    // customer again
                    $defual_account_customer = [
                        'accounting_account_id' => $customer,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => $input['pay_amount'],
                        'type' => 'credit',
                        'sub_type' => 'sell',
                        'map_type' => 'payment_account',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];

                }elseif ($input['saleType'] == 'sale' && $input['pay_method'] == 'advance') {
                    
                    $transaction = Transaction::where('business_id', $business_id)->where('id', $id)->firstorFail();
                    // customer that sale
                    $customer_account = [
                        'accounting_account_id' => $customer,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => $transaction->final_total,
                        'type' => 'debit',
                        'sub_type' => 'sell',
                        'map_type' => 'deposit_to',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];
                      // account that config by system
                    $sale_account = [
                        'accounting_account_id' => $payment_account,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => !empty($tax_sum['taxs']) ? $tax_sale_sum : $transaction->final_total,
                        'type' => 'credit',
                        'sub_type' => 'sell',
                        'map_type' => 'payment_account',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];
                } elseif ($type == 'purchases') {
                    
                    $transaction = Transaction::where('business_id', $business_id)->where('id', $id)->firstorFail();
                    $default_payment_account = $default_payment_accounts[$input['pay_method']]['account'];
                
                    // account that config by system
                    $purchases_account = [
                        'accounting_account_id' => $payment_account,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => !empty($tax_sum['taxs']) ? $tax_sale_sum : $transaction->final_total,
                        'type' => 'debit',
                        'sub_type' => 'purchases',
                        'map_type' => 'payment_account',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];

                    // customer that sale
                    $customer_account = [
                        'accounting_account_id' => $customer,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => $transaction->final_total,
                        'type' => 'credit',
                        'sub_type' => 'purchases',
                        'map_type' => 'deposit_to',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];
                    
                    // like sandok
                    $default_payment_accounts = [
                        'accounting_account_id' => $default_payment_account,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => $input['pay_amount'],
                        'type' => 'credit',
                        'sub_type' => 'purchases',
                        'map_type' => 'deposit_to',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];

                    // customer again
                    $defual_account_customer = [
                        'accounting_account_id' => $customer,
                        'transaction_id' => $id,
                        'acc_trans_mapping_id' => $acc_trans_mapping,
                        'transaction_payment_id' => null,
                        'amount' => $input['pay_amount'],
                        'type' => 'debit',
                        'sub_type' => 'purchases',
                        'map_type' => 'payment_account',
                        'created_by' => $user_id,
                        'operation_date' => $input['transaction_date'] ?? \Carbon::now(),
                    ];

                }

                if ($input['saleType'] == 'sale') {
                    if ($input['pay_amount'] > 0 && $input['pay_method'] != 'advance') {
                        AccountingAccountsTransaction::create($customer_account);
                        AccountingAccountsTransaction::create($sale_account);
                        if (!empty($tax_sum['taxs'])) {
                            // tax_account that sale 
                            foreach ($tax_account_account as $tax_account) {
                                AccountingAccountsTransaction::create($tax_account);
                            }
                            
                        }
                        AccountingAccountsTransaction::create($default_payment_accounts);
                        AccountingAccountsTransaction::create($defual_account_customer);
                    }else {
                        
                        AccountingAccountsTransaction::create($customer_account);
                        AccountingAccountsTransaction::create($sale_account);
                        if (!empty($tax_sum['taxs'])) {
                            // tax_account that sale 
                            foreach ($tax_account_account as $tax_account) {
                                AccountingAccountsTransaction::create($tax_account);
                            }
                        }
                    }
                }
                
                
                if($input['saleType'] == 'purchases'){
                    if ($input['pay_amount'] > 0 && $input['pay_method'] != 'advance') {
                        AccountingAccountsTransaction::create($customer_account);
                        AccountingAccountsTransaction::create($purchases_account);
                        if (!empty($tax_sum['taxs'])) {
                            // tax_account that sale 
                            foreach ($tax_account_account as $tax_account) {
                                AccountingAccountsTransaction::create($tax_account);
                            }
                            
                        }
                        AccountingAccountsTransaction::create($default_payment_accounts);
                        AccountingAccountsTransaction::create($defual_account_customer);
                    }else {
                        
                        AccountingAccountsTransaction::create($customer_account);
                        AccountingAccountsTransaction::create($purchases_account);
                        if (!empty($tax_sum['taxs'])) {
                            // tax_account that sale 
                            foreach ($tax_account_account as $tax_account) {
                                AccountingAccountsTransaction::create($tax_account);
                            }
                        }
                    }
                }
               

                DB::commit();
                $output = ['success' => true,'msg' => __('lang_v1.updated_success'),];
        } catch (\Exception $e) {
            print_r($e->getMessage());
            exit;
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
        }

    }
    

    public function payContactDue($input, $business_id , $user_id){
        
        try {
            DB::beginTransaction();
            $now = Carbon::now();
            $journal_date = Carbon::createFromTimestamp(strtotime($now))->format('Y-m-d H:i:s');
            $accounting_settings = $this->accountingUtil->getAccountingSettings($business_id);
            $ref_no = '';
            $ref_count = $this->util->setAndGetReferenceCount('journal_entry');
            if (empty($ref_no)) {
                $prefix = ! empty($accounting_settings['journal_entry_prefix']) ? $accounting_settings['journal_entry_prefix'] : '';
                //Generate reference number
                $ref_no = $this->util->generateReferenceNumber('journal_entry', $ref_count, $business_id, $prefix);
            } 
            // dd(request('paid_on'));
            if (empty(request('paid_on'))) { 
                $input['paid_on'] = \Carbon::now();
            } else {
                $input['paid_on'] = $this->productUtil->uf_date(request('paid_on'), true);
            }
            $acc_trans_mapping = new AccountingAccTransMapping();
            $acc_trans_mapping->business_id = $business_id;
            $acc_trans_mapping->ref_no = $ref_no;
            $acc_trans_mapping->original = $input['due_payment_type'];
            $acc_trans_mapping->created_by = $user_id;
            $acc_trans_mapping->operation_date = $input['paid_on'] ?? $journal_date;
            $acc_trans_mapping->save();


                $location = BusinessLocation::where('business_id', $business_id)->first();
                $default_payment_accounts = !empty($location->default_payment_accounts) ? json_decode($location->default_payment_accounts, true) : [];

                $default_payment_account = $default_payment_accounts[$input['method']]['account'];
                $customer =  MainAccount::where('business_id',$business_id)->where('contact_id', $input['contact_id'])->first();

            if ($input['due_payment_type'] == 'sell') { 
                // customer that sale
                $customer_account = [
                    'accounting_account_id' => $customer->id,
                    'transaction_id' => null,
                    'acc_trans_mapping_id' => $acc_trans_mapping->id,
                    'transaction_payment_id' => null,
                    'amount' => $input['amount'],
                    'type' => 'credit',
                    'sub_type' => 'sell',
                    'map_type' => 'deposit_to',
                    'created_by' => $user_id,
                    'operation_date' => $input['paid_on'] ?? \Carbon::now(),
                ];
                 
                // like sandok
                $default_payment_accounts = [
                    'accounting_account_id' => $default_payment_account,
                    'transaction_id' => null,
                    'acc_trans_mapping_id' => $acc_trans_mapping->id,
                    'transaction_payment_id' => null,
                    'amount' => $input['amount'],
                    'type' => 'debit',
                    'sub_type' => 'sell',
                    'map_type' => 'payment_account',
                    'created_by' => $user_id,
                    'operation_date' => $input['paid_on'] ?? \Carbon::now(),
                ];
            }else if ($input['due_payment_type'] == 'purchase') {
                
                // customer that sale
                $customer_account = [
                    'accounting_account_id' => $customer->id,
                    'transaction_id' => null,
                    'acc_trans_mapping_id' => $acc_trans_mapping->id,
                    'transaction_payment_id' => null,
                    'amount' => $input['amount'],
                    'type' => 'debit',
                    'sub_type' => 'purchase',
                    'map_type' => 'deposit_to',
                    'created_by' => $user_id,
                    'operation_date' => $input['paid_on'] ?? \Carbon::now(),
                ];
                 
                // like sandok
                $default_payment_accounts = [
                    'accounting_account_id' => $default_payment_account,
                    'transaction_id' => null,
                    'acc_trans_mapping_id' => $acc_trans_mapping->id,
                    'transaction_payment_id' => null,
                    'amount' => $input['amount'],
                    'type' => 'credit',
                    'sub_type' => 'purchase',
                    'map_type' => 'payment_account',
                    'created_by' => $user_id,
                    'operation_date' => $input['paid_on'] ?? \Carbon::now(),
                ];
            }

            if ($input['due_payment_type'] == 'sell') {
                AccountingAccountsTransaction::create($default_payment_accounts);
                AccountingAccountsTransaction::create($customer_account);
            }

            if ($input['due_payment_type'] == 'purchase') {
                AccountingAccountsTransaction::create($default_payment_accounts);
                AccountingAccountsTransaction::create($customer_account);
            }



            DB::commit();
            $output = ['success' => true,'msg' => __('lang_v1.updated_success'),];
    } catch (\Exception $e) {
        print_r($e->getMessage());
        exit;
        DB::rollBack();
        \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
    }
    }
    
}