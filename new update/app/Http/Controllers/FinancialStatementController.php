<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\FinancialStatement;
use Yajra\DataTables\Facades\DataTables;

class FinancialStatementController extends Controller
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
            $account_category = FinancialStatement::where('business_id', $business_id)->select('*');
            
            return Datatables::of($account_category)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\FinancialStatementController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".financial_statement_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\FinancialStatementController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_financial_statement"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns(['action', 'parent_id'])
                ->make(true);
        }
        return view('financial_statement.index');
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
        $financial_statements = FinancialStatement::where('business_id', $business_id)->get();
        return view('financial_statement.create',compact('financial_statements'));
        
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
            $input = $request->only(['name_en','name_ar','parent_id']);
            $account = new FinancialStatement;
            $account->name_en = $input['name_en'] ?? null;
            $account->name_ar = $input['name_ar'];
            $account->parent_id = $input['parent_id'] ?? null;
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
        $financial_statement = FinancialStatement::where('business_id', $business_id)->findOrFail($id);
        $financial_statements = FinancialStatement::where('business_id', $business_id)->get();
        return view('financial_statement.edit')->with(compact('financial_statement','financial_statements'));
        
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
            FinancialStatement::where('business_id', $business_id)
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
                FinancialStatement::where('business_id', $business_id)
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
