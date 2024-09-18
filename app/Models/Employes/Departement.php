<?php

namespace App\Models\Employes;

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Departement
 *
 * @package App\Models\Employes
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 * @property string $intitule
 * @property string|null $chemin_complet
 * @property string|null $description
 * @property integer|null $type_departement_id
 * @property integer|null $departement_parent_id
 * @property integer|null $employe_responsable_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Departement[] $departementenfants
 * @property-read int|null $departementenfants_count
 * @property-read \App\Models\Employes\Employe|null $employeResponsable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Employes\Employe[] $employes
 * @property-read int|null $employes_count
 * @property-read Departement|null $parent
 * @property-read \App\Models\Employes\TypeDepartement|null $typedepartement
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Departement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereCheminComplet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereDepartementParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereEmployeResponsableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereIntitule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereTypeDepartementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class Departement extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Tools

    public static function defaultRules() {
        return [
            'intitule' => ['required','string','min:3','max:100',],
            'type_departement_id' => ['required',],
        ];
    }
    public static function createRules()  {
        return array_merge(self::defaultRules(), [
            'chemin_complet' => ['unique:departements,chemin_complet,NULL,id,deleted_at,NULL'],
        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [
            'chemin_complet' => ['unique:departements,chemin_complet,'.$model->id.',id,deleted_at,NULL',],
        ]);
    }
    public static function validationMessages() {
        return [
            'type_departement_id.required' => 'Prière de sélectionner un Type Département',
            'chemin_complet.unique' => 'Il existe déjà un département de même intitulé pour ce Parent',
        ];
    }

    #region Eloquent Relationships

    /**
     * Renvoie l'employe responsable du Departement.
     */
    public function typedepartement() {
        return $this->belongsTo(TypeDepartement::class, 'type_departement_id');
    }

    /**
     * Renvoie le Departement du Departement.
     */
    public function parent() {
        return $this->belongsTo(Departement::class, 'departement_parent_id');
    }

    /**
     * Renvoie les employés de ce Departement.
     */
    public function employes() {
        return $this->hasMany(Employe::class);
    }

    /**
     * Renvoie les departement departementenfants du Departement.
     */
    public function departementenfants() {
        return $this->hasMany(Departement::class, 'departement_parent_id');
    }

    /**
     * Renvoie l'employe responsable du Departement.
     */
    public function employeResponsable() {
        return $this->belongsTo(Employe::class, 'employe_responsable_id');
    }

    #endregion

    #region Custom Functions

    public static function getCheminComplet($intitule, $parent_id) {
        if (is_null($parent_id)) {
            return $intitule;
        } else {
            $parent = Departement::find($parent_id);

            return $parent->chemin_complet . ' > ' . $intitule;
        }
    }

    /**
     * Reconstruit le chemin complet du Depatement
     * @return void
     */
    private function rebuildCheminComplet() {
        $new_chemin_complet = Departement::getCheminComplet($this->intitule, $this->departement_parent_id);
        if ($this->chemin_complet == $new_chemin_complet) {
            // nothing to do
        } else {
            // we set the new one
            $this->chemin_complet = $new_chemin_complet;
            $this->save();
        }
    }

    public static function boot(){
        parent::boot();

        // Après chaque modification
        self::updated(function($model){
            // On reconstruit le chemin complet
            $model->rebuildCheminComplet();
            // On reconstruit les chemins complet de tous les enfants
            foreach ($model->departementenfants as $departementenfant) {
                $departementenfant->rebuildCheminComplet();
            }
        });
    }

    #endregion
}
