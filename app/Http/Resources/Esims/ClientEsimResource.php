<?php

namespace App\Http\Resources\Esims;

use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Person\PhoneNum;
use App\Models\Esims\ClientEsim;
use App\Models\Person\EmailAddress;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

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
 * @property string $email_address
 * @property string $email_address_list
 * @property string $phone_number
 * @property string $phone_number_list
 * @property string $pin
 * @property string $puk
 *
 * @property-read Collection|PhoneNum[] $phonenums
 * @property-read PhoneNum|null $latestPhonenum
 * @property-read PhoneNum|null $oldestPhonenum
 *
 * @property-read Collection|EmailAddress[] $emailaddresses
 * @property-read EmailAddress|null $latestEmailAddress
 * @property-read EmailAddress|null $oldestEmailAddress
 *
 * @property Status $status
 * @property User $creator
 * @property User $updator
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
            'pin' => $this->pin,
            'puk' => $this->puk,

            'phonenums' => $this->phonenums,
            'phone_number' => $this->phone_number,
            'phone_number_list' => $this->phone_number_list,
            'latestPhonenum' => $this->latestPhonenum,
            'oldestPhonenum' => $this->oldestPhonenum,

            'emailaddresses' => $this->emailaddresses,
            'email_address' => $this->email_address,
            'email_address_list' => $this->email_address_list,
            'latestEmailAddress' => $this->latestEmailAddress,
            'oldestEmailAddress' => $this->oldestEmailAddress,

            'modelclass' => ClientEsim::class,
            'modeltype' => "clientesims",

            'creator' => $this->creator,
            'updator' => $this->updator,

            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
