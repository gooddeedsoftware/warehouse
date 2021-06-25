<?php

namespace App\Http\Controllers;

use Redirect;
use Request;
use Response;
use App\Models\Export;

class GanticController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function construct_orderby($orderby)
    {
        $neworder = array();
        if (isset($orderby)) {
            foreach ($orderby as $k => $value) {
                if ($k != 'model') {
                    $value1 = 'desc';
                    if ($value == 'desc') {
                        $value1 = 'asc';
                    }
                    if ($k != "page") {
                        $neworder[$k] = $k . '=' . $value1;
                    }

                }
            }
        }
        return $neworder;

    }
    public function get_querystring($querystring, $sorder, $by, $text = false)
    {
        $data['sortorder' . $text] = $sorder;
        $data['sortby' . $text]    = $by;
        foreach ($querystring as $key => $value) {
            if ($key != 'page' && $key != 'model') {
                $data['sortorder' . $text] = $key;
                $data['sortby' . $text]    = $value;
            }
            $data['orderby'][$key] = $value;
        }
        return $data;
    }

    /**
     *   Export csv file
     *
     **/
    public function exportCSV()
    {
        $export_ids  = Request::get('export_ids');
        $model       = Request::get('model');
        $object_id   = (Request::get('object_id')) ? Request::get('object_id') : '';
        $report_file = Export::exportRecords($export_ids, $model, $object_id);
        $headers     = array('Content-Type' => 'text/html');
        $fileName    = $model . '.csv';
        if ($report_file) {
            return Response::download($report_file, $fileName, $headers);
        } else {
            return Redirect::back()->with('error', trans('main.export_fail'));
        }
    }
}
