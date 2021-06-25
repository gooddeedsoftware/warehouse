@extends('layouts.layouts')
@section('title',trans('permission_group.permission.title'))
@section('header')
<h3>
    <i class="icon-message"></i> {!!trans('permission_group.permission.title') !!}
</h3>
@stop

@section('help')
<p class="lead">{!!trans('permission_group.permission.title') !!}</p>
<p>{!!trans('permission_group.permission.help') !!}</p>
@stop

@section('content')
<div class='container'>
	<div class="card">
		<div class="card-header">
			<b>{!!trans('permission_group.permission.title') !!}</b>
		</div>
		<div class="card-body">
			<div class="row">
			 	<div class="col-xs-12 col-sm-6 col-md-8">
				 	<div class="form-group">
	                    <a class="btn btn-primary" href="{!! route('main.offerpermission.create') !!}" >
	                    	<i class="d-block d-sm-none fa fa-plus"></i>
	                    	<div class="d-none d-sm-block">{!!trans('main.add').' '.trans('permission_group.permission.title') !!} </div>
	                    </a>
	                </div>
				</div>
	        </div>
			<div class="table-responsive">
	            <table class="table table-striped table-hover">
	                <thead>
	                    <tr>
							<th>
								<a>@sortablelink('offergroup', trans('permission_group.group.title'))</a>
							</th>
   	                        <th> </th>
	                    </tr>
	                </thead>
					<tbody>
                    @foreach($offerpermissions as $offerpermission)
                    <tr>
                        <td><a href="{{route('main.offerpermission.edit',array($offerpermission->id))}}">{{ @$group_list[$offerpermission->group] }} - {{ @$offerpermission->module }} </a></td>
						<td class="delete-td">
							<a href="{{ route('main.offerpermission.destroy', array($offerpermission->id)) }}"
							data-method="delete"
							data-modal-text="{!!trans('main.deletemessage') !!} {!!strtolower(trans('permission_group.permission.title')) !!}?" data-csrf="{!! csrf_token() !!}">
							<i class="fas fa-trash-alt"></i>
						</a>
                       </td>
                    </tr>
                    @endforeach
					</tbody>
				</table>
			</div>
			@include('common.pagination',array('paginator'=>@$offerpermissions))
		</div>
	</div>
</div> 
@endsection
