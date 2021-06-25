<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!! __('main.company') !!}</b>
        </div>
        <div class="card-body">
        	<div class="row">
        		<div class="col-md-6">
	                <div class="form-group row">
						{!! Form::label('name',__('main.name'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('name',@$company->name,array('class'=>'form-control required')) !!}
						</div>
					</div>
					<div class="form-group row">
						{!! Form::label('IBAN',__('main.IBAN'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('IBAN',@$company->IBAN,array('class'=>'form-control')) !!}
						</div>
					</div>				

					<div class="form-group row">
						{!! Form::label('BIC',__('main.BIC'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('BIC',@$company->BIC,array('class'=>'form-control')) !!}
						</div>
					</div>

					<div class="form-group row">
						{!! Form::label('account_number',__('main.account_no'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('account_number',@$company->account_number,array('class'=>'form-control')) !!}
						</div>
					</div>

					<div class="form-group row">
						{!! Form::label('company_information',__('main.company_information'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::textarea('company_information',@$company->company_information,array('class'=>'form-control','rows'=>'2','cols'=>'3')) !!}
						</div>
					</div> 

					<div class="form-group row">
						{!! Form::label('company_email',trans('main.email'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('company_email',@$company->company_email,array('class'=>'form-control')) !!}
						</div>
					</div> 

					<div class="form-group row">
						{!! Form::label('company_VAT',__('main.vat'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('company_VAT',@$company->company_VAT,array('class'=>'form-control')) !!}
						</div>
					</div> 

				</div>
				<div class="col-md-6">
					<div class="form-group row">
						{!! Form::label('post_address',__('main.post_address'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::textarea('post_address',@$company->post_address,array('class'=>'form-control','rows'=>'2','cols'=>'3')) !!}
						</div>
					</div>
					
					<div class="form-group row">
						{!! Form::label('zip',trans('main.zip'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('zip',@$company->zip,array('class'=>'form-control')) !!}
						</div>
					</div>
					
					<div class="form-group row">
						{!! Form::label('city',trans('main.city'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('city',@$company->city,array('class'=>'form-control')) !!}
						</div>
					</div>

					<div class="form-group row">
					    {!! Form::label('country', __('main.country'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
					    <div class="col-md-6">
					        {!! Form::select('country',@$countries, @$company->country,array('class'=>'form-control','required','placeholder'=>trans('main.selected'))) !!}
					    </div>
					</div>

					<div class="form-group row">
						{!! Form::label('phone',trans('main.phone'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('phone',@$company->phone,array('class'=>'form-control')) !!}
						</div>
					</div> 

					<div class="form-group row">
						{!! Form::label('web_page',trans('main.web_page'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('web_page',@$company->web_page,array('class'=>'form-control')) !!}
						</div>
					</div>

					<div class="form-group row">
						<label class="col-md-4 col-form-label text-md-right" style="visibility: hidden;">sapce</label>
						<div class="col-md-6">
							<button type="button" class="btn btn-primary file_upload_button form-control">{!! __('main.upload_logo') !!}</button>
							<span style="color:red">{{__('main.logoimage')}}</span>
						</div>
			        	<input class="" accept="image/x-png" name="logo_image" type="file" id="file_upload" style="display: none">
					</div>

					<div class="form-group row">
						<label class="col-md-4 col-form-label text-md-right" style="visibility: hidden;">sapce</label>
						<div class="col-md-6">
			        		<img src="{!! URL::to('/') !!}/images/maskinstyring_report_logo.png?{!! time() !!}" alt="Logo here" />
						</div>
					</div>

				</div>
			</div>
            <div class="col text-center">
				<button type='submit' class="btn btn-primary company_submit_btn" name="company_submit_btn">{{$btn}}</button>
				<a href="{{ route('main.company.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
			</div>
        </div>
    </div>
</div> 
@section('page_js')
	<script type="text/javascript">
		$(document).ready(function () {
			$("#companyForm").validate();
			$(".file_upload_button").click(function() {
			    $("#file_upload").click();
			})
		});
	</script>
@endsection
