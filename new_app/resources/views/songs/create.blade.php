<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\SongsControllerController::class, 'store']), 'method' => 'post', 'id' => 'song_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'songs.add_song' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'song.song_name' ) . ':*') !!}
          {!! Form::text('song_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'song.song_name' )]); !!}
      </div>

      <div class="form-group">
          {!! Form::label('account_parent', __( 'accounting::lang.account_parent' ) . ':*') !!}
          <div class="input-group">
              <span class="input-group-addon">
                  <i class="fa fa-song"></i>
              </span>
                <select class="form-control accounts-dropdown select2" name="song" id="song" required>
                    <option value="">@lang('messages.please_select')</option>
                    @foreach ($selectOptions as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
          </div>
      </div>

      <div class="form-group">
          {!! Form::label('account_parent', __( 'accounting::lang.account_parent' ) . ':*') !!}
          <div class="input-group">
          <span class="input-group-addon">
                  <i class="fa fa-path"></i>
              </span>
              <select id="routeSelect"  class="form-control accounts-dropdown select2" name="routeSelect"  required>
                @foreach($routeNames as $routeName)
                    <option value="{{ $routeName }}">{{ str_replace(['.', '_', '-'], ' ', $routeName) }}</option>
                @endforeach
            </select>
          </div>
      </div>
      
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->