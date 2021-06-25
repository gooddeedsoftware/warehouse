<?php
/*
------------------------------------------------------------------------------------------------
Created By   : S David Antony
Email:       : david@processdrive.com
Created Date : 20.3.2018
Purpose      : Offer Product Model
------------------------------------------------------------------------------------------------
*/
namespace App\Models;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\MaskinstyringHelper;

class OfferProduct extends Model
{
    protected $table = 'offer_product';
	public $timestamps = true;
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	protected $fillable = array(
                    'offer_id',
					'product_id',
					'product_text',
					'qty',
					'unit',
					"price",
					"discount",
					'sum_ex_vat',
					'vat',
					'created_by',
					'updated_by',
					'sn_required',
					'deleted_at',
                  );


	/**
	 * [offers description]
	 * @return [type] [description]
	 */
    public function offers() {
        return $this->belongsTo('App\Models\Offer', 'id', 'offer_id');
    }

	/**
	 * [storeOfferProductDetails description]
	 * @param  [type]  $data      [description]
	 * @param  boolean $row_count [description]
	 * @return [type]             [description]
	 */
	public static function storeOfferProductDetails($offer_id, $data, $row_count = false) {
		try {
			$offer_product = array();
			for ($i = 1; $i <= $row_count; $i++) {
				$record['offer_id'] = $offer_id;
				if (@$data['product_'.$i]) {
					$record['product_id'] = @$data['product_'.$i];
					$record['product_text'] = @$data['product_text_'.$i];
					$record['qty'] =  isset($data['qty_'.$i]) ? str_replace(",", ".", $data['qty_'.$i]) : "";
					$record['unit'] = @$data['unit_'.$i];
					$record["price"] = isset($data['price_'.$i]) ? str_replace(",", ".", $data['price_'.$i]) : "";
					$record["discount"] = isset($data['discount_'.$i]) ? str_replace(",", ".", $data['discount_'.$i]) : "";
					$record['sum_ex_vat'] = isset($data['sum_ex_vat_'.$i]) ? str_replace(",", ".", $data['sum_ex_vat_'.$i]) : "";
					$record['vat'] = isset($data['vat_'.$i]) ? str_replace(",", ".", $data['vat_'.$i]) : "";
					$record['created_by'] = Auth::User()->id;
					$record['sn_required'] = @$data['sn_required_'.$i];
					OfferProduct::create($record);
					$record['created_by_details'] = User::findOrFail(Auth::User()->id)->toArray();
					$record['product_full_details'] = Product::findOrFail($record['product_id'])->toArray();
					$offer_product[] = $record;
				}
			}
			return $offer_product;
		} catch (\Exception $e) {
			$error_message = 'Date: '. date("Y-m-d H:i:s") .' [Line '. $e->getLine() . ' - '. $e->getMessage(). ' - '.$e->getFile(). ']';
            Offer::offerLog($error_message);
		}
	}
}
