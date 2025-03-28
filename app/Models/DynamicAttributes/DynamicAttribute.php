<?php

namespace App\Models\DynamicAttributes;

use App\Models\Status;
use App\Models\BaseModel;
use App\Enums\HtmlTagKey;
use Illuminate\Support\Carbon;
use App\Models\FormatRule\FormatRule;
use Illuminate\Database\Eloquent\Model;
use App\Models\DynamicValue\DynamicRow;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\FormatRule\HasFormatRules;
use App\Models\DynamicValue\DynamicValue;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\FormatRule\IHasFormatRules;
use App\Traits\AnalysisRules\HasAnalysisRules;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Contracts\AnalysisRules\IHasAnalysisRules;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Contracts\DynamicAttribute\IHasDynamicAttributes;
use App\Contracts\AnalysisRules\IHasMatchedAnalysisRules;

/**
 * Class DynamicAttribu                                                                te
 * @package App\Models\DynamicAttributes
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property integer|null $status_id
 *
 * @property string $name
 * @property string $title
 * @property integer $num_ord
 * @property string|null $description
 *
 * @property string $offset
 * @property integer $max_length
 * @property bool $searchable
 * @property bool $sortable
 * @property bool $can_be_notified
 *
 * @property string $hasdynamicattribute_type
 * @property integer $hasdynamicattribute_id
 *
 * @property integer $dynamic_attribute_type_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property DynamicAttributeType $dynamicattributetype
 * @property IHasDynamicAttributes $hasdynamicattribute
 *
 * @method static DynamicAttribute first()
 */
class DynamicAttribute extends BaseModel implements Auditable, IHasAnalysisRules, IHasFormatRules
{
    use HasFactory, HasAnalysisRules, HasFormatRules, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['dynamicattributetype'];
    protected $casts = [
        'searchable' => 'boolean',
        'sortable' => 'boolean',
        'can_be_notified' => 'boolean',
    ];

    #region Validation Rules

    public static function defaultRules()
    {
        return [
            'dynamicattributetype' => ['required'],
        ];
    }

    public static function createRules()
    {
        return array_merge(self::defaultRules(), [
            'name' => ['required','unique:dynamic_attributes,name,NULL,id'],
        ]);
    }

    public static function updateRules($model)
    {
        return array_merge(self::defaultRules(), [
            'name' => ['required','unique:dynamic_attributes,name,'.$model->id.',id'],
        ]);
    }

    public static function messagesRules() {
        return [
            'name.required' => "Prière de renseigner le nom",
            'name.unique' => "Ce nom de projet est déjà utilisé",
            'dynamicattributetype.required' => "Prière de renseigner le Type",
        ];
    }

    #endregion

    #region Eloquent Relationships

    public function dynamicattributetype() {
        return $this->belongsTo(DynamicAttributeType::class,"dynamic_attribute_type_id");
    }

    /*public function analysisrules()
    {
        return $this->hasMany(AnalysisRule::class, 'dynamic_attribute_id');
    }*/

    /**
     * The Model which has this Attribute
     *
     * @return MorphTo
     */
    public function hasdynamicattribute()
    {
        return $this->morphTo();
    }

    /**
     * The (Dynqmic) values of this attribute
     * @return HasMany
     */
    public function values() {
        return $this->hasMany(DynamicValue::class, "dynamic_attribute_id");
    }

    /**
     * The lastest (Dynqmic) value of this attribute
     * @return HasOne
     */
    public function latestValue() {
        return $this->hasOne($this->dynamicattributetype->model_type, "dynamic_attribute_id")
            ->latest();
    }

    public function oldestValue() {
        return $this->hasOne($this->dynamicattributetype->model_type, "dynamic_attribute_id")
            ->oldest();
    }

    #endregion

    #region Custom Functions

