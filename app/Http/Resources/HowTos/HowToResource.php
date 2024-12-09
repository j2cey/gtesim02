<?php

namespace App\Http\Resources\HowTos;

use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Models\HowTos\HowTo;
use Illuminate\Support\Carbon;
use App\Models\HowTos\HowToType;
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
 * @property string|null $view
 * @property string|null $htmlbody
 * @property string $description
 *
 * @property integer|null $created_by
 * @property integer|null $updated_by
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property HowToType $howtotype
 * @property Status $status
 * @property User $creator
 * @property mixed $tags
 */
class HowToResource extends JsonResource
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
            'view' => $this->view,
            'htmlbody' => $this->htmlbody,
            'description' => $this->description,
            'tags' => $this->tags,

            'howtotype' => $this->howtotype,

            'status' => $this->status,
            'modelclass' => HowTo::class,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => $this->creator,
        ];
    }
}
