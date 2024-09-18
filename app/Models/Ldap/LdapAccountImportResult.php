<?php

namespace App\Models\Ldap;

use PHPUnit\Util\Json;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class LdapAccountImportResult
 *
 * @package App\Models\Ldap
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 * @property integer $lines_count
 * @property integer $lines_parsed
 * @property integer $lines_parse_success
 * @property integer $lines_parse_fail
 * @property Json $report
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereLinesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereLinesParseFail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereLinesParseSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereLinesParsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImportResult whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */
class LdapAccountImportResult extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
    protected $guarded = [];
}
