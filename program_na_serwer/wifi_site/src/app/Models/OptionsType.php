<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OptionsType
 * 
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $min
 * @property int $max
 * @property string $values
 *
 * @package App\Models
 */
class OptionsType extends Model
{
	protected $table = 'options_types';
	public $timestamps = false;

	protected $casts = [
		'min' => 'int',
		'max' => 'int'
	];

	protected $fillable = [
		'name',
		'type',
		'min',
		'max',
		'values'
	];
}
