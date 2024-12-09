<?php

namespace App\Models\HowTos;

use App\Models\BaseModel;
use Illuminate\Support\Str;
use App\Traits\Code\HasCode;
use Illuminate\Support\Carbon;
use App\Traits\Media\HasMedia;
use App\Contracts\IsBaseModel;
use App\Contracts\Media\IHasMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\InvalidBase64Data;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

/**
 * Class HowTos
 *
 * @package App\Models
 *
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string $title
 * @property string $code
 * @property string|null $view
 * @property string|null $htmlbody
 * @property string $description
 * @property int|null $how_to_type_id howto_step_type reference
 * @property int|null $status_id status reference
 * @property integer|null $created_by
 * @property integer|null $updated_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read \App\Models\Status $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\HowTos\HowToType|null $howtotype
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo query()
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo withAnyTagsOfAnyType($tags)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereHowToNextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereHowToPrevId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereHowToTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo wherePosi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HowTo whereView($value)
 */

class HowTo extends BaseModel implements IHasMedia
{
    use HasMedia, HasCode, HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    protected $with = ['tags'];

    protected $auditExclude = [
        'htmlbody', 'htmlbody_parsed',
    ];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'title' => ['required'],
            'howtotype' => ['required'],
            'description' => ['required'],
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
            'description.required' => 'The Description is required',
        ];
    }

    #endregn

    #region Eloquent Relationships

    public function howtotype(): BelongsTo
    {
        return $this->belongsTo(HowToType::class, 'how_to_type_id');
    }

    #endregion

    #region Custom Functions

    /**
     * @param HowToType $howtotype
     * @param $title
     * @param $view
     * @param $description
     * @param null $code
     * @param null $tags
     * @return HowTo
     */
    public static function createNew(HowToType $howtotype, $title, $view, $description, $code = null, $tags = null) : HowTo
    {
        $howto = self::create([
            'title' => $title,
            'code' => $code,
            'view' => $view,
            'description' => $description,
        ]);

        $howto->howtotype()
            ->associate($howtotype)
            ->save();

        if ( ! is_null($tags)) {
            $howto->syncTags($tags);
        }

        return $howto;
    }

    public function updateOne(HowToType $howtotype, $title, $view, $description, $code = null, $tags = null) : HowTo
    {
        //dd($tags);
        $current_user = Auth::user();
        $this->update([
            'title' => $title,
            'view' => $view,
            'description' => $description,
        ]);

        $this->howtotype()
            ->associate($howtotype)
            ->save();

        if ( ! is_null($code) && ($current_user && $current_user->can('howto-update-code')) ) {
            $this->update([
                'code' => $code,
            ]);
        }

        if ( ! is_null($tags)) {
            $this->syncTags($tags);
        }

        return $this;
    }

    public function saveHtmlBody(string $htmlbody, array $images): static
    {
        $this->update([
            'htmlbody_parsed' => html_entity_decode($htmlbody)
        ]);

        $this->update([
            'htmlbody' => html_entity_decode($htmlbody)
        ]);

        //$howto->htmlbody = $body;
        //$howto->save();

        // If images not empty
        if ($images) {
            foreach ($images as $image)
            {
                // Create a new image from base64 string and attach it to article in article-images collection
                $media = $this->saveBase64Image($image, 'howto-images','howtos');

                // Replace the base64 string in article body with the url of the last uploaded image
                $this->htmlbody = str_replace($image['base64data'], $media->getFullUrl(), $this->htmlbody);
            }
        }

        $this->save();
        $this->removeImagesNotPresent();

        return $this;
    }

    private function removeImagesNotPresent(): void
    {
        $mediaItems = $this->load('media')->getMedia('howto-images');
        foreach ($mediaItems as $mediaItem)
        {
            if ( ! str_contains($this->htmlbody, $mediaItem->getFullUrl()) ) {
                $mediaItem->delete();
            }
        }
    }

    public static function getById(int $id) : ?HowTo {
        return HowTo::whereId($id)->first();
    }

    #endregion
}
