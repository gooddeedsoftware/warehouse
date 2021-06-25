<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\OfferPermission;
use App\Models\PermissionGroupUsers;
use Illuminate\Http\Request;
use Input;
use Redirect;
use Session;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = @Session::get('group_search') ? @Session::get('group_search') : [];
        @\Request::input() ? Session::put('group_search', array_merge($data, @\Request::input())) : '';
        $data['groups']  = Group::getGroups(@Session::get('group_search'));
        $data['modules'] = ['Offer' => trans('main.offer.title')];
        return view('group.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('group.form', Group::getDataForEditOrCreate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $group_detail = Group::where('group', '=', $request->get('group'))->where('module', '=', $request->get('module'))->get();
        if (count(@$group_detail) > 0) {
            return Redirect::back()->with('error', trans('permission_group.group.validation_msg'));
        }
        $group = Group::saveGroupDetails($request->all());
        return redirect()->route('main.group.index')->with('success', trans('permission_group.group.createmsg'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('group.edit', Group::getDataForEditOrCreate($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $group_detail = Group::where('group', '=', $request->get('group'))->where('module', '=', $request->get('module'))->where('id', '!=', $id)->get();
        if (count(@$group_detail) > 0) {
            return Redirect::back()->with('error', trans('permission_group.group.validation_msg'));
        }
        $permission = Group::updateOfferGroupDetails($request->all(), $id);
        return redirect()->route('main.group.index')->with('success', trans('permission_group.group.updatemsg'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = OfferPermission::where('group', '=', $id)->get()->toArray();
        if (@$permission) {
            return redirect()->route('main.group.index')->with('warning', trans('permission_group.group.delete_error'));
        } else {
            $group = Group::find($id);
            PermissionGroupUsers::where('group_id', $id)->delete();
            $group->delete();
            return redirect()->route('main.group.index')->with('success', trans('permission_group.group.deletemsg'));
        }

    }
}
