@extends('layouts.layouts')
@section('title',__('main.company'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!__('main.company') !!}
</h3>
@stop
@section('help')
<p class="lead">{!!__('main.company') !!}</p>
<p>{!!__('main.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!__('main.company') !!}</b>
		</div>
		<div class="card-body">
			<div class="table-responsive">
	             <table class="table table-striped table-hover">
	                <thead>
	                    <tr>
							<th>{!!trans('main.name') !!}</th>
							<th>{!!__('main.IBAN') !!}</th>
							<th>{!!__('main.BIC') !!}</th>
							<th>{!!trans('main.email') !!}</th>
							<th>{!!trans('main.phone') !!}</th>
						</tr>
	                </thead>
	                <tbody>
	                    @foreach(@$company_details as $company)
	                    <tr>
							<td><a href="{{route('main.company.edit',array($company->id))}}">
							{{ htmlspecialchars(@$company->name) }}</a></td>
							<td>{{ htmlspecialchars(@$company->IBAN) }}</td>			
							<td>{{ htmlspecialchars(@$company->BIC) }}</td>
							<td>{{ htmlspecialchars(@$company->company_email) }}</td>
							<td>{{ htmlspecialchars(@$company->phone) }}</td>
						</tr>
						@endforeach
	                </tbody>
	            </table>
			</div>
		</div>
	</div>
</div> 
@endsection
