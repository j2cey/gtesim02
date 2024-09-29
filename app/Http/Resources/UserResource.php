<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Employes\EmployeResource;
use App\Http\Resources\Employes\PhoneNumResource;

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

            'employe' => EmployeResource::make($this->employe),

            'status' => StatusResource::make($this->status),
            'created_at' => $this->created_at,

            'roles' => $this->roles,
            'phonesesimcreated' => PhoneNumResource::collection($this->phonesesimcreated),

            'model_type' => User::class,

            'edit_url' => route('users.edit', $this->uuid),
            'destroy_url' => route('users.destroy', $this->uuid),
        ];
    }
}
