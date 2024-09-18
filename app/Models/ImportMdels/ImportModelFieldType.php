<?php

namespace App\Models\ImportModels;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ImportModelFieldType
 *
 * @package App\Models\ImportModels
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 * @property string $name
 * @property string|IInnerImportModelFieldType $model_type
 * @property string $view_name
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldType whereViewName($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class ImportModelFieldType extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'name' => ['required'],
            'model_type' => ['required'],
            'view_name' => ['required'],
        ];
    }
    public static function createRules() {
        return array_merge(self::defaultRules(), [

        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [

        ]);
    }

    public static function messagesRules() {
        return [

        ];
    }

    #endregion

    #region Eloquent Relationships

    #endregion

    #region Custom Functions

    public static function createNew($name,$model_type,$view_name,$description = null): ImportModelFieldType {

        $importmodelfieldtype = ImportModelFieldType::create([
            'name' => $name,
            'model_type' => $model_type,
            'view_name' => $view_name,
            'description' => $description,
        ]);

        $importmodelfieldtype->save();

        return $importmodelfieldtype;
    }

    #endregion
}
