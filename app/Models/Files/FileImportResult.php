<?php

namespace App\Models\files;

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class FileImportResult
 *
 * @package App\Models\Files
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 * @property boolean $imported
 * @property Carbon $importstart_at
 * @property Carbon $importend_at
 * @property integer $nb_rows
 * @property boolean $import_processing
 * @property integer $nb_rows_success
 * @property integer $nb_rows_failed
 * @property integer $nb_rows_processing
 * @property integer $nb_rows_processed
 * @property integer|null $file_id
 * @property string $row_last_processed
 * @property integer $nb_try
 * @property Json $report
 * @property Carbon $suspended_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $hasimportfileresult_type referenced model type
 * @property int $hasimportfileresult_id referenced model id
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereHasimportfileresultId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereHasimportfileresultType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereImportProcessing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereImported($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereImportendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereImportstartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereNbRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereNbRowsFailed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereNbRowsProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereNbRowsProcessing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereNbRowsSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereNbTry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereRowLastProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileImportResult whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */
class FileImportResult extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

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

    #endregion

    #region Custom Functions

    #endregion



}

