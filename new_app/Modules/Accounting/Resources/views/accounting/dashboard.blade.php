@extends('layouts.app')

@section('title', __('accounting::lang.accounting'))

@section('content')
    @include('accounting::layouts.nav')
    
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group pull-right">
                        <div class="input-group">
                        <button type="button" class="btn btn-primary" id="dashboard_date_filter">
                            <span>
                            <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                            </span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                        </div>
                </div>
            </div>
        </div>
        <div class="row">
            
        </div>

        <div class="row">
            @foreach($all_charts as $key => $chart)
            <div class="col-md-6">
                @component('components.widget', ['class' => 'box-primary', 
                'title' => __('accounting::lang.' . $key)])
                {!! $chart->container() !!}
                @endcomponent
            </div>
            @endforeach
        </div>
    </section>
@stop

@section('javascript')
{!! $coa_overview_chart->script() !!}
@foreach($all_charts as $key => $chart)
{!! $chart->script() !!}

<script type="text/javascript">
    $(document).ready( function(){
        dateRangeSettings.startDate = moment('{{$start_date}}', 'YYYY-MM-DD');
        dateRangeSettings.endDate = moment('{{$end_date}}', 'YYYY-MM-DD');
        $('#dashboard_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#dashboard_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );  
            
            var start = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');

            var end = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            var url = "{{action([\Modules\Accounting\Http\Controllers\AccountingController::class, 'dashboard'])}}?start_date=" + start + '&end_date=' + end;

            window.location.href = url;
        });
    });
</script>
@endforeach


@stop