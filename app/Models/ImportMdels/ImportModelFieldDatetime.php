<?php

namespace App\Models\ImportModels;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ImportModels\IsInnerImportModelFieldType;
use App\Contracts\ImportModels\IInnerImportModelFieldType;

/**
 * Class ImportModelFieldDatetime
 *
 * @package App\Models\ImportModels
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 * @property Carbon $importvalue
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\ImportModels\ImportModelField|null $importmodelfield
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime query()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereImportvalue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldDatetime whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class ImportModelFieldDatetime extends BaseModel implements IInnerImportModelFieldType
{
    use HasFactory, IsInnerImportModelFieldType;

    protected $guarded = [];
    protected $with = ['status'];

    #region Validation Rules

    public static function defaultRules() {
        return [
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

    public static function createNew(): ImportModelFieldDatetime {

        $importmodelfielddatetime = ImportModelFieldDatetime::create();

        return $importmodelfielddatetime;
    }

    public function getFormattedValue($importvalue) {
        $field = $this->getImportmodelfield();
        $importmodel = $this->getImportmodel();

        $this->importvalue = $importvalue;
        return $importvalue;
    }

    #endregion
}
