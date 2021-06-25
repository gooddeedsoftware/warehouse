<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\ProductGroupRequest;
use App\Models\ProductGroup;
use Redirect;

class ProductGroupController extends Controller
{
    protected $route                = 'main.productGroup.index';
    protected $success              = 'success';
    protected $error                = 'error';
    protected $notfound             = 'main.notfound';
    protected $something_went_wrong = 'main.something_went_wrong';
    protected $createmsg            = 'main.productGroup_createsuccess';
    protected $updatemsg            = 'main.productGroup_updatesuccess';
    protected $deletemsg            = 'main.productGroup_deletesuccess';
    private $productGroupObj, $ganticHelperObj;

    public function __construct(ProductGroup $productGroupObj, GanticHelper $ganticHelperObj)
    {
        $this->productGroupObj = $productGroupObj;
        $this->ganticHelperObj = $ganticHelperObj;
    }

    /**
     * [index description]
     * @return [type] [description]
     */
    public function index()
    {
        $data['productGroups'] = $this->productGroupObj->getProductGroups($this->ganticHelperObj->createSearchArray(\Request::input(), 'productGroup_search'));
        return view('productGroup.index', $data);
    }

    /**
     * [create description]
     * @return [type] [description]
     */
    public function create()
    {
        return view('productGroup.form');
    }

    public function store(ProductGroupRequest $request)
    {
        return $this->productGroupObj->createRec($request->except('_token')) ? Redirect::route($this->route)->with($this->success, __($this->createmsg)) : Redirect::route($this->route)->with($this->error, __($this->something_went_wrong));
    }

    public function edit($id)
    {
        $data['productGroup'] = $this->productGroupObj->find($id);
        return $data['productGroup'] ? view('productGroup.edit', $data) : Redirect::route($this->route)->with($this->error, __($this->notfound));
    }

    public function update(ProductGroupRequest $request, $id)
    {
        return $this->productGroupObj->updateRec($request->except('_token'), $id) ? Redirect::route($this->route)->with($this->success, __($this->updatemsg)) : Redirect::route($this->route)->with($this->error, __($this->something_went_wrong));
    }

    public function destroy($id)
    {
        return $this->productGroupObj->deleteRec($id) ? Redirect::route($this->route)->with($this->success, __($this->deletemsg)) : Redirect::route($this->route)->with($this->error, __($this->notfound));
    }
}
