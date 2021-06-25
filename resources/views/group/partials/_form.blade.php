<div class='container'>
    <div class="card">
        <div class="card-header">
            <b>{!!trans('permission_group.group.stitle') !!}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        {!! Form::label('group', trans('permission_group.group.title'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::text('group',  @$group->group, array('class' => 'form-control', 'required' => 'required')) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        {!! Form::label('module', trans('permission_group.module'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::select('module', @$modules, @$group->module, array('class'=>'form-control', 'id' => 'module', 'required' => 'required', 'placeholder' => trans('main.selected'))) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        {!! Form::label('users', trans('main.offerpermission.users'), array('class' => 'col-md-4 col-form-label text-md-right custom_required')) !!}
                        <div class="col-md-6">
                            {!! Form::select('users[]', @$users, @$selected_users, array('class'=>'form-control select2', 'multiple', 'id' => 'users', 'required' => 'required',)) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <button type="submit" class="btn btn-primary" name="group_submit_btn">{!! $btn !!}</button>
                <a href="{!!route('main.group.index')!!}" class="btn btn-danger">{!!trans('main.cancel') !!}</a>
            </div>
        </div>
    </div>
</div> 
@section('page_js')
<script type="text/javascript">
    $("#groupForm").validate();
    var token = "{!! csrf_token() !!}";
    var url = "{!! URL::to('/') !!}";
    var id = "{!! @$group->id ? @$group->id : '' !!}";
    var selected_module = "{!! @$group->module !!}";
    $(document).ready(function() {
        $("#module").change(function() {
            loadUsers($(this).val());
        });
        function loadUsers(selected_module) {
            $("#users").html("");
            $('#users').select2('val',null);
            $("#users").val([]).trigger('change')
            $.ajax({
                type : "post",
                url : url + "/group/loadUsers",
                data : {
                    '_token': token,
                    'rest' : 'true',
                    'module' : selected_module,
                    'id' : id,
                },
                success : function (response) {
                    if (response) {
                        var jsonresult = $.parseJSON(response);
                        $("#users").html(jsonresult['users']);
                    }
                },
                fail : function (response) {
                    console.log("Something Went Wrong")
                }
            });
        }
    });
</script>
@endsection
