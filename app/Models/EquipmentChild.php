<?php
namespace App\Models;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;

class EquipmentChild extends Model {

	protected $table = 'equipments_child';
	public $timestamps = false;

	protected $fillable = array('equipment_id', 'child_equipment_id');
	
}