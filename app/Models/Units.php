<?php
/*
------------------------------------------------------------------------------------------------
Created By   : Aravinth.A
Email:       : aravinth@avalia.no
Created Date : 24.01.2018
Purpose      : Units model.
------------------------------------------------------------------------------------------------
*/

namespace App\Models;

use DB;
use Auth;
use Session;
use App\Helpers\MaskinstyringHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Units extends Model
{
    use SoftDeletes;
    protected $table = 'units';
    protected $fillable = ['name', 'deleted_at'];

    /**
     * Get unit name from entered text
     * @param  String $unit
     * @return String
     */
    public static function getUnits($unit = false) {
    	try {
    		if ($unit) {
    			$unit = strtolower($unit);
    			$unit_name = '';
    			switch ($unit) {
    				case 'meter':
    				case 'meters':
    				case 'mtr':
    					$unit_name = 'MTR';
    					break;
    				case 'kilogram':
    				case 'kilograms':
    				case 'kg':
    				case 'kgm':
    					$unit_name = 'KGM';
    					break;
    				case 'square metre':
    				case 'square metres':
    				case 'squaremetres':
    				case 'squaremetre':
    				case 'MTK':
    					$unit_name = 'MTK';
    					break;
    				case 'hour':
    				case 'hours':
    				case 'hur':
    				case 'time':
                    case 'timer':
    					$unit_name = 'HUR';
    					break;
    				case 'piece':
    				case 'pieces':
    				case 'pcs':
    				case 'c62':
                    case 'stk':
                    case 'kr': 
    					$unit_name = 'STK';
    					break;
					case 'litre':
    				case 'litres':
    				case 'ltr':
                    case 'liter':
    					$unit_name = 'LTR';
    					break;
    				default:
    					$unit_name = 'HUR';
    					break;
    			}
    			$units = Units::select('name')->where('name', '=', $unit_name)->first();
    			if (@$units) {
    				return $units->name;
    			}
    			return $unit_name;
    		}
    	} catch (Exception $e) {

    	}
    }
}
