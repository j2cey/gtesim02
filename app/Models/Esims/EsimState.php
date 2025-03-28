<?php

namespace App\Models\Esims;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class EsimState
 * @package App\Models\Esims
 *
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 *
 * @property integer|null $esim_id
 * @property integer|null $statut_esim_id
 * @property integer|null $user_id
 *
 * @property string|null $details
 * @property integer $ord
 * @property integer|null $prev_esim_state_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property EsimState|null $prevesimstate
 * @property StatutEsim $statutesim
 * @method static EsimState create(array $array)
 */
class EsimState extends BaseModel implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    protected $with = ['user','statutesim','prevesimstate'];

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

    public function statutesim() {
        return $this->belongsTo(StatutEsim::class, 'statut_esim_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prevesimstate() {
        return $this->belongsTo(EsimState::class, 'prev_esim_state_id');
    }

    #endregion

    #region Custom Functions

    public static function createNew(Esim $esim, EsimState $prev_esim_state = null): EsimState
    {
        $statutesim_attribue = StatutEsim::attribue()->first();

        $statutesim = $esim->statutesim;
        $user = Auth::user();
        $details = "";

        if ($statutesim && $statutesim->id === $statutesim_attribue->id) {
            if ($esim->phonenum) {
                $details = $esim->phonenum?->numero;
                if (!$user) {
                    $user = $esim->phonenum?->creator;
                }
            }
        }

        $user_id = $user ? $user->id : null;

        $new_esim_state = EsimState::create([
            'esim_id' => $esim->id,
            'statut_esim_id' => $statutesim?->id,
            'user_id' => $user_id,
            'details' => $details,
        ]);

        if ( ! is_null($prev_esim_state) ) {
            $new_esim_state->prevesimstate()->associate($prev_esim_state);
        }

        return $new_esim_state;
    }

    #endregion
}
