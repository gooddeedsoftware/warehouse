<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\AccPlanRequest;
use App\Models\AccPlan;
use App\Models\UniAccounts;
use App\Models\UniIntegration;
use Illuminate\Http\Request;
use Redirect;
use Response;
use Session;

class AccPlanController extends Controller
{

    protected $route     = 'main.accplan.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.accplan_createsuccess';
    protected $updatemsg = 'main.accplan_updatesuccess';
    protected $deletemsg = 'main.accplan_deletesuccess';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('accplan_search') ? @Session::get('accplan_search') : [];
        @\Request::input() ? Session::put('accplan_search', array_merge($data, \Request::input())) : '';
        $data['accplans'] = AccPlan::getAccPlans(@Session::get('accplan_search'));
        return view('accplan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['uni_accounts'] = UniAccounts::selectRaw('concat(if(account_no is null, "", concat(account_no, " - ")) ,if(account_name is null, "", account_name)) as name, id')->pluck('name', 'uni_id')->toArray();
        return view('accplan.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(AccPlanRequest $request)
    {
        $input               = $request->all();
        $input['id']         = GanticHelper::gen_uuid();
        $input['ResAccount'] = $request->get('ResAccount') ? 1 : 0;
        $input['DefAccount'] = $request->get('DefAccount') ? 1 : 0;
        $input['added_by']   = Session::get('currentUserID');
        Accplan::create($input);
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
        $data['accplan'] = AccPlan::find($id);
        $data['uni_accounts'] = UniAccounts::selectRaw('concat(if(account_no is null, "", concat(account_no, " - ")) ,if(account_name is null, "", account_name)) as name, id')->pluck('name', 'uni_id')->toArray();
        if (!$data['accplan']) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        return view('accplan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(AccPlanRequest $request, $id)
    {
        $input   = $request->all();
        $accplan = AccPlan::find($id);
        if (!$accplan) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $input['ResAccount'] = $request->get('ResAccount') ? 1 : 0;
        $input['DefAccount'] = $request->get('DefAccount') ? 1 : 0;
        $input['updated_by'] = Session::get('currentUserID');
        $accplan->fill($input);
        $accplan->save();
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
        $accplan = AccPlan::find($id);
        if (!$accplan) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $accplan->delete();
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

    /**
     * [syncUNIAccounts description]
     * @return [type] [description]
     */
    public function syncUNIAccounts()
    {
        $result = UniIntegration::fetchUniAccounts();
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
