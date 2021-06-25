<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.accplan') !!}</b>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
			      		{!! Form::label('AccountNo', __('main.account_no'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			      		<div class="col-md-6">
				      		{!! Form::text('AccountNo',@$accplan->AccountNo,array('class'=>'form-control', 'required', 'max-length'=> 11)) !!}
				      	</div>
				    </div>

				    <div class="form-group row">
				     	{!! Form::label('name',__('main.name'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
				     	<div class="col-md-6">
				      		{!! Form::text('Name', @$accplan->Name, array('class'=>'form-control', 'required', 'max-length'=>80)) !!}
				      	</div>
				    </div>

				    <div class="form-group row">
				     	{!! Form::label('AccountGroup',__('main.account_group'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
				     	<div class="col-md-6">
				      		{!! Form::text('AccountGroup', @$accplan->AccountGroup,array('class'=>'form-control','max-length'=>80)) !!}
				      	</div>
				    </div>

				    <div class="form-group row">
				     	{!! Form::label('ResAccount',__('main.res_account'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
				     	<div class="col-md-6">
				      		@if(@$accplan->ResAccount)
								{!! Form::checkbox('ResAccount', '1', @$accplan->ResAccount, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.yes'), "data-off" => trans('main.no'), "id" => "ResAccount", "checked" => "checked")) !!}
							@else
								{!! Form::checkbox('ResAccount', '1', @$accplan->ResAccount, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.yes'), "data-off" => trans('main.no'), "id" => "ResAccount")) !!}
							@endif
						</div>
				    </div>

				    <div class="form-group row">
				     	{!! Form::label('TaxCode', __('main.tax_code'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
				     	<div class="col-md-6">
				      		{!! Form::text('TaxCode', @$accplan->TaxCode,array('class'=>'form-control', 'max-length'=>'11')) !!}
				      	</div>
				    </div>

				    <div class="form-group row">
				     	{!! Form::label('DefAccount',__('main.def_account'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
				     	<div class="col-md-6">
				      		@if(@$accplan->DefAccount)
								{!! Form::checkbox('DefAccount', '1', @$accplan->DefAccount, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.yes'), "data-off" => trans('main.no'), "id" => "DefAccount", "checked" => "checked")) !!}
							@else
								{!! Form::checkbox('DefAccount', '1', @$accplan->DefAccount, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" => trans('main.yes'), "data-off" => trans('main.no'), "id" => "DefAccount")) !!}
							@endif
						</div>
				    </div>

			     	<div class="form-group row">
                        {!! Form::label('uni_account', __('main.uni_account'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                        	{!! Form::select('uni_id',@$uni_accounts, @$accplan->uni_id,array('class'=>'form-control select2','placeholder'=>trans('main.selected'))) !!}
                        </div>
                    </div>
				</div>
			</div>
			<div class="col-md-6 text-center">
				<button type="submit" class="btn btn-primary accplan_submit_btn" name="accplan_submit_btn">{!! $btn !!}</button>
				<a href="{!!route('main.accplan.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
			</div>
		</div>
	</div>
</div> 
@section('page_js')
<script type="text/javascript">
	$("#accplanform").validate();
</script>
@endsection
