<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Models\Customer;
use App\Models\GrossMargin;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Redirect;

class GrossMarginController extends Controller
{
    protected $route                = 'main.grossMargin.index';
    protected $success              = 'success';
    protected $error                = 'error';
    protected $notfound             = 'main.notfound';
    protected $something_went_wrong = 'main.something_went_wrong';
    protected $createmsg            = 'main.grossMargin_createsuccess';
    protected $updatemsg            = 'main.grossMargin_updatesuccess';
    protected $deletemsg            = 'main.grossMargin_deletesuccess';
    private $grossMarginObj, $ganticHelperObj;

    public function __construct(GrossMargin $grossMarginObj, GanticHelper $ganticHelperObj)
    {
        $this->grossMarginObj  = $grossMarginObj;
        $this->ganticHelperObj = $ganticHelperObj;
    }

    /**
     * [index description]
     * @return [type] [description]
     */
    public function index()
    {
        $data['grossMargins'] = $this->grossMarginObj->getGrossMargins($this->ganticHelperObj->createSearchArray(\Request::input(), 'grossMargin_search'));
        return view('grossMargin.index', $data);
    }

    /**
     * [create description]
     * @return [type] [description]
     */
    public function create()
    {
        $data              = array();
        $data['suppliers'] = Customer::where('is_supplier', '=', 1)->where('status', '=', '0')->pluck('name', 'id');
        $data['groups']    = ProductGroup::where('status', '=', '0')->pluck('name', 'id');
        return view('grossMargin.form', $data);
    }

    /**
     * [store description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function store(Request $request)
    {
        return $this->grossMarginObj->createRec($request->except('_token')) ? Redirect::route($this->route)->with($this->success, __($this->createmsg)) : Redirect::route($this->route)->with($this->error, __($this->something_went_wrong));
    }

    /**
     * [edit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function edit($id)
    {
        $data                = array();
        $data['grossMargin'] = $this->grossMarginObj->find($id);
        $data['suppliers']   = Customer::where('is_supplier', '=', 1)->where(function ($query) {
            $query->where('status', '=', '0');
            $query->orWhere('id', '=', @$data['grossMargin']->supplier);
        })->pluck('name', 'id');
        $data['groups'] = ProductGroup::where('status', '=', '0')->orWhere('id', @$data['grossMargin']->product_group)->pluck('name', 'id');
        return $data['grossMargin'] ? view('grossMargin.edit', $data) : Redirect::route($this->route)->with($this->error, __($this->notfound));
    }

    /**
     * [update description]
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function update(Request $request, $id)
    {
        return $this->grossMarginObj->updateRec($request->except('_token'), $id) ? Redirect::route($this->route)->with($this->success, __($this->updatemsg)) : Redirect::route($this->route)->with($this->error, __($this->something_went_wrong));
    }

    /**
     * [destroy description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function destroy($id)
    {
        return $this->grossMarginObj->deleteRec($id) ? Redirect::route($this->route)->with($this->success, __($this->deletemsg)) : Redirect::route($this->route)->with($this->error, __($this->notfound));
    }
}
