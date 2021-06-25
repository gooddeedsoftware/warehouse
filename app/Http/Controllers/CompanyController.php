<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\Country;
use App\Models\Files;
use Input;
use Redirect;
use Request;
use Session;

class CompanyController extends Controller
{
    protected $route     = 'main.company.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.company_createsuccess';
    protected $updatemsg = 'main.company_updatesuccess';
    protected $deletemsg = 'main.company_deletesuccess';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data['company_details'] = Company::getCompanyDetails(@Session::get('search'));
        return view('company.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['company'] = new Company();
        $data['countries'] = Country::pluck('name', 'id');
        return view('company.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(CompanyRequest $request)
    {
        $input       = $request->all();
        $input['id'] = GanticHelper::gen_uuid();
        Company::create($input);
        return Redirect::route($this->route)->with($this->success, __($this->createmsg));
    }

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
        $data['company'] = Company::findorFail($id);
        $data['countries'] = Country::pluck('name', 'id');
        return view('company.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(CompanyRequest $request, $id)
    {
        $company     = Company::findOrFail($id);
        $logo_result = 1;
        $input       = $request->all();
        if (@$input['logo_image']) {
            $logo_result = Files::uploadCompanyLogo($input['logo_image']);
        }
        $company->fill($input);
        $company->save();
        if ($logo_result == 0) {
            return Redirect::route($this->route)->with($this->error, __('main.logoimagefail'));
        } else {
            return Redirect::route($this->route)->with($this->success, __($this->updatemsg));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        if (!$company) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $company->delete();
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

}