    /**
     * @param IHasDynamicAttributes $model
     * @param Model|DynamicAttributeType $dynamicattributetype
     * @param string $name
     * @param string|null $title
     * @param Status|null $status
     * @param string|null $description
     * @param int|null $offset
     * @param int|null $max_length
     * @param bool|null $searchable
     * @param bool|null $sortable
     * @param bool|null $can_be_notified
     * @return DynamicAttribute
     */
    public static function createNew(IHasDynamicAttributes $model, Model|DynamicAttributeType $dynamicattributetype, string $name, string $title = null, Status $status = null, string $description = null, int $offset = null, int $max_length = null, bool $searchable = null, bool $sortable = null, bool $can_be_notified = null): DynamicAttribute {
        return $model->addDynamicAttribute($name, $dynamicattributetype, $title, $status, $description, $offset, $max_length, $searchable, $sortable, $can_be_notified);
    }

    /**
     * @param Model|DynamicAttributeType $dynamicattributetype
     * @param string $name
     * @param string|null $title
     * @param Status|null $status
     * @param string|null $description
     * @param int|null $offset
     * @param int|null $max_length
     * @param bool|null $searchable
     * @param bool|null $sortable
     * @param bool|null $can_be_notified
     * @return $this
     */
    public function updateThis(
        Model|DynamicAttributeType $dynamicattributetype,
        string $name,
        string $title = null,
        Status $status = null,
        string $description = null,
        int $offset = null,
        int $max_length = null,
        bool $searchable = null,
        bool $sortable = null,
        bool $can_be_notified = null
    ): DynamicAttribute
    {
        $this->name = $name;
        $this->title = $title ?? $name;
        $this->description = $description;
        $this->offset = $offset ?? $this->offset;
        $this->max_length = $max_length ?? $this->max_length;
        $this->searchable = $searchable;
        $this->sortable = $sortable;
        $this->can_be_notified = $can_be_notified;

        $this->dynamicattributetype()->associate($dynamicattributetype);
        if ( ! is_null($status) ) {
            $this->status()->associate($status);
        }

        $this->save();

        return $this;
    }

    /*public function addAnalysisRule(Model|AnalysisRuleType $analysisruletype, string $title, string $rule_result_for_notification, string $description = null): AnalysisRule
    {
        return AnalysisRule::createNew($this,$analysisruletype,$title,null,$rule_result_for_notification,$description);
    }*/



    public function addValue($thevalue, DynamicRow $dynamicrow) {

        $dynamicvalue = DynamicValue::where("dynamic_row_id", $dynamicrow->id)->where("dynamic_attribute_id")->first();

        if (! $dynamicvalue) {
            $dynamicvalue = DynamicValue::createNew($thevalue, $this, $dynamicrow);
            //DynamicValueCreatedEvent::dispatch($dynamicvalue->id, HtmlTagKey::TABLE_COL);
            $dynamicvalue->setFormattedValue(HtmlTagKey::TABLE_COL);//, $thevalue);
            $dynamicvalue->setDefaultFormatSize();
        }

        return $dynamicvalue;
    }

    /**
     * @param DynamicValue $dynamicValue
     * @param IHasMatchedAnalysisRules $ihasmatchedanalysisrules
     * @return array|Collection|FormatRule[]
     */
    public function getFormatRulesForNotification(DynamicValue $dynamicValue, IHasMatchedAnalysisRules $ihasmatchedanalysisrules)
    {
        $formatrules = new \Illuminate\Database\Eloquent\Collection;
        $formatrules = $formatrules->merge($this->formatrules);
        /*if ( $this->id === 2 ) {
            dump("1st: ", $formatrules);
        }*/
        foreach ($this->analysisrules as $analysisrule) {
            $curr_formatrules = $analysisrule->getFormatRulesForNotification($dynamicValue, $ihasmatchedanalysisrules);
            $formatrules = $formatrules->merge($curr_formatrules);
        }
        return $formatrules;
    }

    public static function getById(int|null $id): ?DynamicAttribute {
        if ( is_null($id) ) {
            return null;
        }
        return DynamicAttribute::find($id);
    }

    #endregion
}
