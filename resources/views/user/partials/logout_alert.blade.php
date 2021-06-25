<!-- modal content start -->
<div class="modal-header">
    <strong>
        {!! trans('main.loggout_session_expired') !!}
    </strong>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 modal-div-position">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}" autocomplete="on">
                {!! csrf_field() !!}

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="col-md-4 control-label custom_required">{!! trans('main.email') !!}</label>
                    <div class="col-md-6">
                        {!! Form::text('user_login_email',  Auth::user()->email, array('class' => 'form-control user_login_email' , 'id' => 'user_login_email','disabled' => 'disabled', 'readonly' => 'true')) !!}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="col-md-4 control-label custom_required">{!! trans('main.password') !!}</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control user_login_password" id="user_login_password" name="user_login_password" autocomplete="on">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="button" class="btn btn-primary user_login_btn" id="user_login_btn">
                            <i class="fa fa-btn fa-sign-in"></i>{!! trans('main.login') !!}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--- modal content end -->
<script type="text/javascript">
    var user_login_notify_text = "{!! trans('main.user.user_not_found') !!}";
    var message = '{!! trans("main.message") !!}';
    var password_required = "{!! trans('main.password_required') !!}";
    $(document).ready(function () {

        $('input').keypress(function (e) {
            if (e.which == '13') {
                $("#user_login_btn").trigger("click");
            }
        });

        $("#user_login_btn").click(function () {
            if ($(".user_login_password").val() == "") {
                alert(password_required);
            } else {
                var url = "{!! URL::to('/') !!}";
                var token = "{!! csrf_token() !!}";
                var password_check_url = url+"/user/validateuserpassword";
                var password = $('.user_login_password').val();
                var email = $('.user_login_email').val();
                $.ajax({
                    type: "POST",
                    url: password_check_url,
                    data : {
                        '_token': token,
                        'rest' : 'true',
                        "password" : password,
                        "email" : email,
                    },
                    success: function (response) {
                        if (response) {
                        var jsonresult = $.parseJSON(response);
                            if (jsonresult['login_success'] == true) {
                                $('.user_login_password').val("");
                                $('#users_logout_model').modal('hide');
                                window.localStorage.setItem("ReloadState", false);
                                window.localStorage.setItem("logout_user", false);
                            } else {
                                new PNotify({
                                    title : message,
                                    text : user_login_notify_text,
                                    type : "error"
                                });
                                $('.user_login_password').val("");
                                $('.user_login_password').focus();
                            }

                        }
                    },
                    fail: function() {
                        console.log("Something went wrong");
                    }
                });

            }
        });
    });
</script>