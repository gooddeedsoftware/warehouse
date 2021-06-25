<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!  __('main.activity') !!}</b>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
			      		{!! Form::label('department_id',  __('main.department'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
			      		<div class="col-md-6">
							{!! Form::select('department_id',@$department, @$activities->department_id,array('class'=>'form-control','placeholder'=>  __('main.selected'),'required')) !!}
						</div>
				    </div>	

				    <div class="form-group row">
				     	{!! Form::label('ltkode',   __('main.ltkode'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
			      		<div class="col-md-6">
							{!! Form::text('ltkode',@$activities->ltkode,array('class'=>'form-control','required','max-length'=>10)) !!}
						</div>
				    </div>	
				    <div class="form-group row">
				     	{!! Form::label('fk_AccountNo',  __('main.account_no'),array('class'=>'col-md-4 col-form-label text-md-right', 'id' => 'fk_accountno_label')) !!}
			      		<div class="col-md-6">
							{!! Form::select('fk_AccountNo',@$accplans, @$activities->fk_AccountNo,array('class'=>'form-control','placeholder'=>  __('main.selected'))) !!}
						</div>
				    </div>	

				    <div class="form-group row">
				     	{!! Form::label('unit',  __('main.unit'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
			      		<div class="col-md-6">
							{!! Form::text('unit',@$activities->unit,array('class'=>'form-control','max-length'=>10)) !!}
						</div>
				    </div>	
				    
				    <div class="form-group row">
				     	{!! Form::label('billable',  __('main.billable'), array('class'=>'col-md-4 col-form-label text-md-right')) !!}
				     	<div class="col-md-6">
							@if (@$activities->billable)
								{!! Form::checkbox('billable', '1', @$activities->billable, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" =>   __('main.yes'), "data-off" =>   __('main.no'), "id" => "billable", "checked" => "checked")) !!}
							@else
								{!! Form::checkbox('billable', '1', @$activities->billable, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" =>   __('main.yes'), "data-off" =>   __('main.no'), "id" => "billable")) !!}
							@endif
						</div>
				    </div>

				     <div class="form-group row" id="category_div">
				     	{!! Form::label('category',  __('main.category'),array('class'=>'col-md-4 col-form-label text-md-right', 'id' => 'fk_accountno_label')) !!}
				     	<div class="col-md-6">
				      		{!! Form::select('category',@$category_list, @$activities->category,array('class'=>'form-control','placeholder'=>  __('main.selected'))) !!}
				      	</div>
				    </div>

			    	<div class="form-group row">
						{!! Form::label('comments',  __('main.comments'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::textarea('comments',@$activities->comments,array('class'=>'form-control', 'rows' => 3)) !!}
						</div>
					</div>
				</div>
			    <div class="col-md-6">
			    	<div class="form-group row">
				     	{!! Form::label('travel_expense',  __('main.travel_expense'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
				     	<div class="col-md-6">
							@if (@$activities->travel_expense)
							    {!! Form::checkbox('travel_expense', '1', @$activities->travel_expense, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" =>   __('main.yes'), "data-off" =>   __('main.no'), "id" => "travel_expense", "checked" => "checked")) !!}
							@else
							    {!! Form::checkbox('travel_expense', '1', @$activities->travel_expense, array("data-toggle" => "toggle",'data-offstyle' => "btn btn-secondary", "data-on" =>   __('main.yes'), "data-off" =>   __('main.no'), "id" => "travel_expense")) !!}
						    @endif
						</div>
				    </div>

				    <div class="form-group row">
				     	{!! Form::label('show_to_all',  __('main.show_to_all'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
				     	<div class="col-md-6">
							@if (@$activities->show_to_all)
							    {!! Form::checkbox('show_to_all', '1', @$activities->show_to_all, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" =>   __('main.yes'), "data-off" =>   __('main.no'), "id" => "show_to_all", "checked" => "checked")) !!}
							@else
							    {!! Form::checkbox('show_to_all', '1', @$activities->show_to_all, array("data-toggle" => "toggle", 'data-offstyle' => "btn btn-secondary", "data-on" =>   __('main.yes'), "data-off" =>   __('main.no'), "id" => "show_to_all")) !!}
						    @endif
						</div>
				    </div>

					<div class="form-group row">
						{!! Form::label('wgsrt_wagetype',  __('main.wgsrt_wagetype'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::Number('wgsrt_wagetype',@$activities->wgsrt_wagetype,array('class'=>'form-control')) !!}
						</div>
					</div>

					<div class="form-group row">
						{!! Form::label('description',  __('main.description'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('description',@$activities->description,array('class'=>'form-control')) !!}
						</div>
					</div>

					<div class="form-group row">
						{!! Form::label('invoice_text',  __('main.invoice_text'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('invoice_text',@$activities->invoice_text,array('class'=>'form-control')) !!}
						</div>
					</div>

					<div class="form-group row">
						{!! Form::label('price',  __('main.price'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::Number('price',@$activities->price,array('class'=>'form-control')) !!}
						</div>
					</div>

					<div class="form-group row">
						{!! Form::label('VAT',  __('main.vat'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::Number('VAT',@$activities->VAT,array('class'=>'form-control')) !!}
						</div>
					</div>

				
				</div>	
			</div>
			<div class="col text-center">
				<button type='submit' class="btn btn-primary department_submit_btn" name="department_submit_btn">{{$btn}}</button>
				<a href="{{ route('main.activities.index') }}" class="btn btn-danger">{{   __('main.cancel') }}</a>
			</div>
		</div>
	</div>
</div> 
@section('page_js')
	<script type="text/javascript">
		$("#activityform").validate();
		$(document).ready(function () { 
			$("#billable").on("change", function() { 
				if ($("#billable").is(":checked")) {
					$("#fk_accountno_label").addClass("custom_required");
					$("#fk_AccountNo").attr("required", "true");
					$("#category_div").hide();
				} else {
					$("#fk_accountno_label").removeClass("custom_required");
					$("#fk_AccountNo").removeAttr("required");
					$("#category_div").show();
				}
			});
			var billable = "{!! @$activities->billable !!}";
			if (billable == "1") {
				$("#category_div").hide();	
			}
		});
	</script>
@endsection