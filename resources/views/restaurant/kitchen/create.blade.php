<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action([\App\Http\Controllers\Restaurant\KitchenController::class, 'store']), 'method' => 'post',
        'id' => 'table_add_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'lang_v1.add_kitchen' )</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-12">
                    {!! Form::label('name', __( 'tax_rate.name' ) . ':*') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'tax_rate.name' )]); !!}
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
        </div> 
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->