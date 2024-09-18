<?php

namespace App\Models\Ldap;

use Illuminate\Support\Carbon;
use App\Traits\Base\BaseTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class LdapAccount
 *
 * @package App\Models\Ldap
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property integer|null $status_id
 * @property string $objectguid
 * @property string $username
 * @property string|null $email
 * @property string|null $password
 * @property string|null $cn
 * @property string|null $cn_result
 * @property string|null $sn
 * @property string|null $sn_result
 * @property string|null $title
 * @property string|null $title_result
 * @property string|null $description
 * @property string|null $description_result
 * @property string|null $physicaldeliveryofficename
 * @property string|null $physicaldeliveryofficename_result
 * @property string|null $telephonenumber
 * @property string|null $telephonenumber_result
 * @property string|null $givenname
 * @property string|null $givenname_result
 * @property string|null $distinguishedname
 * @property string|null $distinguishedname_result
 * @property string|null $service
 * @property string|null $division
 * @property string|null $direction
 * @property string|null $agence
 * @property string|null $zone
 * @property string|null $whencreated
 * @property string|null $whencreated_result
 * @property string|null $whenchanged
 * @property string|null $whenchanged_result
 * @property string|null $department
 * @property string|null $department_result
 * @property string|null $company
 * @property string|null $company_result
 * @property string|null $name
 * @property string|null $name_result
 * @property string|null $badpwdcount
 * @property string|null $badpwdcount_result
 * @property string|null $logoncount
 * @property string|null $logoncount_result
 * @property string|null $samaccountname
 * @property string|null $samaccountname_result
 * @property string|null $mail
 * @property string|null $mail_result
 * @property string|null $userprincipalname
 * @property string|null $userprincipalname_result
 * @property string|null $thumbnailphoto
 * @property string|null $thumbnailphoto_result
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereAgence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereBadpwdcount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereBadpwdcountResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereCn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereCnResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereCompanyResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereDepartmentResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereDescriptionResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereDistinguishedname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereDistinguishednameResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereGivenname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereGivennameResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereLogoncount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereLogoncountResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereMailResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereNameResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereObjectguid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount wherePhysicaldeliveryofficename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount wherePhysicaldeliveryofficenameResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereSamaccountname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereSamaccountnameResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereSnResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereTelephonenumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereTelephonenumberResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereThumbnailphoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereThumbnailphotoResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereTitleResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereUserprincipalname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereUserprincipalnameResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereWhenchanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereWhenchangedResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereWhencreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereWhencreatedResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LdapAccount whereZone($value)
 * @mixin \Eloquent
 */
class LdapAccount extends Authenticatable implements Auditable
{
    use HasFactory;
    use HasFactory, \OwenIt\Auditing\Auditable, BaseTrait;

    protected $guarded = [];

    #region Custom Functions

    public function getTableColumns() {
        //$columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        //$columns = \DB::getSchemaBuilder()->getColumnListing($this->getTable());

        return \Schema::getColumnListing($this->getTable());;
    }

    public function getLdapColumns() {
        $ldap_cols = [];
        //$no_ldap_cols = ['id','objectguid','created_at','updated_at'];
        foreach ($this->getTableColumns() as $col) {
            //if ( substr($col,-7) !== "_result" && ( ! in_array($col, $no_ldap_cols) ) ) {
            if ( substr($col,-7) === "_result" ) {
                $ldap_cols[] = str_replace("_result", "", $col);
            }
        }
        return $ldap_cols;
    }

    public function getAuthIdentifier()
    {
        return $this->samaccountname;
    }

    #endregio
}
