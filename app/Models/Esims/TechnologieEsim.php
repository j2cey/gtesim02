<?php

namespace App\Models\Esims;

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class TechnologieEsim
 *
 * @package App\Models\Esims
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string $libelle
 * @property string $code
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $status_id status reference
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim query()
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereLibelle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TechnologieEsim whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class TechnologieEsim extends BaseModel implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'libelle' => ['required'],
            'code' => ['required'],
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

    public static function createNew($libelle, $code, $description)
    {
        $technologieesim = TechnologieEsim::create([
            'libelle' => $libelle,
            'code' => $code,
            'description' => $description,
        ]);

        return $technologieesim;
    }

    #endregion
}
