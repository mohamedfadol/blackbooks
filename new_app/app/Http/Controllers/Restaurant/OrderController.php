<?php

namespace App\Http\Controllers\Restaurant;

use App\User;
use App\Utils\Util; 
use App\Transaction;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\RestaurantUtil;
use Illuminate\Http\Response;
use App\Events\NewOrdersEvent;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
 
class OrderController extends Controller
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
        // if (!auth()->user()->can('sell.view')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');

        $is_service_staff = false;
        $orders = [];
        $service_staff = [];
        $line_orders = [];
        if ($this->restUtil->is_service_staff($user_id)) {
            $is_service_staff = true;
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => $user_id]);

            $line_orders = $this->restUtil->getLineOrders($business_id, ['waiter_id' => $user_id]);
        } elseif (! empty(request()->service_staff)) {
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff]);

            $line_orders = $this->restUtil->getLineOrders($business_id, ['waiter_id' => request()->service_staff]);
        }

        if (! $is_service_staff) {
            $service_staff = $this->restUtil->service_staff_dropdown($business_id);
        }

        return view('restaurant.orders.index', compact('orders', 'is_service_staff', 'service_staff', 'line_orders'));
    }

    /**
     * Marks an order as served
     *
     * @return json $output
     */
    public function markAsServed($id,$kitchen_id)
    {
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            $query = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id)
                        ->where('transaction_sell_lines.kitchen_id', $kitchen_id);
            $transaction_sell_lines_ids = $query->pluck('transaction_sell_lines.id');
            if ($this->restUtil->is_service_staff($user_id)) {
                $query->where('res_waiter_id', $user_id);
            }
             
            $query->update(['res_line_order_status' => 'served']);

            TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->whereIn('parent_sell_line_id',$transaction_sell_lines_ids)
                        ->update(['res_line_order_status' => 'served']);
            DB::table('order_status')->insert(
                            ['transaction_id' => $id ,
                            'kitchen_id' => $kitchen_id,
                            'business_id' => $business_id,
                            'status' => 'served'
                            ]);
            $msgs = 'New Order Coming ...';
            event(new NewOrdersEvent($msgs));
            $output = ['success' => 1, 'msg' => trans('restaurant.order_successfully_marked_served')];
            DB::commit();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            DB::rollback();
            $output = ['success' => 0, 'msg' => trans('messages.something_went_wrong')];
        }

        return $output;
    }


        /**
     * Marks an order as received
     *
     * @return json $output
     */
    public function markAsReceived($id)
    {
        // if (!auth()->user()->can('sell.update')) { 
        //     abort(403, 'Unauthorized action.');
        // }    
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id);

            if ($this->restUtil->is_service_staff($user_id)) {
                $query->where('res_waiter_id', $user_id);
            }

            $query->update(['res_line_order_status' => 'delivered']);
            DB::table('order_status')->where('transaction_id',$id)->where('business_id', $business_id)->delete();
            $msgs = 'New Order Coming ...';
            event(new NewOrdersEvent($msgs));
            $output = ['success' => 1,'msg' => trans('restaurant.order_successfully_marked_delivered'),];
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
    public function markAsOrderDone($id)
    {
        // if (!auth()->user()->can('sell.update')) {
        //     abort(403, 'Unauthorized action.');
        // }
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id);

            if ($this->restUtil->is_service_staff($user_id)) {
                $query->where('res_waiter_id', $user_id);
            }

            $query->update(['res_line_order_status' => 'done']);
            $msgs = 'New Order Coming ...';
            event(new NewOrdersEvent($msgs));
            $output = ['success' => 1,
                'msg' => trans('restaurant.order_successfully_marked_done'),
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
     * Marks an order as Cooked
     *
     * @return json $output
     */
    public function markAsCooked($id)
    {
        // if (!auth()->user()->can('sell.update')) {
        //     abort(403, 'Unauthorized action.');
        // }
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id);

            if ($this->restUtil->is_service_staff($user_id)) {
                $query->where('res_waiter_id', $user_id);
            }

            $query->update(['info' => 'cooked']);
            $msgs = 'New Order Coming ...';
            event(new NewOrdersEvent($msgs));
            $output = ['success' => 1,
                'msg' => trans('restaurant.order_successfully_marked_cooked'),
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
     * Marks all order as Cooked
     *
     * @return json $output
     */
    public function markAsAllCooked($id, $kitchen_id)
    {
        try {
            DB::beginTransaction();
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            $query = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id)
                        ->where('transaction_sell_lines.kitchen_id', $kitchen_id);
            $transaction_sell_lines_ids = $query->pluck('transaction_sell_lines.id');
            if ($this->restUtil->is_service_staff($user_id)) {
                $query->where('res_waiter_id', $user_id);
            }
            $query->update(['res_line_order_status' => 'cooked']);

            TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                                ->where('t.business_id', $business_id)
                                ->where('kitchen_id', $kitchen_id)
                                ->whereIn('parent_sell_line_id',$transaction_sell_lines_ids)
                                ->update(['res_line_order_status' => 'cooked']);

            $t = Transaction::find($id);
            $t_Lines = TransactionSellLine::where('transaction_id',$id)
                                            ->whereNull('res_line_order_status')
                                            ->orWhereNotNull('res_line_order_status')
                                                                        /* != */
                                            ->where('res_line_order_status','<>','done')  
                                            ->where('res_line_order_status','<>','returned')
                                            ->where('res_line_order_status','<>','delivered')   
                                            ->where('res_line_order_status','<>','cooked'); 
                                                
            if($t_Lines->count() > 0):  
            else:
                $t->update(['info' => 'done']);
                $orders_status_list =  DB::table('order_status')->where('transaction_id',$id)
                                        ->where('business_id', $business_id)
                                            ->where('kitchen_id', $kitchen_id)->update(['status' => 'done']);
                 
                $transaction_sell_lines_list =  TransactionSellLine::where('transaction_id',$id)->get();
                foreach ($transaction_sell_lines_list as  $line) {
                    $line ->update(['res_line_order_status' => 'done']);
                }
            endif;
            $msgs = 'New Order Coming ...';
            event(new NewOrdersEvent($msgs));
            $output = ['success' => 1,'msg' => trans('restaurant.order_successfully_marked_cooked')];
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
     * Marks an line order as served
     *
     * @return json $output
     */
    public function markLineOrderAsServed($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = TransactionSellLine::where('id', $id);

            if ($this->restUtil->is_service_staff($user_id)) {
                $query->where('res_service_staff_id', $user_id);
            }
            $sell_line = $query->first();

            if (! empty($sell_line)) {
                $sell_line->res_line_order_status = 'served';
                $sell_line->save();
                $output = ['success' => 1,
                    'msg' => trans('restaurant.order_successfully_marked_served'),
                ]; 
            } else {
                $output = ['success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            
            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function printLineOrder(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $waiter_id = request()->session()->get('user.id');
            $line_id = $request->input('line_id');
            if (! empty($request->input('service_staff_id'))) {
                $waiter_id = $request->input('service_staff_id');
            }

            $line_orders = $this->restUtil->getLineOrders($business_id, ['waiter_id' => $waiter_id, 'line_id' => $line_id]);
            $order = $line_orders[0];
            $html_content = view('restaurant.partials.print_line_order', compact('order'))->render();
            $output = [
                'success' => 1,
                'msg' => trans('lang_v1.success'),
                'html_content' => $html_content,
            ];
        } catch (Exception $e) {
            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
}
