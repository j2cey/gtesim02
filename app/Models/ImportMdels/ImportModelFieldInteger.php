<?php

namespace App\Models\ImportModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ImportModels\ImportModelFieldInteger
 *
 *
 * @property int $id
 * @property string|null $importvalue the import value
 * @property string|null $comment comment
 * @property string $uuid
 * @property int|null $status_id status reference
 * @property int $is_default determine whether is the default one.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger query()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereImportvalue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImportModelFieldInteger whereUuid($value)
 * @mixin \Eloquent
 */

class ImportModelFieldInteger extends Model
{
    use HasFactory;
}
