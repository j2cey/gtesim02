<?php

namespace App\Http\Resources\HowTos;

use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\HowTos\HowToStep;
use App\Models\HowTos\HowToThread;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class HowToResource
 * @package App\Http\Resources\HowTos
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 *
 * @property string $title
 * @property string $code
 * @property string $image
 * @property string $description
 *
 * @property integer|null $created_by
 * @property integer|null $updated_by
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Status $status
 * @property User $creator
 * @property mixed $tags
 *
 * @property mixed $steps
 * @property HowToStep $firststep
 */
class HowToThreadResource extends JsonResource
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

            'title' => $this->title,
            'code' => $this->code,
            'image' => $this->image,
            'description' => $this->description,
            'steps' => HowToStepResource::collection($this->steps),
            'firststep' => $this->firststep,
            'tags' => $this->tags,

            'status' => $this->status,
            'modelclass' => HowToThread::class,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => $this->creator,
        ];
    }
}
