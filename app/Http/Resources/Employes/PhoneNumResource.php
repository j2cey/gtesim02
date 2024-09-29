<?php

namespace App\Http\Resources\Employes;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\StatusResource;
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
 * @property string $phonenum
 * @property string $hasphonenum_type
 * @property integer $hasphonenum_id
 * @property integer $posi
 * @property integer|null $esim_id
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

            'phonenum' => $this->phonenum,
            'posi' => $this->posi,
            'hasphonenum_type' => $this->hasphonenum_type,
            'hasphonenum_id' => $this->hasphonenum_id,

            'esim' => $this->esim,// EsimResource::make($this->esim),
            'status' => StatusResource::make($this->status),

            'created_at' => $this->created_at,

            'show_url' => route('phonenums.show', $this->uuid),
            'edit_url' => route('phonenums.edit', $this->uuid),
            'destroy_url' => route('phonenums.destroy', $this->uuid),
        ];
    }
}
