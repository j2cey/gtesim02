<?php

namespace App\Http\Resources\Employes;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TypeDepartementResource
 * @package App\Http\Resources\Employes
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 *
 * @property string $intitule
 * @property string|null $description
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TypeDepartementResource extends JsonResource
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

            'intitule' => $this->intitule,
            'description' => $this->description,

            'status' => StatusResource::make($this->status),

            'created_at' => $this->created_at,

            'show_url' => route('typedepartements.show', $this->uuid),
            'edit_url' => route('typedepartements.edit', $this->uuid),
            'destroy_url' => route('typedepartements.destroy', $this->uuid),
        ];
    }
}
