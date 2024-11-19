<?php

namespace App\Http\Resources\Esims;

use App\Models\User;
use App\Models\Status;
use App\Models\Esims\Esim;
use Illuminate\Support\Carbon;
use App\Models\Person\PhoneNum;
use App\Models\Esims\EsimState;
use App\Models\Esims\StatutEsim;
use App\Models\Esims\EsimQrcode;
use App\Models\Esims\TechnologieEsim;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EsimResource
 * @package App\Http\Resources\Esims
 *
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property string $imsi
 * @property string $iccid
 * @property string $ac
 * @property string $pin
 * @property string $puk
 * @property string $eki
 * @property string $pin2
 * @property string $puk2
 * @property string $adm1
 * @property string $opc
 *
 * @property StatutEsim|null $statutesim
 * @property TechnologieEsim|null $technologieesim
 * @property-read EsimQrcode|null $qrcode
 *
 * @property-read PhoneNum $phonenum
 *
 * @property User|null $creator
 * @property User|null $updator
 * @property User|null $attributor
 * @property mixed $states
 * @property EsimState $lateststate
 *
 * @property Status $status
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $attributed_at
 */
class EsimResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,

            'imsi' => $this->imsi,
            'iccid' => $this->iccid,
            'ac' => $this->ac,
            'pin' => $this->pin,
            'puk' => $this->puk,
            'eki' => $this->eki,
            'pin2' => $this->pin2,
            'puk2' => $this->puk2,
            'adm1' => $this->adm1,
            'opc' => $this->opc,

            'statutesim' => $this->statutesim,
            'technologieesim' => $this->technologieesim,
            'qrcode' => $this->qrcode,

            'attributor' => $this->attributor,
            'attributed_at' => $this->attributed_at,
            'phonenum' => $this->phonenum,

            'states' => $this->states,
            'lateststate' => $this->lateststate,

            'status' => $this->status,
            'modelclass' => Esim::class,
            'modeltype' => "esims",

            'creator' => $this->creator,
            'updator' => $this->updator,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
