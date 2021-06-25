<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;

class Export extends Model
{

    /**
     *    Export CSV file
     *    @param ids array
     *    @param model string
     *    @param object_id string
     *    @return filename as string
     **/
    public static function exportRecords($ids = false, $model = false, $object_id = false)
    {
        try {
            if ($ids && $model) {
                $data = Export::getDatasFromModel($model, $ids, $object_id);

                if ($data) {
                    $temp_memory = fopen('php://memory', 'w');
                    $i           = 1;
                    foreach ($data as $input_array) {
                        if ($i == 1) {
                            fputs($temp_memory, utf8_decode(implode(';', array_keys($input_array)) . "\r\n"));
                        }
                        fputs($temp_memory, utf8_decode(implode(';', array_values($input_array)) . "\r\n"));
                        $i++;
                    }
                    fseek($temp_memory, 0);
                    $file = GanticHelper::createTempFile("csv");
                    file_put_contents($file, $temp_memory);
                    return $file;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {

        }
    }

    /**
     *     Get Data from selected module
     *    @param model string
     *    @param ids array
     *    @return array
     **/
    public static function getDatasFromModel($model, $ids, $object_id = false)
    {
        $data = array();
        if ($model) {
            switch ($model) {
                case 'supplier':
                    $data = Supplier::supplierData($ids);
                    break;

                case 'product':
                    $data = Product::productData($ids);
                    break;

                case 'stock':
                    $data = WarehouseDetails::warehouseDetailsData($ids, $object_id);
                    break;
                default:
                    # code...
                    break;
            }
        }
        return $data;
    }
}
