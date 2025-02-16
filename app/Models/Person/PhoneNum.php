<?php

namespace App\Models\Person;

use App\Models\User;
use App\Models\BaseModel;
use App\Traits\Esim\HasEsim;
use Illuminate\Support\Carbon;
use App\Contracts\IsBaseModel;
use Illuminate\Validation\Rule;
use App\Contracts\Esims\IHasEsim;
use App\Contracts\Persons\IHasPhoneNums;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PhoneNum
 *
 * @package App\Models\Employes
 *
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 * @property string $phone_number
 * @property string $hasphonenum_type
 * @property integer $hasphonenum_id
 * @property integer $posi
 * @property integer|null $esim_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property IHasPhoneNums $hasphonenum
 */

class PhoneNum extends BaseModel implements IsBaseModel, IHasEsim
{
    use HasEsim, SoftDeletes, HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    //protected $with = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'phone_number' => [
                'required',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:8',
            ],
        ];
    }

    public static function createRules($phone_number,$hasphonenum_type) {
        $default_rules = self::defaultRules();
        $default_rules['phone_number'][] = Rule::unique('phone_nums', 'phone_number')
            ->where(function ($query) use ($phone_number, $hasphonenum_type) {
                $query
                    ->where('phone_number', $phone_number)
                    ->where('hasphonenum_type', $hasphonenum_type);
        })->ignore($phone_number);

        return array_merge($default_rules, [

        ]);
    }

    public static function updateRules($model,$phone_number,$hasphonenum_type, IHasPhoneNums|null $hasphonenum) {
        $default_rules = self::defaultRules();
        $default_rules['phone_number'][] = Rule::unique('phone_nums', 'phone_number')
            ->where(function ($query) use($phone_number,$hasphonenum_type,$model) {
                $query
                    ->where('phone_number', $phone_number)
                    ->where('hasphonenum_type', $hasphonenum_type)
                ;
            })->ignore($model);

        // Add rules from hasphonenum, if any
        if ( ! is_null($hasphonenum) ) {
            $default_rules = $hasphonenum->customUpdateRules($default_rules);
        }

        return array_merge($default_rules, [
        ]);
    }

    public static function messagesRules(IHasPhoneNums|null $hasphonenum) {
        $messages = [
            'phone_number.required' => 'Numéro de téléphone requis',
            'phone_number.regex' => 'Numéro de téléphone non valide',
            'phone_number.min' => 'Numéro de téléphone doit avoir 8 digits minimum',
            'phone_number.unique' => 'Numéro déjà attribué',
        ];

        // Add messages rules from hasphonenum, if any
        if ( ! is_null($hasphonenum) ) {
            $messages = $hasphonenum->customUpdateRulesMessages($messages);
        }

        return $messages;
    }

    #endregion

    #region Eloquent Relationships

    /**
     * The Model which has this Attribute
     *
     * @return IHasPhoneNums|MorphTo
     */
    public function hasphonenum()
    {
        return $this->morphTo();
    }

    /*public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }*/

    #endregion

    public function updateOne(string $phone_number, int $posi): static
    {
        $this->phone_number = $phone_number;
        $this->posi = $posi;
        $this->save();

        $this->hasphonenum->switchPhonenumsPosi($this);
        $this->hasphonenum->setPhonenumList();

        return $this;
    }

    public function setCreator(User $creator) {
        $this->creator()->associate($creator);
        $this->save();

        return $creator;
    }

    public static function getById(int $id) : ?PhoneNum {
        return PhoneNum::where('id', $id)->first();
    }
}
