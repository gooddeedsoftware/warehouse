<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CcSheetScannedProduct extends Model
{
    protected $table    = 'ccsheet_scanned_products';
    public $timestamps  = true;
    protected $fillable = array(
        'ccsheet_id',
        'product',
        'warehouse',
        'location',
        'qty',
        'counted',
    );

    public function productDetail()
    {
        return $this->belongsTo('App\Models\Product', 'product', 'id');
    }

    public function locationDetail()
    {
        return $this->belongsTo('App\Models\Location', 'location', 'id');
    }
}
