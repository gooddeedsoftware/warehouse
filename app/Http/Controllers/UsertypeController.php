<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\UserTypeRequest;
use App\Models\DropdownHelper;
use App\Models\Permission;
use App\Models\UserType;
use Input;
use Redirect;
use Session;

class UsertypeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    protected $folder    = 'usertype';
    protected $route     = 'main.usertype.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.usertype.notfound';
    protected $createmsg = 'main.usertype.createsuccess';
    protected $updatemsg = 'main.usertype.updatesuccess';
    protected $deletemsg = 'main.usertype.deletesuccess';

    public function index()
    {
        $data = @Session::get('user_type_search') ? @Session::get('user_type_search') : [];
        @Input::all() ? Session::put('user_type_search', array_merge($data, @Input::all())) : '';
        $data['neworder']  = array();
        $querystring       = $_GET;
        $data['sortorder'] = 'type';
        $data['sortby']    = 'asc';
        if (!empty($querystring)) {
            $data             = $this->get_querystring($querystring, 'type', 'desc');
            $data['neworder'] = $this->construct_orderby($data['orderby']);
        }
        $language               = Session::get('language') ? Session::get('language') : 'no';
        $data['usertypes_list'] = DropdownHelper::where('language', $language)->where('groupcode', '001')->orderBy('keycode', 'asc')->lists('label', 'keycode')->toArray();
        $data['usertypes']      = UserType::getUsertypes(@Session::get('user_type_search'), $data['sortorder'], $data['sortby']);
        return view('usertype.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['permissions']     = Permission::all();
        $data['userPermissions'] = array();
        $language                = Session::get('language') ? Session::get('language') : 'no';
        $data['usertypes']       = DropdownHelper::where('language', $language)->where('groupcode', '001')->orderBy('keycode', 'asc')->lists('label', 'keycode');
        return view('usertype.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(UserTypeRequest $request)
    {
        $input = $request->all();

        $input['id']          = GanticHelper::gen_uuid();
        $input['permissions'] = $request->input('permissions') != "" ? json_encode($request->input('permissions')) : "";
        UserType::create($input);

        return Redirect::route($this->route)->with($this->success, trans($this->createmsg));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {

        $language          = Session::get('language') ? Session::get('language') : 'no';
        $data['usertypes'] = DropdownHelper::where('language', $language)->where('groupcode', '001')->orderBy('keycode', 'asc')->lists('label', 'keycode');
        $data['usertype']  = $user  = UserType::findOrFail($id);
        if ($user) {
            $data['permissions']     = Permission::all();
            $data['userPermissions'] = Permission::formatpermission($user->permissions);
            return view('usertype.edit', $data);
        } else {
            return Redirect::route($this->route)->with($this->error, trans($this->notfound));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UserTypeRequest $request, $id)
    {
        $user                 = UserType::findOrFail($id);
        $input                = $request->all();
        $input['permissions'] = $request->input('permissions') != "" ? json_encode($request->input('permissions')) : "";
        $user->fill($input);
        $user->save();
        return Redirect::route($this->route)->with($this->success, trans($this->updatemsg));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = UserType::findOrFail($id);
        $user->delete($id);
        return Redirect::route($this->route)->with($this->success, trans($this->deletemsg));
    }

}
