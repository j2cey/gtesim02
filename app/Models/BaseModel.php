<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use Illuminate\Support\Carbon;
use App\Traits\Base\BaseTrait;
use App\Traits\Base\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BaseModel
 * @package App\Models
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 *
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Status $status
 * @method static active()
 * @property boolean $isActive
 */
class BaseModel extends Model
{
    use BaseTrait, HasCreator, HasTags;

    public function getRouteKeyName() { return 'uuid'; }

    #region Eloquent Relationships

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updator() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    #endregion

    #region Accessors & Mutators

    public function getIsActiveAttribute() {
        return ($this->status->code === 'active');
    }
    #endregion

    #region Scopes

    public function scopeDefault($query, $exclude = []) {
        return $query
            ->where('is_default', true)->whereNotIn('id', $exclude);
    }

    public function scopeActive($query) {
        return $query
            ->where('status_id', Status::active()->first()->id);
    }

    public function activate() {
        $this->changeStatus(Status::active()->first());
    }
    public function deactivate() {
        $this->changeStatus(Status::inactive()->first());
    }

    public function changeStatus(Status|Model $status) {
        return $this->status()->associate($status)->save();
    }

    public function saveObject(bool $save) {
        if ($save) {
            $this->save();
        }
    }

    #endregion
}
