<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\Restaurant\KitchenController::class, 'update'], [$kitchen->id]), 'method' => 'PUT', 'id' => 'edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'restaurant.edit_modifier' )</h4>
    </div>

    <div class="modal-body">

      <div class="row">
        
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('name', __( 'restaurant.modifier_set' ) . ':*') !!}
            {!! Form::text('name', $kitchen->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name' ) ]); !!}
          </div>
        </div>

        <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'catergories' ) . ':*') !!}
                    <select name="category_id" class="form-control select2">\
                        <option>@lang('messages.please_select')</option>
                        @foreach($categories as $category) 
                            <option value="{{$category->id}}">{{$category->name}} {{$category->short_code}}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group col-md-12">
                    <table class="table table-slim"> 
                        <thead>
                        <tr>
                            <th>@lang('sale.location')</th>
                        </tr>
                        @foreach($locations as $key => $value)
                            <tr>
                                <td>{{$value}}</td>
                            </tr>
                        @endforeach
                        </thead>
                    </table>
                </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->