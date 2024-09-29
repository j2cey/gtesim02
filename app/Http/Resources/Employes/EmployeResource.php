<?php

namespace App\Http\Resources\Employes;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EmployeResource
 * @package App\Http\Resources\Employes
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 *
 * @property string $nom
 * @property string|null $matricule
 * @property string|null $prenom
 * @property string|null $nom_complet
 * @property string|null $adresse
 * @property string|null $objectguid
 * @property string|null $thumbnailphoto
 * @property integer|null $fonction_employe_id
 * @property integer|null $departement_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class EmployeResource extends JsonResource
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

            'nom' => $this->nom,
            'matricule' => $this->matricule,
            'nom_complet' => $this->nom_complet,
            'adresse' => $this->adresse,

            'status' => StatusResource::make($this->status),

            'fonction' => FonctionEmployeResource::make($this->fonction),
            'departement' => DepartementResource::make($this->departement),

            'created_at' => $this->created_at,

            'show_url' => route('employes.show', $this->uuid),
            'edit_url' => route('employes.edit', $this->uuid),
            'destroy_url' => route('employes.destroy', $this->uuid),
        ];
    }
}
