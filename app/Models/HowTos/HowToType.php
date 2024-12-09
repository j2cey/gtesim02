<?php

namespace App\Models\HowTos;

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class HowToType
 *
 * @package App\Models\HowTo
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string $title
 * @property string $description
 * @property integer|null $created_by
 * @property integer|null $updated_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType query()
 * @mixin \Eloquent
 * @property-read \App\Models\Status $status
 * @property int|null $status_id status reference
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowToType whereUuid($value)
 */

class HowToType extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'title' => ['required'],
        ];
    }
    public static function createRules() {
        return array_merge(self::defaultRules(), [

        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [

        ]);
    }

    public static function messagesRules() {
        return [
            'title.required' => 'The Title is required',
        ];
    }

    #endregion

    #region Eloquent Relationships



    #endregion

    #region Custom Functions

    public static function createNew($title, $description) : HowToType
    {
        return HowToType::create([
            'title' => $title,
            'description' => $description,
        ]);
    }

    public static function updateOrNew($title, $description): HowToType
    {
        $howtotype = HowToType::whereTitle($title)->first();

        if ($howtotype) {
            return $howtotype->updateOne($title, $description);
        } else {
            return HowToType::createNew($title, $description);
        }
    }

    public function updateOne($title, $description): static
    {
        $this->title = $title;
        if ( ! is_null($description) ) {
            $this->description = $description;
        }

        $this->save();

        return $this;
    }

    public static function getById(int $id): ?HowToType {
        return HowToType::whereId($id)->first();
    }

    #endregion;
}
