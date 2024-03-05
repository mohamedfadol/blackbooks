<table class="table table-bordered table-striped">
    <thead>
        <tr> 
            <th>@lang( 'user.name' )</th>
        </tr>
    </thead>
    <tbody>
        @foreach($account_types as $account)
            <tr class="bg-gray">
                
                
                <td>{{$account->name_ar}}</td>
                 
               
            </tr> 
        @endforeach

        @if(!$account_exist)
            <tr>
                <td colspan="10" class="text-center">
                    <h3>@lang( 'accounting::lang.no_accounts' )</h3>
                    <p>@lang( 'accounting::lang.add_default_accounts_help' )</p>
                    <a href="{{route('accounting.create-default-accounts')}}" class="btn btn-success btn-xs">@lang( 'accounting::lang.add_default_accounts' ) <i class="fas fa-file-import"></i></a>
                </td>
            </tr>
        @endif
    </tbody>
</table>