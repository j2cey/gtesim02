<?php

namespace App\Http\Resources\Auth;

use App\Models\LdapUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Employes\Departement;
use App\Models\Employes\FonctionEmploye;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LdapUserResource
 * @package App\Http\Resources\Auth
 *
 * @property integer $id
 *
 * @property string $guid
 * @property string $name
 * @property string $firstname
 * @property string $lastname
 * @property string $login
 * @property string $email
 * @property string $domain
 * @property string $telephone
 * @property string $distinguishedname
 *
 * @property Departement $departement
 * @property string $title
 * @property FonctionEmploye $fonction
 *
 * @property string $password
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class LdapUserResource extends JsonResource
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
            'guid' => $this->guid,

            'name' => $this->name,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'login' => $this->login,
            'email' => $this->email,

            'domain' => $this->domain,
            'telephone' => $this->telephone,
            'distinguishedname' => $this->distinguishedname,

            'departement' => $this->departement,
            'title' => $this->title,
            'fonction' => $this->fonction,

            'password' => $this->password,

            'modelclass' => LdapUser::class,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
