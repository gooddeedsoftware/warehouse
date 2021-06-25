<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Models\PrinterDetail;
use Illuminate\Http\Request;
use Redirect;
use Session;

class LogistraPrinterController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    protected $route     = 'main.printer_detail.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.printer_detail_createsuccess';
    protected $updatemsg = 'main.printer_detail_updatesuccess';
    protected $deletemsg = 'main.printer_detail_deletesuccess';

    public function index()
    {
        $data = @Session::get('printer_detail_search') ? @Session::get('printer_detail_search') : [];
        @\Request::input() ? Session::put('printer_detail_search', array_merge($data, \Request::input())) : '';
        $data['printer_detail'] = PrinterDetail::getRecs(@Session::get('printer_detail_search'));
        return view('printer_detail.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('printer_detail.form');
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
        PrinterDetail::create($input);
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
        $data['printer_detail'] = PrinterDetail::find($id);
        if ($data['printer_detail']) {
            return view('printer_detail.edit', $data);
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
        $printer_detail = PrinterDetail::find($id);
        $input          = $request->all();
        $printer_detail->fill($input);
        $printer_detail->save();
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
        $printer_detail = PrinterDetail::find($id);
        $printer_detail->delete($id);
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

}
