<?php

/*
------------------------------------------------------------------------------------------------
Created By   : Aravinth.A
Email:       : aravinth@avalia.no
Created Date : 06.07.2017
Purpose      : CCSheetCurrency model to store the currency details
------------------------------------------------------------------------------------------------
*/
namespace App\Models;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CCSheetCurrency extends Model
{
    protected $table = 'ccsheet_currency';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['ccsheet_id', 'curr_iso', 'exch_rate', 'deleted_at'];

    /**
    *	Store ccsheet_currency details
    *	@param ccsheet id int
    */
    public static function storeCCSheetCurrency($ccsheet_id) {
    	try {
	    	if ($ccsheet_id) {
	    		$currency_details = Currency::getRecentCurrencyDetails();
	    		if ($currency_details) {
	    			foreach ($currency_details as $key => $value) {
	    				$input['ccsheet_id'] = $ccsheet_id;
	    				$input['curr_iso'] = $value->curr_iso_name;
	    				$input['exch_rate'] = $value->exch_rate;
	    				CCSheetCurrency::create($input);
	    			}
	    		}
	    	}
    	} catch (Exception $e) {}
    }

}
