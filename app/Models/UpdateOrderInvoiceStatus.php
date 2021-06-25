<?php

namespace App\Models;

use App\Models\HourLogging;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderMaterial;
use Illuminate\Database\Eloquent\Model;

class UpdateOrderInvoiceStatus extends Model
{

    /**
     * update order invoice status
     * @param  String $id
     * @return void
     */
    public static function updateOrderInvoiceStatus($id = false)
    {
        if (@$id) {
            $order_result = Order::where('id', '=', $id)->get();
        } else {
            $order_result = Order::all();
        }
        UpdateOrderInvoiceStatus::checkHourloggAndMaterialsForOrder($order_result);
    }

    /**
     * check Hourlogg And Materials For Order
     * @param  object $order_details
     * @return void
     */
    public static function checkHourloggAndMaterialsForOrder($order_details = false)
    {
        if (@$order_details) {
            try {

                foreach ($order_details as $key => $value) {
                    $hourlogging                   = HourLogging::where(['order_id' => $value->id, 'billable' => 1])->get();
                    $expense                       = HourLogging::where(['order_id' => $value->id, 'billable' => 3])->get();
                    $order_material                = OrderMaterial::where(['order_id' => $value->id])->get();
                    $invoice                       = Invoice::where(['order_id' => $value->id])->get();
                    $total_invoice_hours_materials = count($hourlogging) + count($order_material) + count($expense);

                    $sum_of_hourlogg  = $hourlogging->sum('invoiced');
                    $sum_of_materials = $order_material->sum('invoiced');
                    $sum_of_expense   = $expense->sum('invoiced');
                    $sum_of_invoice   = $sum_of_expense + $sum_of_hourlogg + $sum_of_materials;

                    if (count($invoice) && count($invoice) == count($order_material) && $total_invoice_hours_materials > 0) {
                        $invoice_status = "1";
                    } else if ($sum_of_invoice < $total_invoice_hours_materials && $total_invoice_hours_materials > 0 && $sum_of_invoice > 0) {
                        $invoice_status = "2";
                    } else {
                        $invoice_status = "3";
                    }
                    $is_deleted = 1;
                    if (count(@$hourlogging) < 1 && count(@$order_material) < 1 && count(@$invoice) < 1 && count(@$expense) < 1) {
                        $is_deleted = 0;
                    }
                    // update order invoice status
                    Order::where('id', '=', $value->id)->update(['order_invoice_status' => $invoice_status, 'is_delete' => $is_deleted]);
                }
            } catch (Exception $e) {

            }
        }
    }

}
