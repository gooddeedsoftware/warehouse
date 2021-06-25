<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><p>{!!trans('main.supplier.title') !!}</p></div>
		<div class="panel-body">
			<div class='container'>
				<div class="row">
                    
                   <div class="form-group">
                        {!! Form::label('name', trans('main.supplier.title'), array('class' => 'col-xs-4 col-sm-4 col-md-2 col-lg-2 control-label custom_required')) !!}
                        <div class="col-xs-8 col-sm-3 col-md-3 col-lg-2">
                                {!! Form::text('name', @$supplier->name, array('class'=>'form-control','required', 'placeholder' => trans('main.supplier.name') )) !!}    
                        </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
				    </div>
				    <div class="col-xs-8 col-sm-3 col-md-3 col-lg-2">
                            <button type="submit" class="btn btn-primary">{!! $btn !!}</button>
                            <a href="{!!route('main.supplier.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
                        </div>
                    </div>
                            
                </div>
    		</div> 
        </div>
    </div> 
</div>