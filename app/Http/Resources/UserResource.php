<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Person\PhoneNum;
use App\Models\Employes\Employe;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $username
 * @property string $email
 * @property boolean $is_local
 * @property boolean $is_ldap
 *
 * @property Employe $employe
 * @property-read Collection|Role[] $roles
 * @property-read Collection|PhoneNum[] $phonesesimcreated
 *
 * @property Status $status
 * @property User $creator
 * @property User $updator
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserResource extends JsonResource
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

            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,

            'is_local' => $this->is_local,
            'is_ldap' => $this->is_ldap,

            'employe' => $this->employe,

            'roles' => $this->roles,
            'phonesesimcreated' => $this->phonesesimcreated,

            'modelclass' => User::class,
            'modeltype' => "users",

            'creator' => $this->creator,
            'updator' => $this->updator,

            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
