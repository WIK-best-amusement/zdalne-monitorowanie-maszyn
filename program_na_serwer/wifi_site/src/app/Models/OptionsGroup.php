<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OptionsGroup
 * 
 * @property int $id
 * @property string $name
 * @property int $order
 * 
 * @property Collection|Option[] $options_group
 *
 * @package App\Models
 */
class OptionsGroup extends Model
{
	protected $table = 'options_groups';
	public $timestamps = false;

	protected $casts = [
		'order' => 'int'
	];

	protected $fillable = [
		'name',
		'order'
	];

	public function options_group()
	{
		return $this->hasMany(Option::class, 'location_id');
	}
}
