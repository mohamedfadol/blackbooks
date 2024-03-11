    {{$account->name_ar}}   
    @if(!empty($account->account_number))({{$account->account_number}}) @endif
    {{session("currency")["symbol"]}}  {{number_format(-1 * $account->totalBalance,3,".","")}}  
      
     @if($account->status == 'active') 
         <span style="margin-right: 5px;"><i class="fas fa-check text-success" title="@lang( 'accounting::lang.active' )"></i></span>
     @elseif($account->status == 'inactive') 
         <span style="margin-right: 5px;"><i class="fas fa-times text-danger" title="@lang( 'lang_v1.inactive' )" style="font-size: 14px;"></i></span>
     @endif
     <span class="tree-actions" style="margin-right: 5px;">
         <a class="btn btn-xs btn-default text-success ledger-link" style="margin-right: 5px;"
             title="@lang( 'accounting::lang.ledger' )"
             href="{{action([\Modules\Accounting\Http\Controllers\CoaController::class, 'ledger'], $account->id)}}">
             <i class="fas fa-file-alt"></i>
         </a>
         <a class="btn-modal btn-xs btn-default text-primary" style="margin-right: 5px;" title="@lang('messages.edit')"
             href="{{action([\Modules\Accounting\Http\Controllers\CoaController::class, 'edit'], $account->id)}}" 
             data-href="{{action([\Modules\Accounting\Http\Controllers\CoaController::class, 'edit'], $account->id)}}" 
             data-container="#create_account_modal">
             <i class="fas fa-edit"></i>
         </a>
         <a class="activate-deactivate-btn text-warning  btn-xs btn-default" style="margin-right: 5px;"
             title="@if($account->status=='active') @lang('messages.deactivate') @else 
             @lang('messages.activate') @endif"
             href="{{action([\Modules\Accounting\Http\Controllers\CoaController::class, 'activateDeactivate'], $account->id)}}">
             <i class="fas fa-power-off"></i>
         </a>
         @if ($account->isParent->count() == 0 && $account->balance == null && $account->sumBalanceOfChildren() == null)
             <a href="#" class="delete-delete-btn text-danger btn-xs btn-default" style="margin-right: 5px;"
                 title="@lang('messages.delete')"  
                     data-href="{{action([\Modules\Accounting\Http\Controllers\CoaController::class, 'destroy'], [$account->id])}}">
                 <i class="fas fa-trash"></i>
             </a>
         @endif
     </span>
 
     <ul>
        <x-accounts :accounts="$account->children" />
     </ul>