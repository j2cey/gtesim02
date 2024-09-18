<?php

namespace App\Models\Authorization;

use Illuminate\Support\Carbon;
use App\Traits\Base\HasCreator;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Role
 * @package App\Models\Authorization
 *
 * @property integer $id
 *
 * @property string $name
 * @property string $guard_name
 * @property string|null $description
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property Carbon $deleted_at
 */
class Role extends SpatieRole
{
    use HasCreator, SoftDeletes;

    #region Validation Tools

    public static function defaultRules() {
        return [
            'permissions' => ['required'],
        ];
    }
    public static function createRules()  {
        return array_merge(self::defaultRules(), [
            'name' => ['required',
                'unique:roles,name,NULL,id',
            ],
        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [
            'name' => ['required',
                'unique:roles,name,'.$model->id.',id',
            ]
        ]);
    }
    public static function validationMessages() {
        return [];
    }

    #endregion
}
