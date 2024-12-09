<?php

namespace App\Models\ImportModels;

use App\Models\BaseModel;
use App\Models\Files\File;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ImportModel
 *
 * @package App\Models\ImportModels
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 * @property string $title
 * @property string $code
 * @property string $array_values
 * @property string $file_fullname
 * @property string $description
 * @property string $targetmodel_type
 * @property string $filemodel_type
 * @property string $hasimportmodel_type
 * @property integer $hasimportmodel_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read File|null $file
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereArrayValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereFileFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereFilemodelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereHasimportmodelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereHasimportmodelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereTargetmodelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModel whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class ImportModel extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules()
    {
        return [
            'title' => ['required'],
        ];
    }

    public static function createRules()
    {
        return array_merge(self::defaultRules(), [

        ]);
    }

    public static function updateRules($model)
    {
        return array_merge(self::defaultRules(), [

        ]);
    }

    public static function messagesRules()
    {
        return [

        ];
    }

    #endregion

    #region Eloquent Relationships

    public function file() {
        return $this->belongsTo(File::class, 'file_id');
    }

    #endregion

    #region Custom Functions

    #endregion
}
