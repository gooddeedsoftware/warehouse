<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.users') !!}</b>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
						{!! Form::label('usertype_id',__('main.usertype'),array('class'=>'col-md-4 col-form-label text-md-right custom_required')) !!}
						<div class="col-md-6">
							{!! Form::select('usertype_id',@$usertypes, @$users->usertype_id,array('class'=>'form-control','required','placeholder'=>trans('main.selected'))) !!}
						</div>
				    </div>
				    <div class="form-group row">
				    	{!! Form::label('department_id',__('main.department'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::select('department_id',@$departments, @$users->department_id,array('class'=>'form-control','placeholder'=>trans('main.selected'))) !!}
						</div>
				    </div>
				    <div class="form-group row">
				    	{!! Form::label('first_name', __('main.first_name'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
						<div class="col-md-6">
							{!! Form::text('first_name',@$users->first_name,array('class'=>'form-control','required')) !!}
						</div>
				    </div>
				    <div class="form-group row">
				    	{!! Form::label('last_name',__('main.last_name'),array('class'=>'col-md-4 col-form-label text-md-right ')) !!}
						<div class="col-md-6">
							{!! Form::text('last_name',@$users->last_name,array('class'=>'form-control')) !!}
						</div>
				    </div>
				    <div class="form-group row">
				    	{!! Form::label('email', __('main.email'), array('class' => ' col-md-4 col-form-label text-md-right custom_required')) !!}
						<div class="col-md-6">
							{!! Form::email('email',@$users->email,array('class'=>'form-control','required','autocomplete'=>'false')) !!}
						</div>
				    </div>
				    <div class="form-group row">
				    	{!! Form::label('password',trans('main.password'),array('class'=>'col-md-4 col-form-label text-md-right ')) !!}
						<div class="col-md-6">
							{!! Form::password('password',array('class'=>'form-control','autocomplete'=>'new-password')) !!}
						</div>
				    </div>

				    <div class="form-group row">
				    	{!! Form::label('initials',__('main.initial'),array('class'=>'col-md-4 col-form-label text-md-right ')) !!}
						<div class="col-md-6">
							{!! Form::text('initials',@$users->initials,array('class'=>'form-control')) !!}
						</div>
				    </div>
				    <div class="form-group row">
				    	{!! Form::label('phone',__('main.phone'),array('class'=>'col-md-4 col-form-label text-md-right')) !!}
				    	<div class="col-md-6">
							{!! Form::text('phone',@$users->phone,array('class'=>'form-control')) !!}
						</div>
				    </div>

				    <div class="form-group row">
				    	{!! Form::label('activated', trans('main.activated'), array('class' => ' col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!!Form::select('activated',array('0' => __('main.active'),'1' => __('main.inactive')),@$users->activated,array('class'=>'form-control'))!!}
						</div>
				    </div>

				    <div class="form-group row">
				    	{!! Form::label('pagination_size',__('main.pagination_size'), array('class' => ' col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::select('pagination_size',array('10' => '10','20' => '20' ,'30' => '30', '50' => '50', '100' => '100'),@$users->pagination_size,array('class'=>'form-control')) !!}
						</div>
				    </div>

				    <div class="form-group row">
				    	{!! Form::label('hourly_rate',__('main.hourly_rate'), array('class' => ' col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::text('hourly_rate',@$users->hourly_rate,array('class'=>'form-control')) !!}
						</div>
				    </div>
				</div>
				<div class="col-md-6">

					<div class="form-group row">
                        {!! Form::label('uni_seller', __('main.uni_seller'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
                        <div class="col-md-6">
                        	{!! Form::select('uni_seller', @$uni_sellers, @$users->uni_seller,array('class'=>'form-control select2','placeholder'=>trans('main.selected'))) !!}
                        </div>
                    </div>

					{!! Form::hidden('signature',@$users->signature,array('class'=>'form-control', 'id'=>'user_signature')) !!}
					<div class="form-group row">
						{!! Form::label('signature', __('main.signature'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							<a role="button" class='btn btn-primary col-xs-12 col-sm-12 col-md-12 col-lg-12' data-toggle="modal" data-target='#user-signature-pad' href="#">{!! trans('main.open').' '.strtolower(trans('main.signature')) !!}</a>
						</div>
				    </div>

				    <div class="form-group row">
						{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6"  id="sigdiv">
							@if(@$users->signature)
								<img src='{!!@$users->signature !!}' id='mySignature' width="250" height="250"/>
							@endif
						</div>
				    </div>

				  	<div class="form-group row">
				    	{!! Form::label('signature_image', __('main.signature_image'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::file('signature_image',array('class'=>''),'required') !!} 
							<div class="clearfix"></div>
							<span style="color:red">{{__('main.signimage')}}</span>
						</div>
				    </div>
				    @if(@$users->signature_image)
				    	<div class="form-group row">
				    		{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
				    		<div class="col-md-6">
								<img src="{{ createImageAsBase64(storage_path() . "/uploads/user/" . $signature_image) }}"  />
							</div>
					    </div>
				    @endif

				    <div class="form-group row">
				    	{!! Form::label('user_image', __('main.userimage'), array('class' => 'col-md-4 col-form-label text-md-right')) !!}
						<div class="col-md-6">
							{!! Form::file('user_image',array('class'=>''),'required') !!} 
							<div class="clearfix"></div>
							<span style="color:red">{{__('main.signimage')}}</span>
						</div>
				    </div>
				    @if(@$users->user_image)
				    	<div class="form-group row">
				    		{!! Form::label('', '', array('class' => 'col-md-4 col-form-label text-md-right')) !!}
				    		<div class="col-md-6">
				    			<img src="{{ createImageAsBase64(storage_path() . "/uploads/user/" . $user_image) }}"  />
							</div>
					    </div>
				    @endif
				</div>
			</div>
			<div class="col-l text-center">
		    	<button type='submit' class="btn btn-primary user_submit_btn" name="user_submit_btn">{{ $btn }}</button>
		    	<a href="{{ route('main.user.index') }}" class="btn btn-danger">{{ trans('main.cancel') }}</a>
		    </div>
		</div>
		<div class="modal fade" id="user-signature-pad" role="dialog" aria-labelledby="signatureLabel">
		    <div class="modal-dialog">
				<div class="modal-content">
					<!-- Modal header-->
					<div class="modal-header">
						<h3 id="signatureLabel"><i class="icon-external-link"></i>{!! trans('main.signature') !!}</h3>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<!-- Modal body-->
					<div class="modal-body">
						<div class="m-signature-pad--body">
							<canvas></canvas>
						</div>
					</div>
					<!-- Modal footer-->
					<div class="m-signature-pad--footer">
						<div class="description">{!! trans('main.signabove') !!}</div>
						<button type="button" class="button clear btn btn-danger " data-action="clear" id='sign_clear'>{!! trans('main.clear') !!}</button>
						<button type="button" class="button save btn btn-success" data-action="save" id='sign_save'>{!! trans('main.save') !!}</button>
					</div>
				</div>
		    </div>
		</div>
	</div>
</div> 


@section('page_js')
<script type="text/javascript">
	$("#userform").validate();
	$(document).ready(function() {
		$("#email").on('change',function(){
			var posted_value=$("#email").val();
			$.post( "{!! route('main.user.validateuseremail') !!}",{email:posted_value,_token:'{!! csrf_token() !!}'}, function( data ) {
				if(data>0)
				{
					$("#email").focus();
					if(data==1){
						$("#tick").hide();
						$("#email_invalid_error").hide();

					}
					else {
						$("#wrong").show();
						$("#email_exists_error").hide();
						$("#email_invalid_error").show();
					}
				}
				else
				{
					$("#wrong").hide();
					$("#tick").show();
					$("#email_exists_error").hide();
					$("#email_invalid_error").hide();
				}
	        });
		});



		/**** signature-pad for user sign ****/
		var wrapper = document.getElementById("user-signature-pad");
		var signaturePad;
		if(wrapper){
			canvas = wrapper.querySelector("canvas");
			signaturePad = new SignaturePad(canvas);
			window.onresize = resizeCanvas;
			resizeCanvas(signaturePad);
		}
		// Adjust canvas coordinate space taking into account pixel ratio,
		// to make it look crisp on mobile devices.
		// This also causes canvas to be cleared.

		function resizeCanvas(signaturePad) {
			//signaturePad1.clear();
			// When zoomed out to less than 100%, for some very strange reason,
			// some browsers report devicePixelRatio as less than 1
			// and only part of the canvas is cleared then.
			var ratio =  Math.max(window.devicePixelRatio || 1, 1);
			width = canvas.offsetWidth;
			height =  canvas.offsetHeight;
			if(width<1){
				width=500;
				height=250;
			}
		canvas.width = width*ratio;
		canvas.height = height*ratio;
			canvas.getContext("2d").scale(ratio, ratio);
			if($("#user_signature").val())
				signaturePad.fromDataURL($("#user_signature").val());
		}


		$('#user-signature-pad').on('shown.bs.modal', function (e) {
			resizeCanvas(signaturePad);
		});


		$('#sign_clear').on('click',function(){
			signaturePad.clear();
		});
		$('#sign_save').on('click',function(){
			if (signaturePad.isEmpty()) {
				alert("{!! trans('main.providesignature') !!}");
			} else {
				$("#user_signature").val(signaturePad.toDataURL());
				var user_sign = $("#user_signature").val();
				console.log(user_sign, "user_sign")
				if($("#mySignature").length>0) {
					document.getElementById("mySignature").src = user_sign;
				}
				else
				{
					var img = $('<img id="mySignature" width="250" height="250">');
					img.attr('src', user_sign);
					img.appendTo('#sigdiv');
				}
				$('#user-signature-pad').modal('toggle');
			}
		});
	});
</script>
@endsection