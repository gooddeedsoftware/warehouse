<?php 
namespace App\Http\Controllers;
use Lang;
use Input;
use Session;
use Request;
use Redirect;
use App\Models\Permission;
use App\Http\Requests\PermissionRequest;

class PermissionController extends MaskinstyringController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

//        if (!Request::get('page')) {
//            Session::put('search', '');
//        }
//        if (Input::get('search')) {
//            Session::put('search', Input::all());
//            $data['search_string'] = Input::get('search');
//        }
        $data = @Session::get('permission_search') ? @Session::get('permission_search') : [];
        @Input::all() ? Session::put('permission_search', array_merge($data, @Input::all())) : '' ;

        $data['neworder'] = array();
        $querystring = $_GET;
        $data['sortorder'] = 'name';
        $data['sortby'] = 'asc';
        if (!empty($querystring)) {
            $data = $this->get_querystring($querystring,'name','desc');
            $data['neworder'] = $this->construct_orderby($data['orderby']);
        }
		
        $data['permissions'] = Permission::search_permission(@Session::get('permission_search'),$data['sortorder'],$data['sortby']);
        return view('permissions.index', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['options'] = array('create' => 'create', 'edit' => 'edit', 'view' => 'view', 'delete' => 'delete', 'update' => 'update');
        return view('permissions.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(PermissionRequest $request)
    {
        $input = $request->all();
        $inputn['name'] = ucfirst($request->get('name'));
        $inputn['permissions'] = Permission::getper($input);
        $inputn['slug'] = strtolower($request->get('slug'));
        Permission::create($inputn);
        Session::flash('success', Lang::get('main.permission.createsuccess'));
        return Redirect::route('main.permission.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $data['permission'] = $permission = Permission::findOrFail($id);
        return view('permissions.view', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data['permission'] = $permission = Permission::findOrFail($id);
        $options = array('create' => 'create', 'edit' => 'edit', 'view' => 'view', 'delete' => 'delete', 'update' => 'update');
        if (@$permission->permissions) {
            $json = json_decode($permission->permissions);

            foreach ($json as $value) {
                $newarray[] = str_replace(strtolower($permission->slug) . '.', '', $value);
                $options[str_replace(strtolower($permission->slug) . '.', '', $value)] = str_replace(strtolower($permission->slug) . '.', '', $value);
            }
        }
        $data['options'] = $options;
        $data['newarray'] = $newarray;

        return view('permissions.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(PermissionRequest $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $input = $request->all();

        $inputn['name'] = ucfirst($request->get('name'));
        $inputn['slug'] = strtolower($request->get('slug'));
        $inputn['permissions'] = Permission::getper($input);
        $permission->fill($inputn);
        $permission->save() == true ? Session::flash('success', Lang::get('main.permission.updatesuccess')) :
        Session::flash('message', Lang::get('main.permission.fail'));
        return Redirect::route('main.permission.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        Session::flash('success', Lang::get('main.permission.deletesuccess'));
        return Redirect::route('main.permission.index');

    }

}
