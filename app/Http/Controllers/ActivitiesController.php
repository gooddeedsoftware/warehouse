<?php
namespace App\Http\Controllers;

use App\Http\Requests\ActivitiesRequest;
use App\Models\AccPlan;
use App\Models\Activities;
use App\Models\Department;
use App\Models\DropdownHelper;
use DB;
use Illuminate\Http\Request;
use Redirect;
use Response;
use Session;

class ActivitiesController extends Controller
{

    protected $route            = 'main.activities.index';
    protected $title            = 'main.activities';
    protected $success          = 'success';
    protected $error            = 'error';
    protected $notfound         = 'main.notfound';
    protected $createmsg        = 'main.activities_createsuccess';
    protected $updatemsg        = 'main.activities_updatesuccess';
    protected $deletemsg        = 'main.activities_deletesuccess';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('activities_search') ? @Session::get('activities_search') : [];
        @\Request::input() ? Session::put('activities_search', array_merge($data, @\Request::input())) : '';
        $data['activities'] = Activities::getActivities(@Session::get('activities_search'));
        return view('activities.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['department']    = Department::pluck('name', 'id');
        $data['accplans']      = AccPlan::select(DB::Raw("concat(AccountNo, ' - ', IFNULL(Name,'')) AS name,id"))->orderBy('AccountNo', 'asc')->pluck('name', 'id');
        $language              = Session::get('language') ? Session::get('language') : 'no';
        $data['category_list'] = DropdownHelper::where('language', $language)->where('groupcode', '016')->orderBy('keycode', 'asc')->pluck('label', 'keycode')->toArray();
        return view('activities.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ActivitiesRequest $request)
    {
        $activity = Activities::createOrUpdate($request->all());
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
        $data['activities'] = Activities::find($id);
        if (!$data['activities']) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $data['department']    = Department::pluck('name', 'id');
        $data['accplans']      = AccPlan::select(DB::Raw("concat(AccountNo, ' - ', IFNULL(Name,'')) AS name,id"))->orderBy('AccountNo', 'asc')->pluck('name', 'id');
        $language              = Session::get('language') ? Session::get('language') : 'no';
        $data['category_list'] = DropdownHelper::where('language', $language)->where('groupcode', '016')->orderBy('keycode', 'asc')->pluck('label', 'keycode')->toArray();
        return view('activities.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(ActivitiesRequest $request, $id)
    {
        $activity = Activities::find($id);
        if (!$activity) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $activity = Activities::createOrUpdate($request->all(), $id, $activity);
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
        $activity = Activities::find($id);
        if (!$activity) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $activity->delete();
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

}
