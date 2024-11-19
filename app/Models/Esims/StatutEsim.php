<?php

namespace App\Models\Esims;

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class StatutEsim
 *
 * @package App\Models\Esims
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property string $libelle
 * @property string $code
 * @property string $description
 * @property string $style
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $status_id status reference
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim query()
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereLibelle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatutEsim whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 *
 * @method static StatutEsim whereCode($code)
 */

class StatutEsim extends BaseModel implements Auditable
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

    #region Scopes

    public function scopeNouveau($query) {
        return $query
            ->where('code', "nouveau");
    }

    public function scopeAttribue($query) {
        return $query
            ->where('code', 'attribue');
    }

    public function scopeAttribution($query) {
        return $query
            ->where('code', 'attribution');
    }

    #endregion

    #region Custom Functions

    public static function createNew(string $libelle, string $code, string $description = null, string $style = null): StatutEsim
    {
        $data = [
            'libelle' => $libelle,
            'code' => $code,
        ];
        if ( ! is_null($description) ) $data['description'] = $description;
        if ( ! is_null($style) ) $data['style'] = $style;

        return StatutEsim::create($data);
    }

    public function updateOne(string $libelle, string $code, string $description = null, string $style = null): static
    {
        $this->libelle = $libelle;
        $this->code = $code;
        if ( ! is_null($description) ) {
            $this->description = $description;
        }
        if ( ! is_null($style) ) {
            $this->style = $style;
        }

        $this->save();

        return $this;
    }

    public static function updateOrNew(string $libelle, string $code, string $description = null, string $style = null): StatutEsim
    {
        $statutesim = StatutEsim::whereCode($code)->first();

        if ($statutesim) {
            return $statutesim->updateOne($libelle, $code, $description, $style);
        } else {
            return StatutEsim::createNew($libelle, $code, $description, $style);
        }
    }

    #endregion
}
