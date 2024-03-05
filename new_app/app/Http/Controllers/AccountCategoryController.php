<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\AccountCategory;
use Yajra\DataTables\Facades\DataTables;

class AccountCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (! auth()->user()->can('accounting.manage_accounts')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $account_category = AccountCategory::where('business_id', $business_id)->select('*');
            
            return Datatables::of($account_category)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\AccountCategoryController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".account_category_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\AccountCategoryController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_account_category"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns(['action', 'parent_id'])
                ->make(true);
        }
        return view('account_category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (! auth()->user()->can('accounting.manage_accounts')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $acc_category = AccountCategory::where('business_id', $business_id)->get();
        return view('account_category.create',compact('acc_category'));
        
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');
        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $input = $request->only(['name_en','name_ar','acc_category_parent_id']);
            $account = new AccountCategory;
            $account->name_en = $input['name_en'] ?? null;
            $account->name_ar = $input['name_ar'];
            $account->parent_id = $input['acc_category_parent_id'] ?? null;
            $account->created_by = $user_id;
            $account->business_id = $business_id;
            $account->save();
            $output = ['success' => true,'msg' => __('lang_v1.added_success')];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,'msg' => __('messages.something_went_wrong')];
        }

        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('accounting.manage_accounts')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $account_category = AccountCategory::where('business_id', $business_id)->findOrFail($id);
        $acc_category = AccountCategory::where('business_id', $business_id)->get();
        return view('account_category.edit')->with(compact('account_category','acc_category'));
        
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
        $user_id = $request->session()->get('user.id');
        if (! (auth()->user()->can('superadmin') ||
            $this->moduleUtil->hasThePermissionInSubscription($business_id, 'accounting_module')) ||
            ! (auth()->user()->can('accounting.manage_accounts'))) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        try {
            $input = $request->only(['name_en','name_ar','parent_id']);
            $input['parent_id'] = $input['parent_id'] ?? null;
            AccountCategory::where('business_id', $business_id)
                        ->where('id', $id)
                        ->update($input);

            $output = ['success' => true,
                'msg' => __('lang_v1.updated_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('accounting.manage_accounts')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                AccountCategory::where('business_id', $business_id)
                        ->where('id', $id)
                        ->delete();

                $output = ['success' => true,
                    'msg' => __('lang_v1.deleted_success'),
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
