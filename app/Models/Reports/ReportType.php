<?php

namespace App\Models\Reports;

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ReportType
 * @package App\Models\Report
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 *
 * @property string $name
 * @property string $code
 * @property string $description
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static Builder defaultReport()
 */
class ReportType extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'name' => ['required'],
            'code' => ['required'],
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

        ];
    }

    #endregion

    #region Eloquent Relationships

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    #endregion

    #region Scopes

    public function scopeDefaultReport($query) {
        return $query
            ->where('code', "default");
    }

    #endregion

    #region Custom Functions

    public static function createNew($name,$code,$description): ReportType {

        $reporttype = ReportType::create([
            'name' => $name,
            'code' => $code,
            'description' => $description,
        ]);

        $reporttype->save();

        return $reporttype;
    }

    #endregion
}
