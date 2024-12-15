<?php

namespace App\Http\Resources\Aris;

use App\Models\User;
use App\Models\Esims\Esim;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Aris\ArisStatus;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EmployeResource
 * @package App\Http\Resources\Employes
 *
 * @property integer $id
 *
 * @property string|null $icc
 * @property Carbon|null $status_change_date
 * @property Carbon|null $requested_at
 * @property Carbon|null $responded_at
 * @property string|null $response_message
 *
 * @property int|null $esim_id
 * @property Esim $esim
 * @property int|null $request_id
 *
 * @property string $formatted_status
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ArisStatusResource extends JsonResource
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

            'icc' => $this->icc,
            'status_change_date' => $this->status_change_date,
            'requested_at' => $this->requested_at,
            'responded_at' => $this->responded_at,
            'response_message' => $this->response_message,

            'esim' => $this->esim,

            'formatted_status' => $this->formatted_status,

            'modelclass' => ArisStatus::class,
            'modeltype' => "arisstatuses",

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
