<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\Warehouse;
use config;
use Illuminate\Database\Eloquent\Model;
use PHPExcel_Style_Alignment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reporting extends Model
{
    /**
     * [getReportDatas description]
     * @return [type] [description]
     */
    public static function getReportDatas()
    {
        $data                          = array();
        $data['warehouses']            = Warehouse::orderby('shortname', 'asc')->pluck('shortname', 'id');
        $data['hourlogg_filter_types'] = array(
            'ccsheet_report' => trans('main.ccsheet'),
            'stock'          => trans('main.inventory'),
        );
        return $data;
    }

    /**
     * [createInventoryReport description]
     * @param  [type] $warehouse_id [description]
     * @return [type]               [description]
     */
    public static function createInventoryReport($warehouse_id)
    {

        $whs_inventory_details = WarehouseInventory::where('warehouse_id', $warehouse_id)->orderBy('location_id', 'asc')->with('product')->get();
        $item_details          = $whs_inventory_details->groupBy('product_id');
        $location_details      = $whs_inventory_details->groupBy('location_id');
        $total_inventory       = $whs_inventory_details->sum('qty');
        $warehouse_details     = Warehouse::whereId($warehouse_id)->first();

        $locations = Location::pluck('name', 'id');

        try {
            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->getProperties()
                ->setCreator("Gantic")
                ->setLastModifiedBy("Gantic")
                ->setTitle("Inventory Report")
                ->setSubject("Inventory Report")
                ->setDescription("Inventory Report")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Inventory Report");

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B1', __('main.total_inventory_value'))
                ->setCellValue('D1', __('main.locations'))
                ->setCellValue('G1', __('main.inventory_items'))
                ->setCellValue('A4', __('main.rod_nr'))
                ->setCellValue('B4', __('main.description'))
                ->setCellValue('C4', __('main.warehouse'))
                ->setCellValue('D4', __('main.location'))
                ->setCellValue('E4', __('main.list_price'))
                ->setCellValue('F4', __('main.vendor_price'))
                ->setCellValue('G4', __('main.avaliable'))
                ->setCellValue('H4', __('main.total'));
            $i = 5;

            $total_value     = 0;
            $inventory_items = 0;

            foreach ($whs_inventory_details as $key => $value) {
                $total           = @$value->qty && @$value->product->vendor_price ? @$value->qty * @$value->product->vendor_price : 0;
                $total_value     = $total_value + $total;
                $inventory_items = $inventory_items+@$value->qty;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, @$value->product->product_number)
                    ->setCellValue('B' . $i, @$value->product->description)
                    ->setCellValue('C' . $i, @$warehouse_details->shortname)
                    ->setCellValue('D' . $i, @$locations[$value->location_id])
                    ->setCellValue('E' . $i, 0)
                    ->setCellValue('F' . $i, number_format(@$value->product->vendor_price, "2", ",", ""))
                    ->setCellValue('G' . $i, number_format($value->qty, "2", ",", ""))
                    ->setCellValue('H' . $i, number_format($total, "2", ",", ""));
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $i . ':' . 'D' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('E' . $i . ':' . 'H' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $i++;
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B2', number_format($total_value, "2", ",", ""))
                ->setCellValue('D2', count($location_details))
                ->setCellValue('G2', number_format($inventory_items, "2", ",", ""));

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $objPHPExcel->getActiveSheet()->setTitle('Report');
            $objPHPExcel->setActiveSheetIndex(0);
            $writer = new Xlsx($objPHPExcel);
            $file   = GanticHelper::createTempFile("xlsx");
            $writer->save($file);
            ob_end_clean();
            $headers  = config::get("constants.HEADER_FOR_XLSX");
            $fileName = "Inventory.xlsx";
            return array('file' => $file, 'headers' => $headers, 'filename' => $fileName);
        } catch (\Exception $e) {
            echo $e;die;
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return null;
        }
    }
}
