<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Session;

class WhsHistory extends Model
{
    protected $table   = 'whs_history';
    public $timestamps = true;
    use Sortable;
    protected $fillable = array(
        'product_id',
        'whs_inventory_id',
    );
    protected $sortable = array('serial_number');


    public function history_details()
    {
        return $this->hasMany('App\Models\WhsHistoryDetails', 'whs_history_id', 'id')->orderby('created_at', 'desc');
    }


    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }


    /**
     * [getHistoryProdcuts description]
     * @param  boolean $conditions [description]
     * @return [type]              [description]
     */
    public static function getHistoryProdcuts($conditions = false)
    {

        $whs_history = WhsHistory::with('history_details', 'product', 'product.supplier');
        if (isset($conditions['whs_history_search']) && $conditions['whs_history_search'] != '') {
            $search = $conditions['whs_history_search'];
            $whs_history->orwhereHas('product', function ($query) use ($search) {
                $query->orwhere('product.product_number', 'LIKE', '%' . $search . '%');
                $query->orwhere('product.description', 'LIKE', '%' . $search . '%');
            });

        }
        $paginate_size       = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        $whs_history_details = $whs_history->orderBy('created_at', 'desc')->sortable(['created_at' => 'desc'])->paginate($paginate_size);
        return $whs_history_details;
    }

    /**
     * [descriptionSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function descriptionSortable($query, $direction)
    {
        $query->orderby('product.product_number', $direction);
    }

    /**
     * [productnumberSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function productnumberSortable($query, $direction)
    {
        $query->orderby('product.description', $direction);
    }

    /**
     * [insertHistoryData description]
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public static function insertHistoryData($product_id)
    {
        try {
            $whs_history_data               = array();
            $whs_history_data['product_id'] = $product_id;
            $whs_history_result             = WhsHistory::create($whs_history_data);
            return $whs_history_result->id;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            WhsHistory::whsHistoryLog($error_message);
            return null;
        }
    }

    /**
     * [whsHistoryLog description]
     * @param  boolean $log_message [description]
     * @return [type]               [description]
     */
    public static function whsHistoryLog($log_message = false)
    {
        try {
            $file_name = storage_path() . '/uploads/whsHistory.log';
            $fd        = fopen($file_name, "a");
            fwrite($fd, $log_message . "\n");
            fclose($fd);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * [createReturnOrderHistory description]
     * @param  [type] $materials [description]
     * @param  [type] $order_id  [description]
     * @return [type]            [description]
     */
    public static function createReturnOrderHistory($materials, $order_id)
    {
        try {
            foreach ($materials as $key => $value) {
                $whs_history_data = WhsHistory::select('id')->where('product_id', '=', $value->product_id)->first();
                if ($whs_history_data) {
                    $whs_history_id = $whs_history_data->id;
                } else {
                    $whs_history_id = WhsHistory::insertHistoryData($value->product_id, null, null, 0);
                }
                WhsHistoryDetails::insertHistoryDetailData($whs_history_id, 4, $order_id, null, null, $value->warehouse, $value->location, $value->return_qty, null);
            }
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            WhsHistory::whsHistoryLog($error_message);
            return null;
        }
    }
}
