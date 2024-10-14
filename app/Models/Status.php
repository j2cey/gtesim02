<?php

namespace App\Models;

use App\Traits\Base\Uuidable;
use Illuminate\Support\Carbon;
use App\Traits\Base\HasDefault;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Status
 * @package App\Models
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 *
 * @property string $code
 * @property string $name
 * @property string $style
 * @property string|null $description
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static Builder default()
 * @method static Builder active()
 * @method static Builder inactive()
 *
 * @constant inactive
 */
class Status extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable, Uuidable, HasDefault;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'code' => ['required'],
            'name' => ['required'],
            'style' => ['required'],
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
            'code.required' => 'The CODE is required',
            'name.required' => 'The NAME is required ',
            'style.email' => 'The STYLE is required',
        ];
    }

    #endregion

    #region Scopes

    public function scopeDefault($query, $exclude = []) {
        return $query
            ->where('is_default', true)->whereNotIn('id', $exclude);
    }

    public function scopeActive($query) {
        return $query
            ->where('code', 'active');
    }

    public function scopeInactive($query) {
        return $query
            ->where('code', 'inactive');
    }

    #endregion

    #region Custom Public Functions

    /**
     * Create a New Status to the System
     * @param string $code
     * @param string $name
     * @param string $style
     * @param string|null $description
     * @return Status
     */
    public static function createNew(string $code, string $name, string $style, string $description = null)
    {
        return Status::create([
            'code' => $code,
            'name' => $name,
            'style' => $style,
            'description' => $description,
        ]);
    }

    /**
     * Update this Status
     * @param string $code
     * @param string $name
     * @param string $style
     * @param string|null $description
     * @return $this
     */
    public function updateOne(string $code, string $name, string $style, string $description = null)
    {
        $this->code = $code;
        $this->name = $name;
        $this->style = $style;
        $this->description = $description;

        $this->save();

        return $this;
    }

    public static function getByCode(string $code): Status {
        return Status::where('code', $code)->first();
    }
    #endregion
}
