<?php

namespace App\Models\Employes;

use App\Models\User;
use App\Models\BaseModel;
use App\Traits\PhoneNum\HasPhoneNums;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\EmailAddress\HasEmailAddresses;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Employe
 *
 * @package App\Models\Employes
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 * @property string $nom
 * @property string|null $matricule
 * @property string|null $prenom
 * @property string|null $nom_complet
 * @property string|null $adresse
 * @property string|null $email_address_list
 * @property string|null $phone_number_list
 * @property string|null $objectguid
 * @property string|null $thumbnailphoto
 * @property integer|null $fonction_employe_id
 * @property integer|null $departement_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Employes\Departement|null $departement
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Employes\Departement[] $departementsResponsable
 * @property-read int|null $departements_responsable_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Employes\EmailAddress[] $emailaddresses
 * @property-read int|null $emailaddresses_count
 * @property-read \App\Models\Employes\FonctionEmploye|null $fonction
 * @property-read \App\Models\Employes\EmailAddress|null $latestEmailAddress
 * @property-read \App\Models\Employes\PhoneNum|null $latestPhonenum
 * @property-read \App\Models\Employes\EmailAddress|null $oldestEmailAddress
 * @property-read \App\Models\Employes\PhoneNum|null $oldestPhonenum
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Employes\PhoneNum[] $phonenums
 * @property-read int|null $phonenums_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Employe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employe query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereDepartementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereFonctionEmployeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereMatricule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereNomComplet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereObjectguid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereThumbnailphoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employe whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class Employe extends BaseModel implements Auditable
{
    use HasFactory, HasEmailAddresses, HasPhoneNums, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    /**
     * Get the employe's full concatenated name.
     * -- Must postfix the word 'Attribute' to the function name
     *
     * @return string
     */
    public function getNomCompletAttribute()
    {
        return "{$this->nom} {$this->prenom}";
    }

    #region Validation Tools

    public static function defaultRules() {
        return [
            'nom' => ['required','string','min:3','max:255',],
            'fonction_employe_id' => ['required','integer',],
        ];
    }
    public static function createRules()  {
        return array_merge(self::defaultRules(), [
            'nouveau_email' => ['required'],
            'nouveau_phone' => ['required'],
            'matricule' => ['required','unique:employes,matricule,NULL,id,deleted_at,NULL',],
        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [
            'matricule' => ['required','unique:employes,matricule,'.$model->id.',id,deleted_at,NULL',],
        ]);
    }
    public static function validationMessages() {
        return [];
    }

    #endregion

    #region Eloquent Relationships

    /**
     * Renvoie la Fonction de l employe.
     */
    public function fonction() {
        return $this->belongsTo(FonctionEmploye::class, 'fonction_employe_id');
    }

    /**
     * Renvoie l Assignation de l employe.
     */
    public function departement() {
        return $this->belongsTo(Departement::class);
    }

    /**
     * Retourne tous les Departements pour lesquelles cet employe est responsable.
     */
    public function departementsResponsable() {
        return $this->hasMany(Departement::class, 'employe_responsable_id');
    }

    /**
     * Get the user that owns the phone.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    #endregion
}
