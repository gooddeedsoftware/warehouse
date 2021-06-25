<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Models\LogistraDetails;
use Illuminate\Http\Request;
use Redirect;
use Session;

class LogistraDetailsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    protected $route     = 'main.logistraDetails.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.logistraDetails_createsuccess';
    protected $updatemsg = 'main.logistraDetails_updatesuccess';
    protected $deletemsg = 'main.logistraDetails_deletesuccess';

    public function index()
    {
        $data = @Session::get('logistraDetails_search') ? @Session::get('logistraDetails_search') : [];
        @\Request::input() ? Session::put('logistraDetails_search', array_merge($data, \Request::input())) : '';
        $data['logistraDetails'] = LogistraDetails::getRecs(@Session::get('logistraDetails_search'));
        return view('logistraDetails.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('logistraDetails.form');
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
        LogistraDetails::create($input);
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
        $data['logistraDetails'] = LogistraDetails::find($id);
        if ($data['logistraDetails']) {
            return view('logistraDetails.edit', $data);
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
        $logistraDetails = LogistraDetails::find($id);
        $input           = $request->all();
        $logistraDetails->fill($input);
        $logistraDetails->save();
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
        $logistraDetails = LogistraDetails::find($id);
        $logistraDetails->delete($id);
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

}
