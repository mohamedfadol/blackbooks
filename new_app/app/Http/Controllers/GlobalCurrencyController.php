<?php

namespace App\Http\Controllers;

use DB;
use App\GlobalCurrency;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\CustomerNotification;

class GlobalCurrencyController  extends Controller
{

    protected $transactionUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  TransactionUtil  $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $galobal_currencies = GlobalCurrency::where('business_id', $business_id)
                        ->select('*');
                        // dd($galobal_currencies);
            return Datatables::of($galobal_currencies)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\GlobalCurrencyController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".galobal_currencies_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\GlobalCurrencyController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_galobal_currencies_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('galobal_currencies.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (! auth()->user()->can('supplier.create') && ! auth()->user()->can('supplier.view_own')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not
        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        return view('galobal_currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('galobal_currencies.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = $request->session()->get('user.business_id');

            if (! $this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }
           
            try {
                $input = $request->only(['global_currency_name', 'global_currency_value','local_currency_name','local_currency_value']);
                $business_id = $request->session()->get('user.business_id');
                $user_id = request()->session()->get('user.id');
                GlobalCurrency::create([
                    'global_currency_name' => $input['global_currency_name'], 
                    'global_currency_value' => $input['global_currency_value'],
                    // 'local_currency_name' => $input['local_currency_name'],
                    // 'local_currency_value'  => $input['local_currency_value'],
                    'created_by'  => $user_id,
                    'business_id'  => $business_id,
                    ]);

                $output = ['success' => true,
                    'msg' => __('galobal_currencies.added_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
    
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
    
            return $output;         

         
        } catch (\Exception $e) {
            
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     if (! auth()->user()->can('supplier.view') && ! auth()->user()->can('customer.view') && ! auth()->user()->can('customer.view_own') && ! auth()->user()->can('supplier.view_own')) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $business_id = request()->session()->get('user.business_id');
    //     $contact = $this->contactUtil->getContactInfo($business_id, $id);

    //     $is_selected_contacts = User::isSelectedContacts(auth()->user()->id);
    //     $user_contacts = [];
    //     if ($is_selected_contacts) {
    //         $user_contacts = auth()->user()->contactAccess->pluck('id')->toArray();
    //     }

    //     if (! auth()->user()->can('supplier.view') && auth()->user()->can('supplier.view_own')) {
    //         if ($contact->created_by != auth()->user()->id & ! in_array($contact->id, $user_contacts)) {
    //             abort(403, 'Unauthorized action.');
    //         }
    //     }
    //     if (! auth()->user()->can('customer.view') && auth()->user()->can('customer.view_own')) {
    //         if ($contact->created_by != auth()->user()->id & ! in_array($contact->id, $user_contacts)) {
    //             abort(403, 'Unauthorized action.');
    //         }
    //     }

    //     $reward_enabled = (request()->session()->get('business.enable_rp') == 1 && in_array($contact->type, ['customer', 'both'])) ? true : false;

    //     $contact_dropdown = Contact::contactDropdown($business_id, false, false);

    //     $business_locations = BusinessLocation::forDropdown($business_id, true);

    //     //get contact view type : ledger, notes etc.
    //     $view_type = request()->get('view');
    //     if (is_null($view_type)) {
    //         $view_type = 'ledger';
    //     }

    //     $contact_view_tabs = $this->moduleUtil->getModuleData('get_contact_view_tabs');

    //     $activities = Activity::forSubject($contact)
    //        ->with(['causer', 'subject'])
    //        ->latest()
    //        ->get();

    //     return view('contact.show')
    //          ->with(compact('contact', 'reward_enabled', 'contact_dropdown', 'business_locations', 'view_type', 'contact_view_tabs', 'activities'));
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('galobal_currencies.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $galobal_currency = GlobalCurrency::where('business_id', $business_id)->find($id);
            return view('galobal_currencies.edit')->with(compact('galobal_currency'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
        // if (! auth()->user()->can('galobal_currencies.update')) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (request()->ajax()) {
            // dd('hi');
            try {
                $input = $request->only(['global_currency_name', 'global_currency_value','local_currency_name','local_currency_value']);
                $business_id = $request->session()->get('user.business_id');
                $user_id = request()->session()->get('user.id');

                $currency = GlobalCurrency::where('business_id', $business_id)->findOrFail($id);
                $currency->global_currency_name = $input['global_currency_name']; 
                $currency->global_currency_value = $input['global_currency_value'];
                // $currency->local_currency_name = $input['local_currency_name'];
                // $currency->local_currency_value = $input['local_currency_value'];
                $currency->created_by = $user_id;
                $currency->business_id = $business_id;
                $currency->save();
                $output = ['success' => true, 'msg' => __('global_currency.updated_success'),];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                $output = ['success' => false, 'msg' => __('messages.something_went_wrong'),];
            }

            return $output;
        }
    }


    public function destroy($id)
    {
        if (! auth()->user()->can('galobal_currencies.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $galobal_currencies = GlobalCurrency::where('business_id', $business_id)->findOrFail($id);
                $galobal_currencies->delete();

                $output = ['success' => true,
                    'msg' => __('galobal_currencies.deleted_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
            return $output;
        }
    }
 
 
}
