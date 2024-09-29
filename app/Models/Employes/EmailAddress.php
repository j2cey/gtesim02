<?php

namespace App\Models\Employes;

use App\Models\Status;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class EmailAddress
 *
 * @package App\Models\Employes
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 * @property string $email_address
 * @property string $hasemailaddress_type
 * @property integer $hasemailaddress_id
 * @property integer $posi
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 *
 * @property-read Status|null $status
 */
class EmailAddress extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'email_address' => [
                'required',
                'email',
            ],
        ];
    }

    public static function createRules($email,$hasemailaddress_type) {
        return array_merge(self::defaultRules($email), [
            /*'email' => Rule::unique('email_addresses', 'email')
                ->where(function ($query) use($email,$hasemailaddress_type) {
                    $query
                        ->where('email', $email)
                        ->where('hasemailaddress_type', $hasemailaddress_type);
                })->ignore($email),*/
        ]);
    }

    public static function updateRules($model,$email,$hasemailaddress_type) {
        return array_merge(self::defaultRules(), [
            /*'email' => Rule::unique('phone_nums', 'email')
                ->where(function ($query) use($email,$hasemailaddress_type,$model) {
                    $query
                        ->where('email', $email)
                        ->where('hasemailaddress_type', $hasemailaddress_type)
                    ;
                })->ignore($model),*/
        ]);
    }

    public static function messagesRules() {
        return [
            'email_address.required' => 'Adresse Mail requise',
            'email_address.email' => 'Adresse Mail non valide',
            'email_address.unique' => 'Adresse Mail déjà attribué',
        ];
    }

    #endregion

    #region Eloquent Relationships

    /**
     * The Model which has this Attribute
     *
     * @return MorphTo
     */
    public function hasemailaddress()
    {
        return $this->morphTo();
    }

    #endregion
}
