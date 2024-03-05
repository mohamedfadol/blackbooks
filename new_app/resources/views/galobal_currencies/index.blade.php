@extends('layouts.app')
@section('title', __( 'lang_v1.galobal_currencies' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.galobal_currencies' )
        <small>@lang( 'lang_v1.manage_your_galobal_currencies' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.galobal_currencies' )])
        @can('galobal_currencies.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                            data-href="{{action([\App\Http\Controllers\GlobalCurrencyController::class, 'create'])}}" 
                            data-container=".galobal_currencies_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="galobal_currencies_table">
                    <thead>
                        <tr>
                            <th>@lang( 'lang_v1.global_currency_name' )</th>
                            <th>@lang( 'lang_v1.global_currency_value' )</th>
                            <th>@lang( 'lang_v1.local_currency_name' )</th>
                            <th>@lang( 'lang_v1.local_currency_value' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
    @endcomponent
    
    <div class="modal fade galobal_currencies_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection
