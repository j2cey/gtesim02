<?php

namespace App\Models\Esims;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use App\Contracts\IsBaseModel;
use App\Models\Person\PhoneNum;
use App\Models\Aris\ArisStatus;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\ModelPickers\ModelPicker;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Esim
 *
 * @package App\Models\Esims
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property string $imsi
 * @property string $iccid
 * @property string $ac
 * @property string $pin
 * @property string $puk
 * @property string $eki
 * @property string $pin2
 * @property string $puk2
 * @property string $adm1
 * @property string $opc
 * @property integer|null $statut_esim_id
 * @property integer|null $technologie_esim_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $attributed_at
 * @property int|null $status_id status reference
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Esims\EsimQrcode|null $qrcode
 * @property-read \App\Models\Esims\StatutEsim|null $statutesim
 * @property-read \App\Models\Esims\TechnologieEsim|null $technologieesim
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Esim newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Esim newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Esim query()
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereAdm1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereEki($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereIccid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereImsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereOpc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim wherePin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim wherePin2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim wherePuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim wherePuk2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereStatutEsimId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereTechnologieEsimId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Esim whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 *
 * @property EsimState|null $lateststate latest esim state
 */
class Esim extends BaseModel implements IsBaseModel, Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    protected $with = ['qrcode'];

    public const CONFIG_DIR = "esim_fichier_qrcode";

    #region Validation Rules

    public static function defaultRules() {
        return [
            'imsi' => ['required'],
            'iccid' => ['required'],
            'ac' => ['required'],
            'pin' => ['required'],
            'puk' => ['required'],
            'eki' => ['required'],
            'pin2' => ['required'],
            'puk2' => ['required'],
            'adm1' => ['required'],
            'opc' => ['required'],
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
            'imsi.required' => 'Le champs IMSI est requis',
            'iccid.required' => 'Le champs ICCID est requise',
            'ac.required' => 'Le champs AC est requis',
            'pin.required' => 'Le champs PIN est requis',
            'puk.required' => 'Le champs PUK est requis',
            'eki.required' => 'Le champs EKI est requis',
            'pin2.required' => 'Le champs PIN2 est requis',
            'puk2.required' => 'Le champs PUK2 est requis',
            'adm1.required' => 'Le champs ADM1 est requis',
            'opc.required' => 'Le champs OPC est requis',
        ];
    }

    #endregion

    #region Eloquent Relationships

    #endregion

    #region Accessors

    public function getQrcodeimageFullpathAttribute() {
        $separator = "/";
        if ( ! is_null($this->qrcode->qrcode_img) ) {
            return public_path('/') . config('app.' . self::CONFIG_DIR) . $separator . $this->qrcode->qrcode_img;
        }
        return null;
    }

    public function getQrcodeimageFolderpathAttribute() {
        $separator = "/";
        return public_path('/') . config('app.' . self::CONFIG_DIR) . $separator;
    }

    #endregion

    #region Eloquent Relationships

    public function statutesim() {
        return $this->belongsTo(StatutEsim::class, 'statut_esim_id');
    }

    public function technologieesim() {
        return $this->belongsTo(TechnologieEsim::class, 'technologie_esim_id');
    }

    public function qrcode() {
        return $this->hasOne(EsimQrcode::class, 'esim_id');
    }

    public function phonenum() {
        return $this->hasOne(PhoneNum::class, 'esim_id');
    }

    public function attributor() {
        return $this->belongsTo(User::class, 'attributed_by');
    }

    public function states() {
        return $this->hasMany(EsimState::class, 'esim_id');
    }

    public function lateststate(): HasOne
    {
        return $this->states()->one()->ofMany('id', 'max');
    }

    public function arisstatuses() {
        return $this->hasMany(ArisStatus::class, 'esim_id');
    }

    public function latestarisstatus(): HasOne
    {
        return $this->arisstatuses()->one()->ofMany('id', 'max');
    }

    #endregion

    #region Custom Functions

    public function setStatutEsim(StatutEsim $status) {
        if ($status->code === "nouveau") {
            $this->setStatutFree();
        } elseif ($status->code === "attribution") {
            $this->setStatutAttribution();
        } elseif ($status->code === "attribue") {
            $this->setStatutAttribue();
        }
    }

    public function setStatutAttribue() {
        $esim_attribue_statut = StatutEsim::where('code', "attribue")->first();
        $this->statutesim()->associate($esim_attribue_statut);

        // free model picker if any
        $esim_picked = ModelPicker::where('model_type', Esim::class)->where('model_id', $this->id)->first();
        if ($esim_picked) {
            $esim_picked->setFree();
        }

        $this->setAttributor();

        $this->save();
    }

    public function setStatutAttribution() {
        $esim_attribue_statut = StatutEsim::where('code', "attribution")->first();

        $this->statutesim()->associate($esim_attribue_statut);
        $this->save();
    }

    public function setStatutFree() {
        if ( ! $this->phonenum ) {
            $esim_nouveau_statut = StatutEsim::where('code', "nouveau")->first();
            $this->statutesim()->associate($esim_nouveau_statut);
            $this->save();
        }
    }

    public function setStatutSuspendue() {
        if ( ! $this->phonenum ) {
            $esim_nouveau_statut = StatutEsim::where('code', "suspendue")->first();
            $this->statutesim()->associate($esim_nouveau_statut);
            $this->save();
        }
    }

    public function revertPreLastPickupStatus(): void
    {
        $lateststate = $this->lateststate;
        if ($lateststate) {
            $previous_state = $lateststate->prevesimstate;
            if ($previous_state) {
                $this->statutesim()->associate($previous_state->statutesim);
                $this->save();
            }
        }
    }

    public static function getFirstFree($esim_id = -1): Esim {
        if ($esim_id === -1 || is_null($esim_id)) {
            $esim_nouveau_statut = StatutEsim::where('code', "nouveau")->first();

            //$esim = Esim::where('statut_esim_id', $esim_nouveau_statut->id)->first();
            $esim_picked = ModelPicker::pick(Esim::class, [['field'=>"statut_esim_id", 'value'=>$esim_nouveau_statut->id]]);
            $esim = Esim::find($esim_picked->model_id);

            $esim->setStatutAttribution();

            return $esim;
        } else {
            return Esim::where('id', $esim_id)->first();
        }
    }

    public static function pickupFirstFree(Esim $old_esim = null): Esim
    {
        // Revert old esim status if any
        $old_esim?->revertPreLastPickupStatus();

        return Esim::getFirstFree();
    }

    public static function createNew($imsi, $iccid, $ac, $pin, $puk, $eki = null, $pin2 = null, $puk2 = null, $adm1 = null, $opc = null, StatutEsim $statutesim = null, TechnologieEsim $technologieesim = null)
    {
        $default_statutesim = is_null($statutesim) ? StatutEsim::getDefault() : $statutesim;
        $default_technologieesim = is_null($technologieesim) ? TechnologieEsim::getDefault() : $technologieesim;

        $esim = Esim::create([
            'imsi' => $imsi,
            'iccid' => $iccid,
            'ac' => $ac,
            'pin' => $pin,
            'puk' => $puk,
            'eki' => $eki,
            'pin2' => $pin2,
            'puk2' => $puk2,
            'adm1' => $adm1,
            'opc' => $opc,
        ]);

        $esim->statutesim()->associate($default_statutesim);
        $esim->technologieesim()->associate($default_technologieesim);

        $esim->save();

        return $esim;
    }

    public function updateOne($imsi,$iccid,$ac,$pin,$puk,$eki = null,$pin2 = null,$puk2 = null, $adm1 = null, $opc = null, StatutEsim $statutesim = null, TechnologieEsim $technologieesim = null)
    {
        $this->imsi = $imsi;
        $this->iccid = $iccid;
        $this->ac = $ac;
        $this->pin = $pin;
        $this->puk = $puk;
        $this->eki = $eki;
        $this->pin2 = $pin2;
        $this->puk2 = $puk2;
        $this->adm1 = $adm1;
        $this->opc = $opc;

        if (! is_null($statutesim)) {
            $this->statutesim()->associate($statutesim);
        }
        if (! is_null($technologieesim)) {
            $this->statutesim()->associate($technologieesim);
        }

        $this->save();

       return $this;
    }

    public function saveQrcode()
    {
        if ($this->qrcode) {
            // TODO: update qrcode
        } else {
            EsimQrcode::createNew($this, $this->ac);
            $this->save();
        }
    }

    public function saveState() {
        $previous_state = $this->lateststate;
        if ($previous_state) {
            $previous_state->load(['esim','statutesim','user']);
            if ($this->statut_esim_id !== $previous_state->statut_esim_id) {
                EsimState::createNew($this, $previous_state);
            }
        } else {
            EsimState::createNew($this);
        }
    }

    public function setAttributor($user = null, $date = null) {
        if ( is_null($user) ) {
            $user = Auth::user();
        }

        if ( is_null($date) ) {
            $date = Carbon::now();
        }

        $this->attributor()->associate($user);
        $this->attributed_at = $date;

        return $this;
    }

    public static function getById(int $esim_id): ?Esim {
        return Esim::where('id', $esim_id)->first();
    }

    public static function getByIccid(string $iccid): ?Esim {
        return Esim::where('iccid', $iccid)->first();
    }

    #endregion
    public static function boot ()
    {
        parent::boot();

        self::saved(function($model){
            $model->saveState();
        });
    }
}
