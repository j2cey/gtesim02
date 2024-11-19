<?php

namespace App\Models\Employes;

use App\Models\BaseModel;
use Illuminate\Support\Str;
use App\Contracts\IsBaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class FonctionEmploye
 *
 * @package App\Models\Employes
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 * @property string $intitule
 * @property string $slug
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye query()
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereIntitule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FonctionEmploye whereUuid($value)
 * @method static FonctionEmploye create(array $fonctionemploye_values)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class FonctionEmploye extends BaseModel implements IsBaseModel, Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region validation tools

    public static function defaultRules() {
        return [];
    }
    public static function createRules() {
        return array_merge(self::defaultRules(), [
            'intitule' => ['required','string','min:3','max:100',
                //'unique:fonction_employes,intitule,NULL,id,deleted_at,NULL',
                'unique:fonction_employes,intitule,NULL,id',
            ],
        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [
            'intitule' => ['required','string','min:3','max:100',
                //'unique:fonction_employes,intitule,'.$model->id.',id,deleted_at,NULL',
                'unique:fonction_employes,intitule,'.$model->id.',id',
            ],
        ]);
    }
    public static function validationMessages() {
        return [];
    }

    #endregion

    #region BOOT

    public static function boot(){
        parent::boot();

        // when creation
        self::saving(function($model){
            // set slug
            $model->slug = Str::slug($model->intitule);
        });
    }

    #endregion
}
