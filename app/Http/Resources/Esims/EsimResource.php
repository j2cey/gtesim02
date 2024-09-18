<?php

namespace App\Http\Resources\Esims;

use App\Models\Esims\Esim;
use App\Http\Resources\UserResource;
use App\Http\Resources\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'ac' => $this->ac,
            'iccid' => $this->iccid,
            'pin' => $this->pin,
            'puk' => $this->puk,

            'status' => StatusResource::make($this->status),
            'statutesim' => StatutEsimResource::make($this->statutesim),

            'phonenum' => $this->phonenum,
            'technologieesim' => $this->technologieesim,
            'model_type' => Esim::class,

            'attributor' => UserResource::make($this->attributor),
            'states' => EsimStateResource::collection($this->states),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'show_url' => route('esims.show', $this->uuid),
            'edit_url' => route('esims.edit', $this->uuid),
            'destroy_url' => route('esims.destroy', $this->uuid),
        ];
    }
}
