<?php

namespace App\Models\Aris;

use App\Models\Esims\Esim;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ArisStatus
 *
 * @package App\Models\Aris
 * @property integer $id
 *
 * @property string|null $icc
 * @property string|null $status
 * @property Carbon|null $status_change_date
 * @property Carbon|null $requested_at
 * @property Carbon|null $responded_at
 * @property string|null $response_message
 *
 * @property int|null $esim_id
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static ArisStatus create(array $array)
 */
class ArisStatus extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_change_date' => 'datetime',
            'requested_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    protected $appends = [
        'formatted_status_change_date',
        'formatted_requested_at',
        'formatted_responded_at',
        'formatted_status',
    ];

    public function getFormattedStatusChangeDateAttribute()
    {
        return $this->status_change_date->format(config('Settings.date.format'));
    }

    public function getFormattedRequestedAtAttribute()
    {
        return $this->requested_at->format(config('Settings.date.format'));
    }

    public function getFormattedRespondedAtAttribute()
    {
        return $this->responded_at->format(config('Settings.date.format'));
    }

    public function getFormattedStatusAttribute()
    {
        if( strtoupper( $this->status ) === "U") {
            return "Utilisé";
        } else if ( strtoupper( $this->status ) === "A" ) {
            return "Libre";
        }
        return "OCCUPE-ARIS";
    }

    #region Validation Rules

    public static function defaultRules() {
        return [
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

    public function esim() {
        return $this->belongsTo(Esim::class, 'esim_id');
    }

    #endregion

    #region Custom Functions
    public static function createNew($iccid, $icc, $status, $status_change_date, $requested_at, $responded_at, $response_message): ArisStatus
    {
        $arisstatus = ArisStatus::create([
            'icc' => $icc,
            'status' => $status,
            'status_change_date' => $status_change_date,
            'requested_at' => $requested_at,
            'responded_at' => $responded_at,
            'response_message' => $response_message,
        ]);

        $esim = Esim::getByIccid($iccid);

        if ($esim) {
            $arisstatus->esim()->associate($esim)->save();
        }

        return $arisstatus;
    }
    #endregion
}
