<?php

namespace App\Models\Ldap;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class LdapAccountImport
 *
 * @package App\Models\Ldap
 * @property integer $id
 * @property string|null $objectguid
 * @property string|null $username
 * @property string|null $name
 * @property string|null $email
 * @property string|null $password
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport query()
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport whereObjectguid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccountImport whereUsername($value)
 * @mixin \Eloquent
 */
class LdapAccountImport extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
    protected $guarded = [];
}
