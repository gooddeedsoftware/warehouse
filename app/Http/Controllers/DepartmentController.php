<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Models\UniDepartment;
use App\Models\UniIntegration;
use Illuminate\Http\Request;
use Redirect;
use Session;

class DepartmentController extends Controller
{
    protected $route     = 'main.department.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $createmsg = 'main.department_createsuccess';
    protected $updatemsg = 'main.department_updatesuccess';
    protected $deletemsg = 'main.department_deletesuccess';
    protected $notfound  = 'main.notfound';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('department_search') ? @Session::get('department_search') : [];
        @\Request::input() ? Session::put('department_search', array_merge($data, \Request::input())) : '';
        $data['departments'] = Department::getDepartmentDetails(Session::get('department_search'));
        return view('department.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['uni_departments'] = UniDepartment::pluck('name', 'uni_id')->toArray();
        return view('department.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(DepartmentRequest $request)
    {
        $input             = $request->all();
        $input['id']       = GanticHelper::gen_uuid();
        $input['added_by'] = Session::get('currentUserID');
        $department        = Department::create($input);
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
        $data['departments'] = Department::find($id);
        if (!$data['departments']) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $data['uni_departments'] = UniDepartment::pluck('name', 'uni_id')->toArray();
        return view('department.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(DepartmentRequest $request, $id)
    {
        $department = Department::find($id);
        if (!$department) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $input               = $request->all();
        $input['updated_by'] = Session::get('currentUserID');
        $department->fill($input);
        $department->save();
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
        $department = Department::find($id);
        if (!$department) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $department->delete();
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

    /**
     * [syncDepartment description]
     * @return [type] [description]
     */
    public function syncDepartment()
    {
        $result = UniIntegration::fetchUniDepartments();
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
