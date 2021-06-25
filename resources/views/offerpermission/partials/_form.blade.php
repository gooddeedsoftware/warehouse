<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!trans('permission_group.group.title') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('group', trans('permission_group.group.stitle'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::select('group',@$group_list, @$offerpermission->group, array('class' => 'form-control', 'required' => 'required', 'placeholder'=>trans('main.selected'))) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        {!! Form::label('permission', trans('permission_group.permission.title'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::select('permissions[]', @$permissions, @$selected_permissions, array('class'=>'form-control select2', 'multiple', 'id' => 'users', 'required' => 'required',)) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <button type="submit" class="btn btn-primary" name="offerpermission_submit_btn">{!! $btn !!}</button>
                <a href="{!!route('main.offerpermission.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
            </div>
        </div>
    </div>
</div> 
@section('page_js')
<script type="text/javascript">
    $("#permission_group_form").validate();
</script>
@endsection

