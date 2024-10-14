<?php

namespace App\Http\Resources\Persons;

use App\Models\User;
use App\Models\Status;
use App\Models\Esims\Esim;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Person\PhoneNum;
use App\Contracts\Persons\IHasPhoneNums;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PhoneNumResource
 * @package App\Http\Resources\Employes
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 *
 * @property string $phone_number
 * @property string $hasphonenum_type
 * @property integer $hasphonenum_id
 * @property integer $posi
 * @property integer|null $esim_id
 *
 * @property IHasPhoneNums $hasphonenum
 *
 * @property Esim $esim
 * @property Status $status
 *
 * @property User $creator
 * @property User $updator
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PhoneNumResource extends JsonResource
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

            'phone_number' => $this->phone_number,
            'posi' => $this->posi,
            'hasphonenum_type' => $this->hasphonenum_type,
            'hasphonenum_id' => $this->hasphonenum_id,

            'esim' => $this->esim,
            'status' => $this->status,
            'modelclass' => PhoneNum::class,
            'modeltype' => "phonenums",

            'hasphonenum' => $this->hasphonenum,

            'creator' => $this->creator,
            'updator' => $this->updator,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
