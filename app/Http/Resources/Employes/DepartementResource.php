<?php

namespace App\Http\Resources\Employes;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class DepartementResource
 * @package App\Http\Resources\Employes
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 *
 * @property string $intitule
 * @property string|null $chemin_complet
 * @property string|null $description
 * @property integer|null $type_departement_id
 * @property integer|null $departement_parent_id
 * @property integer|null $employe_responsable_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class DepartementResource extends JsonResource
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
            'chemin_complet' => $this->chemin_complet,
            'description' => $this->description,

            'status' => StatusResource::make($this->status),
            'typedepartement' => TypeDepartementResource::make($this->typedepartement),
            'employes_count' => $this->employes()->count(),

            'created_at' => $this->created_at,

            'show_url' => route('departements.show', $this->uuid),
            'edit_url' => route('departements.edit', $this->uuid),
            'destroy_url' => route('departements.destroy', $this->uuid),
        ];
    }
}
