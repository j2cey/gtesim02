<?php

namespace App\Http\Resources\Employes;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EmailAddressResource
 * @package App\Http\Resources\Employes
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 *
 * @property string $email
 * @property string $hasemailaddress_type
 * @property integer $hasemailaddress_id
 * @property integer $posi
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

            'email' => $this->email,
            'posi' => $this->posi,
            'hasemailaddress_type' => $this->hasemailaddress_type,
            'hasemailaddress_id' => $this->hasemailaddress_id,

            'status' => StatusResource::make($this->status),

            'created_at' => $this->created_at,

            'show_url' => route('emailaddresses.show', $this->uuid),
            'edit_url' => route('emailaddresses.edit', $this->uuid),
            'destroy_url' => route('emailaddresses.destroy', $this->uuid),
        ];
    }
}
