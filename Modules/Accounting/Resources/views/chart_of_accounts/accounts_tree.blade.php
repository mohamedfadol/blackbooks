
@if(!$account_exist)
<table class="table table-bordered table-striped">
    <tr>
        <td colspan="10" class="text-center">
            <h3>@lang( 'accounting::lang.no_accounts' )</h3>
            <p>@lang( 'accounting::lang.add_default_accounts_help' )</p>
            <a href="{{route('accounting.create-default-accounts')}}" class="btn btn-success btn-xs">@lang( 'accounting::lang.add_default_accounts' ) <i class="fas fa-file-import"></i></a>
        </td>
    </tr>
</table>
@else
<div class="row">
    <div class="col-md-4 mb-12 col-md-offset-4">
        <div class="input-group">
            <input type="input" class="form-control" id="accounts_tree_search">
            <span class="input-group-addon">
                <i class="fas fa-search"></i>
            </span>
        </div> 
    </div>
    <div class="col-md-4">
        <button class="btn btn-primary btn-sm" id="expand_all">@lang('accounting::lang.expand_all')</button>
        <button class="btn btn-primary btn-sm" id="collapse_all">@lang('accounting::lang.collapse_all')</button>
    </div>
    <div class="col-md-12" id="accounts_tree_container">
        <ul>
            <x-accounts :accounts="$account_types" />
        </ul>
    </div>
</div>
@endif