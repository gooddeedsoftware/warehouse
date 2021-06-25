<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class OfferSettings extends Model
{
    protected $table     = 'offer_settings';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;
    use Sortable;

    protected $dates    = ['deleted_at'];
    protected $fillable = array('id', 'type', 'data', 'comments', 'added_by', 'updated_by');

}
