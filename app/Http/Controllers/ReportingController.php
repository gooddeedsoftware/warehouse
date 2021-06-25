<?php

namespace App\Http\Controllers;

use App\Models\CCSheet;
use App\Models\CCSheetReporting;
use App\Models\Reporting;
use Request;

class ReportingController extends Controller
{

    /**
     * hourlogg/ccsheet Reporting
     */
    public function createReport()
    {
        return view('reporting/reporting', Reporting::getReportDatas());
    }

    /**
     * [getCCSheetDates description]
     * @param  boolean $warehouse_id [description]
     * @return [type]                [description]
     */
    public function getCCSheetDates($warehouse_id = false)
    {
        if ($warehouse_id) {
            $ccsheet = new CCSheet();
            $results = $ccsheet->getCCSheetDatesForWarehouse($warehouse_id);
            if ($results) {
                echo json_encode(array('status' => 'success', 'data' => $results));exit();
            } else {
                echo json_encode(array('status' => 'error', 'message' => trans('main.norecords')));exit();
            }
        }
    }

    /**
     * [downloadReport description]
     * @return [type] [description]
     */
    public function downloadReport()
    {
        try {
            $report_filter_type = Request::get('report_filter_type');
            $ccsheet_id         = Request::get('ccsheet_date');
            $warehouse          = Request::get('warehouse');
            $report_type        = Request::get('report_type');
            if ($report_filter_type == 'stock') {
                $data = Reporting::createInventoryReport($warehouse);
            } else if ($report_filter_type == 'ccsheet_report') {
                $data = CCSheetReporting::downloadCCSheetReporting($report_filter_type, $ccsheet_id, $report_type);
            }
            if (@$data) {
                return response()->download($data['file'], $data['filename'], $data['headers']);
            } else {
                return Redirect::route('main.reporting.createReport')->with('error', trans('main.norecords'));
            }
        } catch (Exception $e) {
            return Redirect::route('main.reporting.createReport')->with('error', trans('main.norecords'));
        }
    }
}
