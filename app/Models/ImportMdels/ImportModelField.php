<?php

namespace App\Models\ImportModels;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Contracts\ImportModels\IInnerImportModelFieldType;

/**
 * Class ImportModelField
 *
 * @package App\Models\ImportModels
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 * @property string $title
 * @property string $mapped_field
 * @property string $description
 * @property string $innerfieldtype_type
 * @property integer $innerfieldtype_id
 * @property integer|null $import_model_id
 * @property integer|null $import_model_field_type_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\ImportModels\ImportModelFieldType|null $fieldtype
 * @property-read \App\Models\ImportModels\ImportModel|null $importmodel
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $innerfieldtype
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField query()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereImportModelFieldTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereImportModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereInnerfieldtypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereInnerfieldtypeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereMappedField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelField whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class ImportModelField extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules()
    {
        return [
            'title' => ['required'],
            'mapped_field' => ['required'],
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

    public function fieldtype() {
        return $this->belongsTo(ImportModelFieldType::class,"import_model_field_type_id");
    }

    /**
     * Undocumented function
     *
     * @return belongsTo|ImportModel
     */
    public function importmodel() {
        return $this->belongsTo(ImportModel::class,"import_model_id");
    }

    /**
     * @return MorphTo
     * Get the parent inner field type model.
     */
    public function innerfieldtype()
    {
        return $this->morphTo();
    }

    #endregion

    #region Custom Functions

    public static function createInnerFieldType(ImportModelFieldType $fieldtype) : IInnerImportModelFieldType {
        return $fieldtype->model_type::createNew();
    }

    private function syncInnerFieldType(ImportModelFieldType $fieldtype, IInnerImportModelFieldType $innerfieldtype) : IInnerImportModelFieldType {

        if ( $this->fieldtype->id !== $fieldtype->id ) {
            // remove the old innerfieldtype
            $this->removeInnerFieldType();

            // and we have to create a new one from new type
            $innerfieldtype = $this->createInnerFieldType($fieldtype);

            $innerfieldtype->attachUpperImportModelField($this);
            $this->fieldtype()->associate($fieldtype);

            $this->save();
        }

        return $innerfieldtype;
    }

    public function removeInnerFieldType()
    {
        $this->innerfieldtype->delete();
    }

    public static function createNew(ImportModel $importmodel, ImportModelFieldType $fieldtype, $title, $mapped_field, $description): ImportModelField {

        $innerfieldtype = self::createInnerFieldType($fieldtype);

        $importmodelfield = $innerfieldtype->importmodelfield()->create([
            'title' => $title,
            'mapped_field' => $mapped_field,
            'description' => $description,
        ]);

        $importmodelfield->importmodel()->associate($importmodel);
        $importmodelfield->fieldtype()->associate($fieldtype);

        $importmodelfield->save();

        return $importmodelfield;
    }

    public function updateOne(ImportModelFieldType $fieldtype, $title, $mapped_field, $description): ImportModelField {

        $this->syncInnerFieldType($fieldtype, $this->innerfieldtype);

        $this->update([
            'title' => $title,
            'mapped_field' => $mapped_field,
            'description' => $description,
        ]);

        $this->save();

        return $this;
    }

    // Analysis Rule broken

    // Analysis Rule followed

    #endregion

    public static function boot(){
        parent::boot();

        static::deleting(function ($model) {
            $model->removeInnerFieldType();
        });
    }
}
