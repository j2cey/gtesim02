<?php

namespace App\Http\Resources\HowTos;

use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Models\HowTos\HowTo;
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
 * @property integer $posi
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
 * @property HowToThread $howtothread
 * @property HowTo $howto
 * @property mixed $comments
 */
class HowToStepResource extends JsonResource
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
            'posi' => $this->posi,
            'description' => $this->description,

            'howtothread' => $this->howtothread,
            'howto' => $this->howto,

            'tags' => $this->tags,
            'comments' => $this->comments,

            'status' => $this->status,
            'modelclass' => HowToStep::class,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => $this->creator,
        ];
    }
}
