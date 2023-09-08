<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class OptionsGroupsRole
 * 
 * @property int $id
 * @property int $option_id
 * @property int $role_id
 * @property bool $can_see
 * 
 * @property LocationsRole $role
 * @property Option $option
 *
 * @package App\Models
 */
class OptionsGroupsRole extends Model
{
	protected $table = 'options_groups_roles';
	public $timestamps = false;

	protected $casts = [
		'option_id' => 'int',
		'role_id' => 'int',
		'can_see' => 'bool'
	];

	protected $fillable = [
		'option_id',
		'role_id',
		'can_see'
	];

	public function role()
	{
		return $this->belongsTo(LocationsRole::class, 'role_id');
	}

	public function option()
	{
		return $this->belongsTo(Option::class);
	}

    public static function getOnlySelectOptions() {
        $result = Option::select('options_types.values', 'options.id')
            ->join('options_types', 'type_id', '=', 'options_types.id')
            ->where('type', 'select')
            ->get();
        $arr = [];
        foreach ($result as $item) {
            $options = explode('|', $item->values);
            foreach ($options as $option) {
                $ex = explode('@', $option);
                $arr[$item->id][$ex[1]] = $ex[0];
            }
        }
        return $arr;
    }

    public static function removeAll() {
        self::truncate();
    }

    public static function insertNew($options) {
        self::removeAll();
        foreach ($options as $option => $value) {
            $arr = explode('_', $value);
            $optionId = $arr[0];
            $roleId = $arr[1];
            self::insert(
                ['option_id' => $optionId, 'role_id' => $roleId, 'can_see' => 1]
            );
        }
    }

    public static function getOptionsWithRolesAsArray() {
        $result = self::where('can_see', 1)->get();
        $arr = [];
        foreach ($result as $item) {
            $arr[$item->option_id.'_'.$item->role_id] = true;
        }
        return $arr;
    }
}
