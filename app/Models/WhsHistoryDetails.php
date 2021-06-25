<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class WhsHistoryDetails extends Model
{
    protected $table    = 'whs_history_details';
    public $timestamps  = true;
    protected $fillable = array(
        'whs_history_id',
        'order_type',
        'order_id',
        'from_warehouse',
        'from_location',
        'destination_warehouse',
        'destination_location',
        'received_qty',
        'user',
        'customer',
        'action_date',
    );

    /**
     * [WhsHistory description]
     */
    public function WhsHistory()
    {
        return $this->belongsTo('App\Models\WhsHistory', 'whs_history_id', 'id');
    }

    /**
     * [insertHistoryDetailData description]
     * @param  [type]  $whs_history_id        [description]
     * @param  [type]  $order_type            [description]
     * @param  [type]  $order_id              [description]
     * @param  [type]  $from_warehouse        [description]
     * @param  [type]  $from_location         [description]
     * @param  [type]  $destination_warehouse [description]
     * @param  [type]  $destination_location  [description]
     * @param  [type]  $qty                   [description]
     * @param  boolean $customer              [description]
     * @return [type]                         [description]
     */
    public static function insertHistoryDetailData($whs_history_id, $order_type, $order_id, $from_warehouse, $from_location, $destination_warehouse, $destination_location, $qty, $customer = false)
    {
        try {
            $whs_history_detail_data                          = array();
            $whs_history_detail_data['whs_history_id']        = $whs_history_id;
            $whs_history_detail_data['order_type']            = $order_type;
            $whs_history_detail_data['order_id']              = $order_id;
            $whs_history_detail_data['user']                  = Session::get('currentUserID');
            $whs_history_detail_data['from_warehouse']        = $from_warehouse;
            $whs_history_detail_data['from_location']         = @$from_location;
            $whs_history_detail_data['destination_warehouse'] = @$destination_warehouse;
            $whs_history_detail_data['destination_location']  = @$destination_location;
            $whs_history_detail_data['received_qty']          = $qty;
            $whs_history_detail_data['customer']              = $customer;
            $whs_history_detail_data['action_date']           = date('Y-m-d');
            $whs_history_detail_result                        = WhsHistoryDetails::create($whs_history_detail_data);
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            WhsHistory::whsHistoryLog($error_message);
            return null;
        }
    }
}
