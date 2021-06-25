<?php

/*
------------------------------------------------------------------------------------------------
Created By   : Aravinth.A
Email:       : aravinth@avalia.no
Created Date : 30.08.2017
Purpose      : Used to download the CCSheet reports as PDF or XLSX
------------------------------------------------------------------------------------------------
 */

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\CCSheet;
use App\Models\WarehouseOrder;
use Config;
use Illuminate\Database\Eloquent\Model;
use PHPExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CCSheetReporting extends Model
{

    /**
     *     Download ccsheet report split report by report type
     *  @param string $report_fileter_type
     *  @param string $department
     *  @param date $date
     *  @param string $report type
     *  @return array filename and headers
     */
    public static function downloadCCSheetReporting($report_filter_type, $id, $report_type)
    {
        try {
            switch ($report_type) {
                case 'pdf':
                    $file     = CCSheet::createReport($id, true);
                    $headers  = config::get("constants.HEADER_FOR_PDF");
                    $filename = "ccsheet_report.pdf";
                    break;
                case 'xlsx':
                    $ccsheet_reporting       = new CCSheetReporting();
                    $ccsheet                 = new CCSheet();
                    $data['ccsheet_details'] = $ccsheet->constructDataForReport($id);
                    $file                    = $ccsheet_reporting->createCCSheetXLSXFile($data);
                    $headers                 = config::get("constants.HEADER_FOR_XLSX");
                    $filename                = "CCSheet_Report.xlsx";
                    break;
            }
            return array('file' => $file, 'headers' => $headers, 'filename' => $filename);
        } catch (Exception $e) {
            echo $e;exit;
            return false;
        }
    }

    /**
     * createCCSheetXLSXFile
     * @param  array $data
     * @return file
     */
    public function createCCSheetXLSXFile($data)
    {
        try {
            $currency_details = Currency::getRecentCurrencyDetails();
            $currencies       = '';
            if ($currency_details) {
                foreach ($currency_details as $key => $value) {
                    if ($value->curr_iso_name != 'NOK') {
                        $currencies = $currencies . ' 1 ' . $value->curr_iso_name . ' = ' . number_format($value->exch_rate, 2, ',', ' ');
                    }
                }
            }
            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->getProperties()
                ->setCreator("Gantic")
                ->setLastModifiedBy("Gantic")
                ->setTitle("Report")
                ->setSubject("Report")
                ->setDescription("Report")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("CCSheet report");
            $ccsheet_reporting = new CCSheetReporting();
            $objPHPExcel       = $ccsheet_reporting->constructCCSheetHeader($data, $objPHPExcel, $currencies, $currency_details);
            $ccsheet_reporting->constructCCSheetData(@$data['ccsheet_details']['locations'], $objPHPExcel);
            $objPHPExcel->getActiveSheet()->setTitle('CCSheet_Report');
            $objPHPExcel->setActiveSheetIndex(0);
            if (@$data['ccsheet_details']['whs_order_id']) {
                $warehouse_order         = new WarehouseOrder();
                $warehouse_order_details = $warehouse_order->getWarehouseOrderAndProductDetails(@$data['ccsheet_details']['whs_order_id'], 2);
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(1);
                $objPHPExcel->getActiveSheet(1)->setTitle('Warehouse Adjustment Order');
                $ccsheet_reporting->constructWarehouseOrderHeader($warehouse_order_details, $objPHPExcel, $currencies, $currency_details);
                $ccsheet_reporting->constructWarehouseOrderdata($warehouse_order_details, $objPHPExcel);
            }
            $objWriter = new Xlsx($objPHPExcel);
            $file      = GanticHelper::createTempFile("xlsx");
            $objWriter->save($file);
            ob_end_clean();
            return $file;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * constructCCSheetHeader
     * @param  object $data
     * @param  object $objPHPExcel
     * @return object
     */
    public function constructCCSheetHeader($data, $objPHPExcel, $currencies = false, $currency_details)
    {
        try {
            if ($data) {
                $j = 4;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('E2', trans('main.ccsheet'));

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('C4', trans('main.warehouse'))
                    ->setCellValue('D4', $data['ccsheet_details']['warehouse'])
                    ->setCellValue('F4', trans('main.completed_by'))
                    ->setCellValue('G4', $data['ccsheet_details']['completed_by']);
                foreach ($currency_details as $key => $value) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $j, '1' . @$value->curr_iso_name)
                        ->setCellValue('B' . $j, number_format(@$value->exch_rate, 2, ',', ' '));
                    $j++;
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('C6', trans('main.completed_at'))
                    ->setCellValue('D6', $data['ccsheet_details']['completed_at'])
                    ->setCellValue('F6', trans('main.comments'))
                    ->setCellValue('G6', $data['ccsheet_details']['comments']);
            }
        } catch (Exception $e) {

        }
        return $objPHPExcel;
    }

    /**
     * constructCCSheetData
     * @param  object $data
     * @param  object $objPHPExcel
     * @return object
     */
    public function constructCCSheetData($data, $objPHPExcel)
    {
        $i = 11;
        try {
            if ($data) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A10', trans('main.product'))
                    ->setCellValue('B10', trans('main.location'))
                    ->setCellValue('C10', trans('main.unit'))
                    ->setCellValue('D10', trans('main.on_stock'))
                    ->setCellValue('E10', trans('main.counted'))
                    ->setCellValue('F10', trans('main.curr_iso'))
                    ->setCellValue('G10', trans('main.vendor_price'))
                    ->setCellValue('H10', trans('main.counted_at'))
                    ->setCellValue('I10', trans('main.counted_by'));

                foreach ($data as $data_key => $data_value) {
                    foreach ($data_value as $key => $value) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, @$value['product_number'] . ' ' . @$value['description'])
                            ->setCellValue('B' . $i, @$value['location']['name'])
                            ->setCellValue('C' . $i, @$value['unit'])
                            ->setCellValue('D' . $i, @$value['on_stock_qty'])
                            ->setCellValue('E' . $i, @$value['counted_qty'])
                            ->setCellValue('F' . $i, @$value['curr_iso'])
                            ->setCellValue('G' . $i, Number_format(@$value['vendor_price'], 2, ',', ''))
                            ->setCellValue('H' . $i, (@$value['counted_at'] ? date('d.m.Y', strtotime(@$value['counted_at'])) : ''))
                            ->setCellValue('I' . $i, @$value['counted_user']['first_name'] . ' ' . @$value['counted_user']['last_name']);
                        $i++;
                    }
                }
            }
        } catch (Exception $e) {

        }
        return $objPHPExcel;
    }

    /**
     * constructCCSheetHeader
     * @param  object $data
     * @param  object $objPHPExcel
     * @return object
     */
    public function constructWarehouseOrderHeader($data, $objPHPExcel, $currencies = false, $currency_details)
    {
        try {
            if ($data) {
                $j = 4;
                $objPHPExcel->setActiveSheetIndex(1)
                    ->setCellValue('D2', trans('main.order_type'))
                    ->setCellValue('E2', @$data['warehouse_details']->order_type);
                $objPHPExcel->setActiveSheetIndex(1)
                    ->setCellValue('C4', trans('main.order_number'))
                    ->setCellValue('D4', @$data['warehouse_details']->order_number)
                    ->setCellValue('E4', trans('main.source_whs'))
                    ->setCellValue('F4', @$data['warehouse_details']->warehouses->shortname);
                foreach ($currency_details as $key => $value) {
                    $objPHPExcel->setActiveSheetIndex(1)
                        ->setCellValue('A' . $j, '1' . @$value->curr_iso_name)
                        ->setCellValue('B' . $j, number_format(@$value->exch_rate, 2, ',', ' '));
                    $j++;
                }
                $objPHPExcel->setActiveSheetIndex(1)
                    ->setCellValue('C6', trans('main.order_date'))
                    ->setCellValue('D6', (@$data['warehouse_details']->order_date ? date('d.m.Y', strtotime(@$data['warehouse_details']->order_date)) : ''))
                    ->setCellValue('E6', trans('main.comments'))
                    ->setCellValue('F6', @$data['warehouse_details']->comments);
            }
        } catch (Exception $e) {

        }
        return $objPHPExcel;
    }

    /**
     * [constructWarehouseOrderdata description]
     * @param  [type] $data        [description]
     * @param  [type] $objPHPExcel [description]
     * @return [type]              [description]
     */
    public function constructWarehouseOrderdata($data, $objPHPExcel)
    {
        try {
            if ($data) {
                $objPHPExcel->setActiveSheetIndex(1)
                    ->setCellValue('A10', trans('main.product'))
                    ->setCellValue('B10', trans('main.qty'))
                    ->setCellValue('C10', trans('main.received_qty'))
                    ->setCellValue('D10', trans('main.received_location'));
                $k = 11;
                for ($i = 0; $i < count(@$data['product_details']); $i++) {
                    $value = $data['product_details'][$i];

                    $objPHPExcel->setActiveSheetIndex(1)
                        ->setCellValue('A' . $k, @$value['product'])
                        ->setCellValue('B' . $k, @$value['ordered_quantity']);
                    foreach ($value['location_details'] as $key => $location_value) {
                        foreach ($location_value as $loc_key => $loc_value) {
                            $objPHPExcel->setActiveSheetIndex(1)->setCellValue('C' . $k, $loc_value['receive_quantity'])
                                ->setCellValue('D' . $k, $loc_value['receive_location']);
                        }
                        $k++;
                    }

                }
            }
        } catch (Exception $e) {

        }
        return $objPHPExcel;
    }
}
