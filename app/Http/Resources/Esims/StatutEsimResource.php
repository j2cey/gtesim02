<?php

namespace App\Http\Resources\Esims;

use App\Http\Resources\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StatutEsimResource extends JsonResource
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

            'libelle' => $this->libelle,
            'code' => $this->code,
            'description' => $this->description,

            'created_at' => $this->created_at,
            'status' => StatusResource::make($this->status),

            'edit_url' => route('statutesims.edit', $this->uuid),
            'destroy_url' => route('statutesims.destroy', $this->uuid),
        ];
    }
}
