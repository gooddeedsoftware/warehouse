<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\EquipmentCategoryRequest;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Redirect;
use Session;

class EquipmentCategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    protected $route     = 'main.equipmentcategory.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.equipmentcategory_createsuccess';
    protected $updatemsg = 'main.equipmentcategory_updatesuccess';
    protected $deletemsg = 'main.equipmentcategory_deletesuccess';

    public function index()
    {
        $data = @Session::get('equipmentcategory_search') ? @Session::get('equipmentcategory_search') : [];
        @\Request::input() ? Session::put('equipmentcategory_search', array_merge($data, @\Request::input())) : '';
        $data['equipmentcategories'] = EquipmentCategory::getEquipmentCategories(@Session::get('equipmentcategory_search'));
        return view('equipmentcategory.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('equipmentcategory.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(EquipmentCategoryRequest $request)
    {
        $input       = $request->all();
        $input['id'] = GanticHelper::gen_uuid();
        EquipmentCategory::create($input);
        return Redirect::route($this->route)->with($this->success, __($this->createmsg));
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
        $data['equipmentcategory'] = EquipmentCategory::find($id);
        if ($data['equipmentcategory']) {
            return view('equipmentcategory.edit', $data);
        } else {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(EquipmentCategoryRequest $request, $id)
    {
        $equipmentcategory = EquipmentCategory::find($id);
        $input             = $request->all();
        $equipmentcategory->fill($input);
        $equipmentcategory->save();
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
        $equipmentcategory = EquipmentCategory::find($id);
        $equipmentcategory->delete($id);
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

}
