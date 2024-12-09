<?php

namespace App\Http\Resources\Employes;

use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Person\PhoneNum;
use App\Models\Employes\Employe;
use OwenIt\Auditing\Models\Audit;
use App\Models\Person\EmailAddress;
use App\Models\Employes\Departement;
use App\Http\Resources\StatusResource;
use App\Models\Employes\FonctionEmploye;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EmployeResource
 * @package App\Http\Resources\Employes
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 *
 * @property string $nom
 * @property string|null $matricule
 * @property string|null $prenom
 * @property string|null $nom_complet
 * @property string|null $adresse
 * @property string|null $objectguid
 * @property string|null $thumbnailphoto
 *
 * @property-read FonctionEmploye|null $fonction
 * @property-read Departement|null $departement
 * @property-read Collection|Departement[] $departements_responsable
 *
 * @property string $email_address
 * @property string $email_address_list
 * @property string $phone_number
 * @property string $phone_number_list
 *
 * @property-read Collection|PhoneNum[] $phonenums
 * @property-read PhoneNum|null $latestPhonenum
 * @property-read PhoneNum|null $oldestPhonenum
 *
 * @property-read Collection|EmailAddress[] $emailaddresses
 * @property-read EmailAddress|null $latestEmailAddress
 * @property-read EmailAddress|null $oldestEmailAddress
 *
 * @property User $creator
 * @property User $updator
 * @property Status $status
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class EmployeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,

            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'matricule' => $this->matricule,
            'nom_complet' => $this->nom_complet,
            'adresse' => $this->adresse,
            'fonction' => $this->fonction,
            'departement' => $this->departement,
            'departements_responsable' => $this->departements_responsable,

            'email_address' => $this->email_address,
            'email_address_list' => $this->email_address_list,
            'phone_number' => $this->phone_number,
            'phone_number_list' => $this->phone_number_list,

            'phonenums' => $this->phonenums,
            'latestPhonenum' => $this->latestPhonenum,
            'oldestPhonenum' => $this->oldestPhonenum,

            'emailaddresses' => $this->emailaddresses,
            'latestEmailAddress' => $this->latestEmailAddress,
            'oldestEmailAddress' => $this->oldestEmailAddress,

            'status' => $this->status,

            'modelclass' => Employe::class,
            'modeltype' => "employes",

            'creator' => $this->creator,
            'updator' => $this->updator,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
