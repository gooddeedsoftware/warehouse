<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Models\Country;
use Illuminate\Http\Request;
use Redirect;
use Session;

class CountryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    protected $route     = 'main.country.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.country_createsuccess';
    protected $updatemsg = 'main.country_updatesuccess';
    protected $deletemsg = 'main.country_deletesuccess';

    public function index()
    {
        $data = @Session::get('country_search') ? @Session::get('country_search') : [];
        @\Request::input() ? Session::put('country_search', array_merge($data, @\Request::input())) : '';
        $data['countries'] = Country::getCountries(@Session::get('country_search'));
        return view('country.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('country.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $input       = $request->all();
        $input['id'] = GanticHelper::gen_uuid();
        Country::create($input);
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
        $data['country'] = Country::find($id);
        if ($data['country']) {
            return view('country.edit', $data);
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
    public function update(Request $request, $id)
    {
        $country = Country::find($id);
        $input   = $request->all();
        $country->fill($input);
        $country->save();
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
        $country = Country::find($id);
        $country->delete($id);
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

}
