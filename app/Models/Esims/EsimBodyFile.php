<?php

namespace App\Models\Esims;

use App\Models\BaseModel;
use App\Traits\File\HasFile;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class EsimBodyFile
 *
 * @package App\Models\Esims
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property string $title
 * @property string $config_key
 * @property string $comment
 * @property integer|null $statut_esim_id
 * @property integer|null $technologie_esim_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $status_id status reference
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Files\File|null $file
 * @property-read \App\Models\Files\FileImportResult|null $fileimportresult
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files\File[] $files
 * @property-read int|null $files_count
 * @property-read \App\Models\Files\File|null $latestFile
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereConfigKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimBodyFile whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class EsimBodyFile extends BaseModel implements Auditable
{
    use HasFile, SoftDeletes, HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'imsi' => ['required'],
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

    #endregion

    #region Custom Functions

    #endregion
}
