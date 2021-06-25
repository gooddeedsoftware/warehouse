<style type="text/css">
	body {
		padding: 0;
		margin: 0;
	}

	html {
		-webkit-text-size-adjust: none;
		-ms-text-size-adjust: none;
	}

	.table_width_100 {
		width: 680px;
	}

	label.breakword {
		word-break: break-all;
		width: 50px;
	}
</style>
<div id="mailsub" class="notification" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 320px;">
		<tr>
			<td align="center" bgcolor="#eff3f8">
				<table border="0" cellspacing="0" cellpadding="0" class="table_width_100" width="100%" style="max-width: 680px; min-width: 400px;">
					<tr>
						<td>
							<div style="height: 10px; line-height: 10px; font-size: 10px;"> </div>
						</td>
					</tr>
					<tr>
						<td class="iage_footer" align="center" bgcolor="#ffffff">
							<!-- padding -->
							<div style="height: 10px; line-height: 10px; font-size: 10px;"> </div>
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td width="5%"></td>
									<td width="20%">
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4db3a4;">
												<b>{!! Form::label('', trans('main.order_number') ) !!}</b>
											</span>
										</font>
										<br>
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #96a5b5;">
												{!! Form::label('', @$order->order_number) !!}
											</span>
										</font>
									</td>

									<td width="20%">
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4db3a4;">
												<b>{!! Form::label('', trans('main.priority') ) !!} </b>
											</span>
										</font>
										<br>
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #96a5b5;">
												{!! Form::label('', @$priorities[@$order->priority]) !!}
											</span>
										</font>
									</td>

									<td width="35%">
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4db3a4;">
												<b>{!! Form::label('', trans('main.supplier') ) !!} </b>
											</span>
										</font>
										<br>
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #96a5b5;">
												{!! Form::label('', @$order->supplier['name']) !!}
											</span>
										</font>
									</td>
								</tr>
							</table>
							<!-- padding -->
							<div style="height: 20px; line-height: 20px; font-size: 10px;"> </div>
						</td>
					</tr>

					<tr>
						<td class="iage_footer" align="center" bgcolor="#ffffff">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td width="5%"></td>
									<td width="80%">
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4db3a4;">
												<b>{!! Form::label('', trans('main.comments') ) !!}</b>
											</span>
										</font>
										<br>
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #96a5b5;">
												<label style="word-break: break-all; width: 100px;">{!! @$order->order_comment !!}</label>
											</span>
										</font>
									</td>
									<td width="15%">
									</td>

								</tr>
							</table>
							<!-- padding -->
							<div style="height: 20px; line-height: 20px; font-size: 10px;"> </div>
						</td>
					</tr>

					@if(@$order->product_details)

					<tr>
						<td class="iage_footer" align="center" bgcolor="#ffffff" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #eff2f4;">
							<!-- padding -->
							<div style="height: 10px; line-height: 10px; font-size: 10px;"> </div>

							<table width="90%" cellspacing="0" cellpadding="0" border="1">
								<tr>

									<td style="padding-left: 7px;" width="15%" align="left">
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4db3a4;">
												<b>{!! Form::label('', trans('main.nobb') ) !!}</b>
											</span>
										</font>
										<br>
									</td>

									<td style="padding-left: 7px;" align="center" width="45%">
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4db3a4;">
												<b>{!! Form::label('', trans('main.product') ) !!}</b>
											</span>
										</font>
										<br>
									</td>

									<td align="center" width="8%">
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4db3a4;">
												<b>{!! Form::label('', trans('main.qty') ) !!} </b>
											</span>
										</font>
										<br>

									</td>

									<td style="padding-left: 7px;" width="32%" align="center">
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4db3a4;">
												<b>{!! Form::label('', trans('main.comment') ) !!} </b>
											</span>
										</font>
										<br>
									</td>

								</tr>

								@foreach(json_decode(@$order->product_details) as $key => $value)
								<tr>
									@php $products = \App\Models\Product::where(['id' => $value->product_id])->first(); @endphp
									<td style="padding-left: 7px;" align="left">
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
												{!! @$products->nobb!!}
											</span>
										</font>
										<br>

									</td>

									<td style="padding-left: 7px;" align="left">
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
												{!! @$value->product_text!!}
											</span>
										</font>
										<br>

									</td>

									<td align="center">
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; ">
												{!! @$value->qty!!}
											</span>
										</font>
										<br>

									</td>

									<td style="padding-left: 7px;" align="center">
										<div style="height: 15px; line-height: 15px; font-size: 10px;"> </div>
										<font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
												{!! @$value->comment!!}
											</span>
										</font>
										<br>

									</td>

								</tr>
								@endforeach
							</table>

							<!-- padding -->
							<div style="height: 30px; line-height: 30px; font-size: 10px;"> </div>
						</td>
					</tr>
					<!--content 3 END-->
					@endif

					<!--footer -->

					<!--footer END-->
					<tr>
						<td>
							<!-- padding -->
							<div style="height: 10px; line-height: 10px; font-size: 10px;"> </div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>