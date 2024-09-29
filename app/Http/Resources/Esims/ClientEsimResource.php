<?php

namespace App\Http\Resources\Esims;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Esims\ClientEsim;
use App\Http\Resources\UserResource;
use App\Http\Resources\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Employes\PhoneNumResource;
use App\Http\Resources\Employes\EmailAddressResource;

/**
 * Class ClientEsimResource
 * @package App\Http\Resources\Esims
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 *
 * @property string $nom_raison_sociale
 * @property string $prenom
 * @property string $email
 * @property string $phonenum
 * @property string $pin
 * @property string $puk
 *
 * @property integer|null $esim_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ClientEsimResource extends JsonResource
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

            'nom_raison_sociale' => $this->nom_raison_sociale,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'phonenum' => $this->phonenum,
            'pin' => $this->pin,
            'puk' => $this->puk,

            'latestPhonenum' => PhoneNumResource::make($this->latestPhonenum),
            'oldestPhonenum' => PhoneNumResource::make($this->oldestPhonenum),
            'phonenums' => PhoneNumResource::collection($this->phonenums),

            'latestEmailAddress' => EmailAddressResource::make($this->latestEmailAddress),
            'oldestEmailAddress' => EmailAddressResource::make($this->oldestEmailAddress),
            'emailaddresses' => EmailAddressResource::collection($this->emailaddresses),

            'creator' => UserResource::make($this->creator),
            'esim_id' => $this->esim_id,
            'model_type' => ClientEsim::class,

            'status' => StatusResource::make($this->status),
            'created_at' => $this->created_at,

            'show_url' => route('clientesims.show', $this->uuid),
            'edit_url' => route('clientesims.edit', $this->uuid),
            'destroy_url' => route('clientesims.destroy', $this->uuid),
        ];
    }
}
