<?php

namespace App\Http\Controllers\Restaurant;

use App\Kitchen;
use App\Category;
use App\Utils\Util;
use App\Transaction;
use App\CompleteOrder;
use App\BusinessLocation;
use App\SellingPriceGroup;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\RestaurantUtil;
use Illuminate\Http\Response;
use App\Events\NewOrdersEvent;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KitchenController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    protected $restUtil;

    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @param  RestaurantUtil  $restUtil
     * @return void
     */
    public function __construct(Util $commonUtil, RestaurantUtil $restUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $kitchens = Kitchen::where('business_id', $business_id)->get();
        return view('restaurant.kitchen.index', compact('kitchens'));
    }


    public function home(Request $request) {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $kitchens = Kitchen::with('category')->where('business_id', $business_id);
            return Datatables::of($kitchens)
                ->addColumn(
                    'action',
                    ' 
                        <button type="button" data-href="{{action(\'App\Http\Controllers\Restaurant\KitchenController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_kitchen_button" data-container=".modifier_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;  
                        <button data-href="{{action(\'App\Http\Controllers\Restaurant\KitchenController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_kitchen_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )               
                ->removeColumn('id')
                ->escapeColumns(['action'])
                ->make(true);
        }
        return view('restaurant.kitchen.home');
    }
    

    public function create(Request $request){
        $business_id = request()->session()->get('user.business_id');
        $categories = Category::where('business_id' , $business_id)->get(['id','name','business_id']);
        $locations = BusinessLocation::forDropdown($business_id);
        $price_groups = SellingPriceGroup::forDropdown($business_id);
        return view('restaurant.kitchen.create')->with(compact('locations', 'categories','price_groups'));

    }

    public function show($id)
    {
        $kitchen = Kitchen::find($id);
        $business_id = request()->session()->get('user.business_id');
        $orders = $this->restUtil->getAllOrders($business_id, ['line_order_status' => 'received'],$kitchen->id);
        return view('restaurant.kitchen.ketchen_home', compact('orders','kitchen','id'));   
    }


    public function store(Request $request){

        // if (! auth()->user()->can('account.access')) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'category_id']);
                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $input['business_id'] = $business_id;
                // $input['created_by'] = $user_id;

                Kitchen::create($input);

                  
                $output = ['success' => true,
                    'msg' => __('account.kitchen_created_success'),
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

    public function edit($id, Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $categories = Category::where('business_id' , $business_id)->get(['id','name','business_id']);
            $locations = BusinessLocation::forDropdown($business_id);
            $price_groups = SellingPriceGroup::forDropdown($business_id);
            $kitchen = Kitchen::where('business_id', $business_id)
                            ->where('id', $id)
                            ->first();

            return view('restaurant.kitchen.edit')->with(compact('kitchen','locations', 'categories','price_groups'));
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0, 'msg' => __('messages.something_went_wrong')];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $input = $request->all();
            $business_id = $request->session()->get('user.business_id');
            $kitchen = Kitchen::where('business_id', $business_id)->where('id', $id)->first();
            $kitchen->update([
                            'name' => $input['name'],
                            'category_id' => $input['category_id'],
                            ]);
            DB::commit();

            $output = ['success' => 1, 'msg' => __('lang_v1.updated_success')];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0, 'msg' => __('messages.something_went_wrong')];
        }

        return $output;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy($id, Request $request)
    {
        if (! auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $business_id = $request->session()->get('user.business_id');
            Kitchen::where('business_id', $business_id)->where('id', $id)->delete();
            DB::commit();
            $output = ['success' => 1, 'msg' => __('lang_v1.deleted_success')];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0, 'msg' => __('messages.something_went_wrong')];
        }

        return $output;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function orderRecevied()
    {
        $business_id = request()->session()->get('user.business_id');
        $orders = $this->restUtil->getAllReceivedOrders($business_id, ['line_order_status' => 'received']);
        return view('restaurant.partials.recevied_orders', compact('orders'));
    }
 
    /**
     * Marks an order as cooked
     *
     * @return json $output
     */
    public function markAsCooked($id)
    {
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $tline = TransactionSellLine::find($id);
            $tline->update(['res_line_order_status' => 'cooked']);
            TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('parent_sell_line_id', '=',  $id)
                        ->update(['res_line_order_status' => 'cooked']);

            $t = Transaction::find($tline->transaction_id);
            $t_Lines = TransactionSellLine::where('transaction_id',$tline->transaction_id)
                                            ->whereNull('res_line_order_status')
                                            ->orWhereNotNull('res_line_order_status')
                                                                        /* != */
                                            ->where('res_line_order_status','<>','done')  
                                            ->where('res_line_order_status','<>','returned') 
                                            ->where('res_line_order_status','<>','delivered') 
                                            ->where('res_line_order_status','<>','cooked'); 
                                              
            if($t_Lines->count() > 0):
            else:
                DB::table('order_status')->where('transaction_id',$tline->transaction_id)->where('business_id', $business_id)->update(['status' => 'done']);
                TransactionSellLine::where('transaction_id',$tline->transaction_id)->update(['res_line_order_status' => 'done']);
                $t->update(['info' => 'done']);
            endif;
                $msgs = 'New Order Coming ...';
                event(new NewOrdersEvent($msgs));
            $output = ['success' => 1, 'msg' => trans('restaurant.order_successfully_marked_cooked')];
            DB::commit();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            DB::rollback();
            $output = ['success' => 0,'msg' => trans('messages.something_went_wrong')];
        }

        return $output;
    }




    /**
     * Marks an order as all cooked
     *
     * @return json $output 
     */
    public function markAsAllCooked($id, $kitchen_id)
    {
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $tline = TransactionSellLine::find($id);
            $tline->update(['res_line_order_status' => 'cooked']);
            TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('parent_sell_line_id', '=',  $id)
                        ->where('transaction_sell_lines.kitchen_id', $kitchen_id)
                        ->update(['res_line_order_status' => 'cooked']);

            $t = Transaction::find($tline->transaction_id);
            $t_Lines = TransactionSellLine::where('transaction_id',$tline->transaction_id)
                                            ->whereNull('res_line_order_status')
                                            ->orWhereNotNull('res_line_order_status')
                                                                        /* != */
                                            ->where('res_line_order_status','<>','done')  
                                            ->where('res_line_order_status','<>','returned')
                                            ->where('res_line_order_status','<>','delivered') 
                                            ->where('res_line_order_status','<>','cooked'); 
                                              
            if($t_Lines->count() > 0):
            else:
                DB::table('order_status')->where('transaction_id',$tline->transaction_id)->where('business_id', $business_id)->update(['status' => 'done']);
                TransactionSellLine::where('transaction_id',$tline->transaction_id)->update(['res_line_order_status' => 'done']);
                $t->update(['info' => 'done']);
            endif;
                $msgs = 'New Order Coming ...';
                event(new NewOrdersEvent($msgs));
            $output = ['success' => 1, 'msg' => trans('restaurant.order_successfully_marked_cooked')];
            DB::commit();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            DB::rollback();
            $output = ['success' => 0,'msg' => trans('messages.something_went_wrong')];
        }

        return $output;
    }


    /**
     * Marks an order as delivered
     *
     * @return json $output
     */
    public function markAsDeliveried($id)
    {
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $sl = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id)
                        ->where(function ($q) {
                            $q->whereNull('res_line_order_status')
                                ->orWhere('res_line_order_status', 'received')
                                ->orWhere('res_line_order_status','served');
                        })
                        ->update(['res_line_order_status' => 'delivered']);
            DB::table('order_status')->where('transaction_id',$id)->where('business_id', $business_id)->update(['status' => 'delivered']);
            $msgs = 'New Order Coming ...';
            event(new NewOrdersEvent($msgs));
            $output = ['success' => 1,
                'msg' => trans('restaurant.order_successfully_marked_delivered'),
            ];
            DB::commit();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            DB::rollback();
            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
    * change order status to returned in order to back Kitchen view
    *
    * @return json $output
    */
    public function orderBackToKitchen() 
    {
        $business_id = request()->session()->get('user.business_id');
        $orders = $this->restUtil->getAllOrdersReturnsKitchen($business_id, ['line_order_status' => 'returns']);
        return view('restaurant.partials.orders_back', compact('orders'));
    }
 
    /**
    * change order status to returned in order to back Kitchen view
    *
    * @return json $output
    */
    public function backToKitchen($id) 
    {
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $sl = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id);
                
                     $transaction_sell_lines_ids = $sl->pluck('transaction_sell_lines.id');
                    TransactionSellLine::whereIn('parent_sell_line_id',$transaction_sell_lines_ids)
                                            ->update(['res_line_order_status' => 'returned']);
                    TransactionSellLine::whereIn('id',$transaction_sell_lines_ids)
                                                ->update(['res_line_order_status' => 'returned']);

                    $sl->update(['info' => 'returned']);
                    DB::table('order_status')->where('transaction_id',$id)->where('business_id', $business_id)->update(['status' => 'returned']);
                    $msgs = 'New Order Coming ...';
                    event(new NewOrdersEvent($msgs));
            $output = ['success' => 1,
                'msg' => trans('restaurant.order_successfully_marked_back_to_cooked'),
            ];
            DB::commit();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            DB::rollback();
            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function viewPage() {
        $business_id = request()->session()->get('user.business_id');
        $orders = $this->restUtil->getAllOrdersCooked($business_id, ['line_order_status' => 'cooked']);
        $resrvedOrders = $this->restUtil->getAllOrdersRerved($business_id, ['line_order_status' => 'served']);
        return view('restaurant.partials.hole_view', compact('orders','resrvedOrders'));
    }
 

    /**
     * Marks an order as done
     *
     * @return json $output
     */
    public function markOrderCompleteDone($id)
    {
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $sl = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_sell_lines.id', $id)
                        ->where(function ($q) {
                            $q->orWhereNotNull('res_line_order_status')
                                ->orWhereNull('res_line_order_status')
                                ->orWhere('res_line_order_status', 'received')
                                ->orWhere('res_line_order_status','served');
                        })
                        ->update(['res_line_order_status' => 'returned']);

            $output = ['success' => 1,'msg' => trans('restaurant.order_successfully_marked_returned'),
            ];
            DB::commit();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            DB::rollback();
            $output = ['success' => 0,'msg' => trans('messages.something_went_wrong'),];
        }

        return $output;
    }

        /**
     * Marks an order as done
     *
     * @return json $output
     */
    public function markOrderCompleteNotDone($id)
    {
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            CompleteOrder::where('id', $id)
                                ->where('business_id', $business_id)
                                ->update(['status' => 'done']);

            $output = ['success' => 1,'msg' => trans('restaurant.order_successfully_marked_done'),
            ];
            DB::commit();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            DB::rollback();
            $output = ['success' => 0,'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Retrives fresh orders
     *
     * @return Json $output
     */
    public function refreshOrdersList(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $orders_for = $request->orders_for;
        $filter = [];
        $service_staff_id = request()->session()->get('user.id');

        if (! $this->restUtil->is_service_staff($service_staff_id) && ! empty($request->input('service_staff_id'))) {
            $service_staff_id = $request->input('service_staff_id');
        }

        if ($orders_for == 'kitchen') {
            $filter['line_order_status'] = 'received';
        } elseif ($orders_for == 'waiter') {
            $filter['waiter_id'] = $service_staff_id;
        }

        $orders = $this->restUtil->getAllOrders($business_id, $filter);

        return view('restaurant.partials.edit_show_orders', compact('orders', 'orders_for'));
    }

    /**
     * Retrives fresh orders
     *
     * @return Json $output
     */
    public function refreshLineOrdersList(Request $request)
    {

        // if (!auth()->user()->can('sell.view')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $business_id = request()->session()->get('user.business_id');
        $orders_for = $request->orders_for;
        $filter = [];
        $service_staff_id = request()->session()->get('user.id');

        if (! $this->restUtil->is_service_staff($service_staff_id) && ! empty($request->input('service_staff_id'))) {
            $service_staff_id = $request->input('service_staff_id');
        }

        if ($orders_for == 'kitchen') {
            $filter['order_status'] = 'received';
        } elseif ($orders_for == 'waiter') {
            $filter['waiter_id'] = $service_staff_id;
        }

        $line_orders = $this->restUtil->getLineOrders($business_id, $filter);

        return view('restaurant.partials.line_orders', compact('line_orders', 'orders_for'));
    }
}
