@extends('layouts.app')

@section('title', __('accounting::lang.ledger'))

@section('content')

@include('accounting::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'accounting::lang.ledger' ) - {{$account->name}}</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-5">
            <div class="box box-solid">
                <div class="box-body">
                    <table class="table table-condensed">
                        <tr>
                            <th>@lang( 'user.name' ):</th>
                            <td>
                                {{$account->name_ar ?? $account->name_en}}

                                @if(!empty($account->account_number))
                                    ({{$account->account_number}})
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>@lang('accounting::lang.account_type' ):</th>
                            <td>
                                @if(!$account->isChild())
                                    {{ $account->name_ar }}
                                    @else
                                    {{ \App\MainAccount::find($account->parent_id)->name_ar}}
                                @endif
                            </td>
                        </tr>

                        <!-- <tr>
                            <th>@lang( 'accounting::lang.account_sub_type' ):</th>
                            <td>
                                @if($account->isChild())
                                    {{ \App\MainAccount::find($account->parent_id)->name_ar}}
                                    @else
                                    {{$account->name_ar}}
                                @endif
                            </td>
                        </tr> -->
 
                        <tr>
                            <th>@lang( 'accounting::lang.detail_type' ):</th>
                            <td>
                                @if(!empty($account->description))
                                    {{__('accounting::lang.' . $account->description)}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>@lang( 'lang_v1.balance' ):</th>
                            <td>{{session("currency")["symbol"]}}  {{number_format($current_bal,3,".","")}} </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-7">
        
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title"> <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters'):</h3>
                </div>
                <div class="box-body">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('all_accounts', __( 'accounting::lang.account' ) . ':') !!}
                            {!! Form::select('account_filter', [$account->id => $account->name_ar ?? $account->name_en], $account->id,
                                ['class' => 'form-control accounts-dropdown', 'style' => 'width:100%', 
                                'id' => 'account_filter', 'data-default' => $account->id]); !!}
                        </div>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-sm-12">
        	<div class="box">
                <div class="box-body">
                    @can('account.access')
                        <div class="table-responsive">
                    	<table class="table table-bordered table-striped" id="ledger">
                    		<thead>
                    			<tr>
                                    <th>@lang( 'messages.date' )</th>
                                    <th>@lang( 'lang_v1.original' )</th>
                                    <th>@lang( 'lang_v1.ref_no' )</th>
                                    <th>@lang( 'brand.note' )</th>
                                    <th>@lang( 'lang_v1.added_by' )</th>
                                    <th>@lang('account.debit')</th>
                                    <th>@lang('account.credit')</th>
                    				<th>@lang( 'lang_v1.balanceT' )</th>
                    			</tr>
                    		</thead>
                            <tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                    <td class="footer_total_debit"></td>
                                    <td class="footer_total_credit"></td>
                                    
                                    <td class="footer_total_credit_and_debit"></td>
                                </tr>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    
                                    <td></td>
                                    <td></td>
                                    <td colspan="5"><strong>@lang('lang_v1.total_balance'):</strong></td>
                                    <td class="footer_total_balance"></td>
                                    
                                </tr>
                            </tfoot>
                    	</table>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</section>

@stop

@section('javascript')
@include('accounting::accounting.common_js')
<script>
    $(document).ready(function(){        
        $('#account_filter').change(function(){
            account_id = $(this).val();
            url = base_path + '/accounting/ledger/' + account_id;
            window.location = url;
        })

        dateRangeSettings.startDate = moment().subtract(6, 'days');
        dateRangeSettings.endDate = moment();
        $('#transaction_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                
                ledger.ajax.reload();
            }
        );
        
        // Account Book
        ledger = $('#ledger').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: '{{action([\Modules\Accounting\Http\Controllers\CoaController::class, 'ledger'],[$account->id])}}',
                                data: function(d) {
                                    var start = '';
                                    var end = '';
                                    if($('#transaction_date_range').val()){
                                        start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                        end = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                    }
                                    var transaction_type = $('select#transaction_type').val();
                                    d.start_date = start;
                                    d.end_date = end;
                                    d.type = transaction_type;
                                }
                            },
                            "ordering": false,
                            columns: [
                                {data: 'operation_date', name: 'operation_date'},
                                {data: 'original', name: 'original'},
                                {data: 'ref_no', name: 'ref_no'},
                                {data: 'note', name: 'note'},
                                {data: 'added_by', name: 'added_by'},
                                {data: 'debit', name: 'amount', searchable: false},
                                {data: 'credit', name: 'amount', searchable: false},
                                {data: 'balance', name: 'balanceT', searchable: false},
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#ledger'));
                            },
                            "footerCallback": function ( row, data, start, end, display ) {
                                var footer_total_debit = 0;
                                var footer_total_credit = 0;
                                var footer_total_credit_and_debit = 0;

                                for (var r in data){
                                    footer_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;
                                    footer_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;
                                    footer_total_credit_and_debit += $(data[r].balance).data('orig-value') ? parseFloat($(data[r].balance).data('orig-value')) : 0;
                                }

                                var api = this.api();

                                // Sum the balanceT column
                                var totalBalance = api.column(7, { page: 'current' }).data().reduce(function (a, b) {
                                    return a + parseFloat(b);
                                }, 0);

                                // Update the footer
                                $(api.column(7).footer()).text(__currency_trans_from_en(totalBalance));

                                var rows = api.rows({ page: 'current' }).nodes();
                                var lastBalance = 0;
                                api.column(7, { page: 'current' }).data().each(function(value, index) {
                                    lastBalance += parseFloat(value || 0);
                                    // Update the 'balanceT' column in the current row
                                    $(rows[index]).find('td:eq(7)').text(__currency_trans_from_en(lastBalance.toFixed(2)) );
                                });

                                $('.footer_total_debit').text(__currency_trans_from_en(footer_total_debit));
                                $('.footer_total_credit').text(__currency_trans_from_en(footer_total_credit));
                                $('.footer_total_balance').text(__currency_trans_from_en(footer_total_debit - footer_total_credit));
                                
                            }
                        });
        $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#transaction_date_range').val('');
            ledger.ajax.reload();
        });
    });
</script>
@stop