<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSupplier extends Model
{
    use SoftDeletes;

    protected $table   = 'product_supplier';
    public $timestamps = true;

    protected $fillable = array('supplier',
        'product_id',
        'articlenumber',
        'articlename',
        'curr_iso_name',
        'supplier_price',
        'supplier_discount',
        'discount',
        'other',
        'addon',
        'realcost',
        'realcost_nok',
        'is_main');

}
