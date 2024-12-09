<?php

namespace App\Http\Resources\Esims;

use JsonSerializable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Esims\EsimState;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EsimStateResource
 * @package App\Http\Resources\Esims
 *
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 *
 * @property integer|null $esim_id
 * @property integer|null $statut_esim_id
 * @property integer|null $user_id
 *
 * @property string|null $details
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */

class EsimStateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,

            'statutesim' => StatutEsimResource::make($this->statutesim),
            'statutesim_libelle' => $this->statutesim->libelle,

            'user' => UserResource::make($this->user),
            'user_name' => $this->user ? $this->user->name : "",

            'model_type' => EsimState::class,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'details' => $this->details,

            'show_url' => route('esimstates.show', $this->uuid),
            'edit_url' => route('esimstates.edit', $this->uuid),
            'destroy_url' => route('esimstates.destroy', $this->uuid),
        ];
    }
}
