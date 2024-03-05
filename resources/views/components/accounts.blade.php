@foreach($accounts as  $account)
    <li @if(!$account->isParent->count() == 0) data-jstree='{"opened" : true}' @else data-jstree='{"icon" : "fas fa-arrow-alt-circle-right"}' @endif>
      <x-account :account="$account" />
    </li>
@endforeach