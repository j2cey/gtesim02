<?php

namespace App\Models\Employes;

use Eloquent;
use App\Models\User;
use App\Models\Status;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use App\Contracts\IsBaseModel;
use App\Models\Person\PhoneNum;
use OwenIt\Auditing\Models\Audit;
use App\Models\Person\EmailAddress;
use App\Traits\PhoneNum\HasPhoneNums;
use OwenIt\Auditing\Contracts\Auditable;
use App\Contracts\Persons\IHasPhoneNums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Persons\IHasEmailAddresses;
use App\Traits\EmailAddress\HasEmailAddresses;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Employe
 *
 * @package App\Models\Employes
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
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
 * @property-read Collection|Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read Departement|null $departement
 * @property-read Collection|Departement[] $departementsResponsable
 * @property-read int|null $departements_responsable_count
 * @property-read Collection|EmailAddress[] $emailaddresses
 * @property-read int|null $emailaddresses_count
 * @property-read FonctionEmploye|null $fonction
 * @property-read EmailAddress|null $latestEmailAddress
 * @property-read PhoneNum|null $latestPhonenum
 * @property-read EmailAddress|null $oldestEmailAddress
 * @property-read PhoneNum|null $oldestPhonenum
 * @property-read Collection|PhoneNum[] $phonenums
 * @property-read int|null $phonenums_count
 * @method static Builder|BaseModel default($exclude = [])
 * @method static Builder|Employe newModelQuery()
 * @method static Builder|Employe newQuery()
 * @method static Builder|Employe query()
 * @method static Builder|Employe whereAdresse($value)
 * @method static Builder|Employe whereCreatedAt($value)
 * @method static Builder|Employe whereCreatedBy($value)
 * @method static Builder|Employe whereDepartementId($value)
 * @method static Builder|Employe whereFonctionEmployeId($value)
 * @method static Builder|Employe whereId($value)
 * @method static Builder|Employe whereIsDefault($value)
 * @method static Builder|Employe whereMatricule($value)
 * @method static Builder|Employe whereNom($value)
 * @method static Builder|Employe whereNomComplet($value)
 * @method static Builder|Employe whereObjectguid($value)
 * @method static Builder|Employe wherePrenom($value)
 * @method static Builder|Employe whereStatusId($value)
 * @method static Builder|Employe whereTags($value)
 * @method static Builder|Employe whereThumbnailphoto($value)
 * @method static Builder|Employe whereUpdatedAt($value)
 * @method static Builder|Employe whereUpdatedBy($value)
 * @method static Builder|Employe whereUuid($value)
 *
 * @method static create(string[] $data)
 *
 * @mixin Eloquent
 * @property-read Status|null $status
 */
class Employe extends BaseModel implements Auditable, IsBaseModel, IHasPhoneNums, IHasEmailAddresses
{
    use HasFactory, HasEmailAddresses, HasPhoneNums, \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    protected $with = ['departement', 'fonction', 'departementsResponsable'];

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

    #region Validation Rules

    public static function defaultRules() {
        return [
            'nom' => ['required','string','min:3','max:255',],
            'fonction' => ['required',],
            'departement' => ['required',],
        ];
    }
    public static function createRules()  {
        return array_merge(self::defaultRules(), [
            'matricule' => ['required','unique:employes,matricule,NULL,id,deleted_at,NULL',],
        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [
            'matricule' => ['required','unique:employes,matricule,'.$model->id.',id,deleted_at,NULL',],
        ]);
    }
    public static function messagesRules() {
        return [
            'nom.required' => 'Le Nom est requis',
            'nom.string' => 'Le Nom doit etre une chaine de caractères',
            'nom.min' => 'Le Nom doit avoir au moins 3 caractères',
            'nom.max' => 'Le Nom ne doit pas avoir plus de 255 caractères',
            'fonction.required' => 'La Fonction est requise',
            'departement.required' => 'Le Département est requis',
            'matricule.required' => 'Le Matricule est requis',
            'matricule.unique' => 'Ce Matricule est déjà utilisé',
        ];
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

    /**
     * @param string $nom
     * @param string $prenom
     * @param string $matricule
     * @param FonctionEmploye $fonction
     * @param Departement $departement
     * @param string|null $adresse
     * @param string|null $userid
     * @return Employe
     */
    public static function createNew(string $nom, string $prenom, string $matricule, FonctionEmploye $fonction, Departement $departement, string $adresse = null, string $userid = null): Employe
    {
        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'matricule' => $matricule,
        ];
        if (! is_null($adresse)) $data['adresse'] = $adresse;

        $employe = Employe::create($data);

        $employe->fonction()->associate($fonction);
        $employe->departement()->associate($departement);

        $employe->save();

        if (! is_null($userid) ) {
            $employe->setUserByUuid($userid);
        }

        return $employe;
    }

    /**
     * Associate this Employee to a given User
     * @param string $userid
     * @return void
     */
    public function setUserByUuid(string $userid): void
    {
        $user = User::getByUuid($userid);
        $this->user()->associate($user)->save();
    }

    /**
     * @param string $nom
     * @param string $prenom
     * @param string $matricule
     * @param FonctionEmploye $fonction
     * @param Departement $departement
     *
     * @param string|null $adresse
     * @return $this
     */
    public function updateOne(string $nom, string $prenom, string $matricule, FonctionEmploye $fonction, Departement $departement, string $adresse = null): static
    {
        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'matricule' => $matricule,
        ];
        if (! is_null($adresse)) $data['adresse'] = $adresse;

        $this->update($data);

        $this->fonction()->associate($fonction);
        $this->departement()->associate($departement);

        $this->save();

        return $this;
    }

    public function getIntituleAttribute()
    {
        return $this->nom . " " . $this->prenom;
    }
}
