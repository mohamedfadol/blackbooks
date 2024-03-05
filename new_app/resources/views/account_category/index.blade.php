@extends('layouts.app')
@section('title', __('lang_v1.account_categories'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.account_categories' ) @show_tooltip(__('lang_v1.account_categories_help_long'))
    </h1>
</section>
 
<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action([\App\Http\Controllers\AccountCategoryController::class, 'create'])}}" 
                    data-container=".account_category_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
        @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="account_categories_table">
                    <thead>
                        <tr>
                            <th>@lang( 'lang_v1.name_ar' )</th>
                            <th>@lang( 'lang_v1.name_en' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                     
                </table>
            </div>
    @endcomponent

    <div class="modal fade account_category_modal contains_select2" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

 