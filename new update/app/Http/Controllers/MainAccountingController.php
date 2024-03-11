<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\MainAccount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MainAccountingController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function AccountsDropdown()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $q = request()->input('q', '');
            $accounts = MainAccount::forDropdown($business_id, true, $q);

            $accounts_array = [];
            foreach ($accounts as $account) {
                $accounts_array[] = [
                    'id' => $account->id,
                    'text' => $account->name_ar.' - <small class="text-muted">'.$account->name_en.'</small>',
                    'html' => $account->name_en.' - <small class="text-muted">'.$account->name_ar.'</small>',
                ];
            }

            return $accounts_array;
        }
    }

}