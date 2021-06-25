<?php

namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\CurrencyRequest;
use App\Models\Currency;
use App\Models\DropdownHelper;
use Illuminate\Http\Request;
use Input;
use Redirect;
use Session;

class CurrencyController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    protected $route     = 'main.currency.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.currency_createsuccess';
    protected $updatemsg = 'main.currency_updatesuccess';
    protected $deletemsg = 'main.currency_deletesuccess';

    public function index()
    {
        $data = @Session::get('currency_search') ? @Session::get('currency_search') : [];
        @\Request::input() ? Session::put('currency_search', array_merge($data, @\Request::input())) : '';
        $language              = Session::get('language') ? Session::get('language') : 'no';
        $data['currency_list'] = DropdownHelper::where('language', $language)->where('groupcode', '015')->orderBy('keycode', 'asc')->pluck('label', 'keycode')->toArray();
        $data['currencies']    = Currency::getCurrencies(@Session::get('currency_search'));
        return view('currency.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $language              = Session::get('language') ? Session::get('language') : 'no';
        $data['currency_list'] = DropdownHelper::where('language', $language)->where('groupcode', '015')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        return view('currency.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(CurrencyRequest $request)
    {
        $input               = $request->all();
        $input['id']         = GanticHelper::gen_uuid();
        $input['valid_from'] = GanticHelper::formatDate($input['valid_from'], 'Y-m-d');
        $input['exch_rate']  = str_replace(",", ".", $input['exch_rate']);
        $input['added_by']   = Session::get('currentUserID');
        Currency::create($input);
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
        $language                     = Session::get('language') ? Session::get('language') : 'no';
        $data['currency_list']        = DropdownHelper::where('language', $language)->where('groupcode', '015')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['currency']             = Currency::findOrFail($id);
        $data['currency']->valid_from = GanticHelper::formatDate($data['currency']->valid_from);
        $data['currency']->exch_rate  = str_replace(".", ",", $data['currency']->exch_rate);
        if ($data['currency']) {
            return view('currency.edit', $data);
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
    public function update(CurrencyRequest $request, $id)
    {
        $currency            = Currency::findOrFail($id);
        $input               = $request->all();
        $input['valid_from'] = GanticHelper::formatDate($input['valid_from'], 'Y-m-d');
        $input['exch_rate']  = str_replace(",", ".", $input['exch_rate']);
        $input['updated_by'] = Session::get('currentUserID');
        $currency->fill($input);
        $currency->save();
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
        $currency = Currency::findOrFail($id);
        $currency->delete($id);
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

    /*
     *   Get currency details
     */
    public function getCurrencyDetails(Request $request)
    {
        try {
            $data = $request->get('data');
            if ($data) {
                $currencyDetails = Currency::getCurrencyDetails($data);
                if ($currencyDetails) {
                    echo json_encode(array("status" => "success", "data" => $currencyDetails));
                } else {
                    echo json_encode(array("status" => "error", "data" => "Something went wrong"));
                }
            } else {
                echo json_encode(array("status" => "error", "data" => "Something went wrong"));
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }
    }
}
