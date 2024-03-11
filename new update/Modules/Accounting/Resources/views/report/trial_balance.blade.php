@extends('layouts.app')

@section('title', __('accounting::lang.trial_balance'))
@section('css')
    <style>
        .hidden {
    display: none !important;
}
.text-black {
    color: #050505;
}
    </style>
@endsection
@section('content')

@include('accounting::layouts.nav')

<section class="content">
        
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
            {!! Form::text('date_range_filter', null, 
                ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'date_range_filter']); !!}
        </div>
    </div>

    <div class="col-md-2">
         
            <button type="button" class="btn btn-info toggle-columns" data-target="debit_credit">{!! Form::label('debit_credit_filter', __('report.debit_credit')) !!}</button>
       
    </div>

    <div class="col-md-2"> 
            <button type="button" class="btn btn-info toggle-columns" data-target="debit_credit_balance">{!! Form::label('debit_credit_balance_filter', __('report.debit_credit_balance')) !!}</button>
           
    </div>

    

    <div class="col-md-12 col-md-offset-0">
        
        <div class="box box-warning">
            <div class="box-header with-border text-center">
                <h2 class="box-title">@lang( 'accounting::lang.trial_balance')</h2>
                <p>{{@format_date($start_date)}} ~ {{@format_date($end_date)}}</p>
            </div>

            <div class="box-body">
                <table class="table table-bordered" border="1" id="trialBalanceTable">
                    <thead>
                        <tr>
                            <th>@lang('accounting::lang.account_name')</th>
                            <th class="debit_credit">@lang( 'accounting::lang.debit')</th>
                            <th class="debit_credit">@lang( 'accounting::lang.credit')</th>
                            <th class="debit_credit_balance">@lang( 'accounting::lang.total_debit_balance')</th>
                            <th class="debit_credit_balance">@lang( 'accounting::lang.total_credit_balance')</th>
                        </tr>
                    </thead>

                    @php
                        $total_debit = 0;
                        $total_credit = 0;
                        $final_balance1 =0;
                        $final_balance2 =0;
                    @endphp

                    <tbody>
                        @foreach($accounts as $account)

                        @php
                            $total_debit += $account->debit_balance;
                            $total_credit += $account->credit_balance;
                            $final_balance1 += ($account->debit_balance - $account->credit_balance < 0) ? 0 : ($account->debit_balance - $account->credit_balance );
                            $final_balance2 += ($account->debit_balance - $account->credit_balance < 0) ? ($account->debit_balance - $account->credit_balance ) : 0;
                            if ($account->parent_id === null || $account->child_accounts()->count() > 0){
                                $account->calculateSum($account);
                            }
                        @endphp
                                 
                            <tr class=  {{ $account->child_accounts->count() > 0 ? "text-success" : "text-black" }} >
                                <td> <a  class= {{ $account->child_accounts()->count() > 0 ? "text-success" : "text-black" }} href="{{ route("accounting.ledger",$account->id) }}"> {{$account->account_number}} - {{$account->name_ar ?? $account->name_en}}  </a> </td>
                                <td> 
                                    @if($account->debit_balance != 0)
                                        {{number_format($account->debit_balance,3,".","")}}
                                    @endif    
                                </td>
                                <td> 
                                    @if($account->credit_balance != 0)
                                        {{number_format($account->credit_balance,3,".","")}}
                                    @endif
                                </td>
                                @php
                                
                                    $db1 = ($account->debit_balance - $account->credit_balance < 0) ? 0 : ($account->debit_balance - $account->credit_balance );
                                    $db2 = ($account->debit_balance - $account->credit_balance < 0) ? ($account->debit_balance - $account->credit_balance ) : 0;
                                    // $db1 = ($account->debit_balance - $account->credit_balance < 0) ? $account->child_sum_balance : ($account->debit_balance - $account->credit_balance );
                                    // $db2 = ($account->debit_balance - $account->credit_balance < 0) ? ($account->debit_balance - $account->credit_balance ) : $account->child_sum_balance;
                                @endphp
                                <td>{{number_format($db1,3,".","") }}</td>
                                <td>{{number_format($db2,3,".","") }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>@lang('accounting::lang.total') </th>
                            <th class="total_debit">{{session("currency")["symbol"]}} {{number_format($total_debit,3,".","") }}</th>
                            <th class="total_credit">{{session("currency")["symbol"]}} {{number_format($total_credit,3,".","") }}</th>
                            <th class="total_debit_balance">{{session("currency")["symbol"]}} {{number_format($final_balance1,3,".","") }}</th>
                            <th class="total_credit_balance">{{session("currency")["symbol"]}} {{number_format($final_balance2,3,".","") }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

</section>


@stop

@section('javascript')
 
<script type="text/javascript">
    $(document).ready(function(){
        dateRangeSettings.startDate = moment('{{$start_date}}');
        dateRangeSettings.endDate = moment('{{$end_date}}');
        $('#date_range_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                apply_filter();
            }
        );
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range_filter').val('');
            apply_filter();
        });

        function apply_filter(){
            var start = '';
            var end = '';

            if ($('#date_range_filter').val()) {
                start = $('input#date_range_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                end = $('input#date_range_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            }

            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('start_date', start);
            urlParams.set('end_date', end);
            window.location.search = urlParams;
        }


 
         
            var table = document.getElementById('trialBalanceTable');
            document.querySelectorAll('.toggle-columns').forEach(function (button) {
                button.addEventListener('click', function () {
                    var target = $(this).data('target');
                    console.log(target);
                    toggleColumns(target);
                });
            });

            function toggleColumns(target) {
                var columnIndexStart, columnIndexEnd;

                if (target === 'debit_credit') {
                    columnIndexStart = 1;
                    columnIndexEnd = 2;
                } else if (target === 'debit_credit_balance') {
                    columnIndexStart = 3;
                    columnIndexEnd = 4;
                }

                for (var i = columnIndexStart; i <= columnIndexEnd; i++) {
                    toggleColumnVisibility(table, i);
                }
            }

            function toggleColumnVisibility(table, columnIndex) {
                for (var row of table.rows) {
                    row.cells[columnIndex].classList.toggle('hidden');
                }
            }
        
    });

</script>

@stop