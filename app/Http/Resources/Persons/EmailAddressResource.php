<?php

namespace App\Http\Resources\Persons;

use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Person\EmailAddress;
use App\Http\Resources\StatusResource;
use App\Contracts\Persons\IHasEmailAddresses;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EmailAddressResource
 * @package App\Http\Resources\Employes
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 *
 * @property string $email_address
 * @property string $hasemailaddress_type
 * @property integer $hasemailaddress_id
 * @property integer $posi
 *
 * @property IHasEmailAddresses $hasemailaddress
 *
 * @property Status $status
 * @property User $creator
 * @property User $updator
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class EmailAddressResource extends JsonResource
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

            'email_address' => $this->email_address,
            'posi' => $this->posi + 1,
            'hasemailaddress_type' => $this->hasemailaddress_type,
            'hasemailaddress_id' => $this->hasemailaddress_id,

            'status' => $this->status,
            'modelclass' => EmailAddress::class,
            'modeltype' => "emailaddresses",
            'hasemailaddress' => $this->hasemailaddress,

            'creator' => $this->creator,
            'updator' => $this->updator,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
