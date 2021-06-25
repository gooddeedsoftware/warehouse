<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\UserRequest;
use App\Models\Department;
use App\Models\DropdownHelper;
use App\Models\UniIntegration;
use App\Models\UniSellers;
use App\Models\User;
use App\Models\UserType;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Redirect;
use Response;
use Session;

class UserController extends Controller
{

    protected $route     = 'main.user.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.user_createsuccess';
    protected $updatemsg = 'main.user_updatesuccess';
    protected $deletemsg = 'main.user_deletesuccess';
    protected $user      = 'main.user';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('user_search') ? @Session::get('user_search') : [];
        @\Request::input() ? Session::put('user_search', array_merge($data, \Request::input())) : '';
        $language          = Session::get('language') ? Session::get('language') : 'no';
        $data['usertypes'] = DropdownHelper::where('language', $language)->where('groupcode', '001')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['users']     = User::getUsers(Session::get('user_search'), @Session::get('user_search')['filter_by_active_user']);
        return view('user.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['departments'] = Department::pluck('name', 'id');
        $data['usertypes']   = UserType::pluck('type', 'id');
        $language            = Session::get('language') ? Session::get('language') : 'no';
        $usertype            = Session::get('usertype');
        $data['usertypes']   = DropdownHelper::join('user_type', 'user_type.type', '=', 'dropdown_helper_view.keycode')->where('language', $language)->where('groupcode', '001')->orderBy('keycode', 'asc')->pluck('label', 'user_type.id');
        if ($usertype != "Admin" && $usertype != "Administrative") {
            $data['usertypes'] = DropdownHelper::join('user_type', 'user_type.type', '=', 'dropdown_helper_view.keycode')->where('language', $language)->where('groupcode', '001')->where('type', '!=', 'Admin')->orderBy('keycode', 'asc')->pluck('label', 'user_type.id');
        }
        $data['uni_sellers'] = UniSellers::pluck('name', 'uni_id')->toArray();
        return view('user.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(UserRequest $request)
    {
        $input = $request->all();
        if ($request->input('password') != '') {
            $input['password'] = bcrypt($request->input('password'));
        }
        $input['dept_chief']  = $request->input('dept_chief') ? "1" : "0";
        $input['id']          = GanticHelper::gen_uuid();
        $input['password']    = bcrypt($input['password']);
        $input['hourly_rate'] = $request->input('hourly_rate') != "" ? str_replace(",", ".", $request->input('hourly_rate')) : null;
        $sign_image           = User::upload_file($request, 'signature_image');
        if ($sign_image) {
            $input['signature_image'] = $sign_image;
        }
        $user_image = User::upload_file($request, 'user_image');
        if ($user_image) {
            $input['user_image'] = $user_image;
        }
        $input['remember_token'] = Str::random(60);
        $input['added_by']       = Session::get('currentUserID');
        User::create($input);
        return Redirect::route($this->route)->with($this->success, __($this->createmsg));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data['users']       = User::find($id);
        $data['departments'] = Department::pluck('name', 'id');
        $language            = Session::get('language') ? Session::get('language') : 'no';
        $usertype            = Session::get('usertype');
        $data['usertypes']   = DropdownHelper::join('user_type', 'user_type.type', '=', 'dropdown_helper_view.keycode')->where('language', $language)->where('groupcode', '001')->orderBy('keycode', 'asc')->pluck('label', 'user_type.id');
        if ($usertype != "Admin" && $usertype != "Administrative") {
            $data['usertypes'] = DropdownHelper::join('user_type', 'user_type.type', '=', 'dropdown_helper_view.keycode')->where('language', $language)->where('groupcode', '001')->where('type', '!=', 'Admin')->orderBy('keycode', 'asc')->pluck('label', 'user_type.id');
        }
        $data['users']['hourly_rate'] = $data['users']['hourly_rate'] != "" ? str_replace(".", ",", $data['users']['hourly_rate']) : null;
        if ($data['users']->signature_image) {
            $data['signature_image'] = json_decode($data['users']->signature_image)->fileName;
        }
        if ($data['users']->user_image) {
            $data['user_image'] = @json_decode($data['users']->user_image)->fileName;
        }
        $data['uni_sellers'] = UniSellers::pluck('name', 'uni_id')->toArray();
        return view('user.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UserRequest $request, $id)
    {
        $input               = $request->all();
        $input['dept_chief'] = ($request->input('dept_chief')) ? 1 : 0;
        $users               = User::find($id);
        // update blocked for normal users
        $logged_in_user = Session::get('currentUserID');
        $user_details   = User::getUser($logged_in_user);
        if ($user_details->usertype->type == "User") {
            return Redirect::route($this->route)->with($this->error, __('main.not_allowed_to_edit_user'));
        }
        // set pagination in session
        if ($id == Session::get('currentUserID')) {
            Session::put('paginate_size', $request->input('pagination_size'));
        }
        if ($request->input('password') != '') {
            $input['password'] = bcrypt($request->input('password'));
        } else {
            $input = $request->except('password');
        }
        $input['hourly_rate'] = $request->input('hourly_rate') != "" ? str_replace(",", ".", $request->input('hourly_rate')) : null;
        $sign_image           = User::upload_file($request, 'signature_image');
        if ($sign_image) {
            $input['signature_image'] = $sign_image;
        }
        $user_image = User::upload_file($request, 'user_image');
        if ($user_image) {
            $input['user_image'] = $user_image;
        }
        $input['updated_by'] = Session::get('currentUserID');
        $users->fill($input);
        $users->save();
        return Redirect::route($this->route)->with($this->success, __($this->updatemsg));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $user->delete();
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

    /**
     * [openLogoutAlertModal description]
     * @return [type] [description]
     */
    public function openLogoutAlertModal($type = false)
    {
        try {
            return view('user/partials/logout_alert');
        } catch (\Exception $e) {
            echo $e;
            exit;
        }
    }

    /**
     * [validateUserPassword description]
     * @param  Requests $request [description]
     * @return [type]            [description]
     */
    public function validateUserPassword()
    {
        try {
            $password      = $_POST['password'];
            $email         = $_POST['email'];
            $login_success = Auth::attempt(['email' => $email, 'password' => $password, 'activated' => 0]);
            return json_encode(array('login_success' => $login_success));
        } catch (\Exception $e) {
            echo $e;
            exit;
        }
    }

    /**
     * [syncSellers description]
     * @return [type] [description]
     */
    public function syncSellers()
    {
        $result = UniIntegration::fetchSellers();
        switch ($result) {
            case 0:
                return Redirect::route($this->route)->with($this->error, __('main.something_went_wrong'));
            case 1:
                return Redirect::route($this->route)->with($this->success, __('main.sync_success'));
            case 2:
                return redirect()->away(config('app.UNI_CODE_URL'));
        }
    }

}
