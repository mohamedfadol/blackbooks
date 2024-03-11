@extends('layouts.app')
@section('title', __( 'songs.songs' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'songs.songs' )
        <small>@lang( 'songs.manage_your_songs' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'songs.songs' )])
        @can('songs.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                            data-href="{{action([\App\Http\Controllers\SongsControllerController::class, 'create'])}}" 
                            data-container=".songs_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="songs_table">
                    <thead>
                        <tr>
                        <th>@lang( 'songs.song_name' )</th>
                            <th>@lang( 'songs.path' )</th>
                            <th>@lang( 'songs.nike_name' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
    @endcomponent
    
    <div class="modal fade songs_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection
