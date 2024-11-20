<?php

namespace App\Models;

use App\Models\Esims\Esim;
use App\Traits\Base\BaseTrait;
use Illuminate\Support\Carbon;
use App\Contracts\IsBaseModel;
use App\Models\Person\PhoneNum;
use App\Models\Comments\Comment;
use App\Models\Esims\ClientEsim;
use App\Models\Employes\Employe;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use LdapRecord\Laravel\Auth\HasLdapUser;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * Class User
 * @package App\Models
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 *
 * @property string $name
 * @property string $login
 * @property string $email
 * @property Carbon $email_verified_at
 *
 * @property string $password
 *
 * @property string $guid
 * @property string $domain
 *
 * @property string|null $avatar
 * @property boolean $is_local
 * @property boolean $is_ldap
 *
 * @property string $local_password
 * @property string $ldap_password
 *
 * @property string|null $objectguid
 * @property string $login_type
 * @property Carbon $last_seen
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property LdapUser $ldapaccount
 * @property Employe $employe
 * @method static User whereEmail($email)
 * @method static whereLogin($email)
 * @method User first()
 * @method User static create(array $array)
 * @method static User create(array $array)
 */
class User extends Authenticatable implements IsBaseModel, Auditable, LdapAuthenticatable
{
    use HasFactory, Notifiable, HasRoles, \OwenIt\Auditing\Auditable, BaseTrait, AuthenticatesWithLdap, HasLdapUser;

    protected $guard_name = 'web';
    public function getRouteKeyName() { return 'uuid'; }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'login',
        'email',
        'password',
        'avatar',
        'is_local',
        'is_ldap',
        'guid',
        'domain',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_local' => 'boolean',
            'is_ldap' => 'boolean'
        ];
    }

    protected $appends = [
        'formatted_created_at',
    ];

    #region Validation Tools

    public static function defaultRules() {
        return [
            'name' => ['required','string',],
            'login' => ['required'],
            'email' => ['required','email'],
        ];
    }
    public static function createRules()  {
        return array_merge(self::defaultRules(), [
            'password' => ['required'],
        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [
        ]);
    }
    public static function validationMessages() {
        return [
            'name.required' => 'Le Nom est requis',
            'login.required' => 'Le Login est requis',
            'email.required' => 'Adresse e-mail requise',
            'email.email' => 'Adresse e-mail non valide',
        ];
    }

    #endregion

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format(config('Settings.date.format'));
    }

    public function avatar(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset($value ? Storage::url($value) : 'images/default-user-image.png'),
        );
    }

    #region Eloquent Relationships
    public function status() {
        return $this->belongsTo(Status::class);
    }
    /**
     * Renvoie le Compte LDAP du User.
     */
    public function ldapaccount() {
        return $this->belongsTo(LdapUser::class, 'ldap_user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the employe associated with the user.
     */
    public function employe()
    {
        return $this->hasOne(Employe::class, "user_id");
    }

    public function phonesesimcreated() {
        return $this->hasMany(PhoneNum::class, "created_by", "id")
            ->where('hasphonenum_type', ClientEsim::class)
            ;
    }

    public function esimsattributed() {
        return $this->hasMany(Esim::class, "attributed_by", "id");
    }
    #endregion

    #region Custom Functions
    /**
     * Create and store a New User
     * @param string $name
     * @param string $login
     * @param string $email
     * @param bool $is_local
     * @param bool $is_ldap
     * @param string|null $guid
     * @param string|null $domain
     * @return User
     */
    public static function createNew(string $name, string $login, string $email, bool $is_local = true, bool $is_ldap = false, string $guid = null, string $domain = null): User
    {
        $data = [
            'name' => $name,
            'email' => $email,
            'login' => $login,
            'is_local' => $is_local,
            'is_ldap' => $is_ldap,
            'password' => "NOT-SET",
        ];
        if (! is_null($guid)) $data['guid'] = $guid;
        if (! is_null($domain)) $data['domain'] = $domain;

        return User::create( $data );
    }

    public function updateOne(string $name, string $login, string $email, bool $is_local = true, bool $is_ldap = false, string $guid = null, string $domain = null): User
    {
        $this->name = $name;
        $this->email = $email;
        $this->login = $login;
        $this->is_local = $is_local;
        $this->is_ldap = $is_ldap;
        if (! is_null($guid)) $this->guid = $guid;
        if (! is_null($domain)) $this->domain = $domain;

        $this->save();

        return $this;
    }

    public static function updateOrNew(string $name, string $login, string $email, bool $is_local = true, bool $is_ldap = false, string $guid = null, string $domain = null): User
    {
        $user = User::whereEmail($email)->first();

        if ($user) {
            return $user->updateOne($name, $login, $email, $is_local, $is_ldap, $guid, $domain);
        } else {
            return User::createNew($name, $login, $email, $is_local, $is_ldap, $guid, $domain);
        }
    }

    public static function getByUuid(string $uuid): ?User {
        return User::where('uuid', $uuid)->first();
    }
    public function switchLocalPassword() {
        if ( is_null($this->local_password) ) {
            $this->local_password = $this->password;
        } else {
            $this->password = $this->local_password;
        }
        $this->saveQuietly();
    }

    public function setPassword($password) {
        if ( ! is_null($password) ) {
            $this->password = $this->getPasswordHash($password);
            $this->saveQuietly();
        }
    }

    public function localPwdSet($password) {
        if ( ! is_null($password) && $this->is_local ) {
            $this->local_password = $this->getPasswordHash($password);
            $this->saveQuietly();
        }
    }
    public function ldapPwdSet($password): void
    {
        if ( ! is_null($password) && $this->is_ldap ) {
            $this->ldap_password = $password;
            $this->saveQuietly();
        }
    }

    public function ldapPwdSwitch(): void
    {
        if ( ! is_null($this->ldap_password) ) {
            $this->password = $this->ldap_password;
            $this->save();
        }
    }
    public function ldapPwdSaveValidated($validated): void
    {
        if ($validated) {
            $this->ldap_password = $this->password;
            $this->save();
        }
    }
    public function localPwdSwitch(): void
    {
        if ( ! is_null($this->local_password) ) {
            $this->password = $this->local_password;
            $this->save();
        }
    }
    public function localPwdSaveValidated($validated): void
    {
        if ($validated) {
            $this->local_password = $this->password;
            $this->save();
        }
    }

    public function ldapAssociate(bool $force = false) {
        if ( ! empty($this->guid) && ( ! $this->ldapaccount || $force ) ) {
            $ldapuser = LdapUser::getByGuid($this->guid);
            if ($ldapuser) {
                $this->ldapaccount()->associate($ldapuser)->save();
            }
        }
    }

    private function getPasswordHash($password) {
        return bcrypt($password);
    }
    #endregion

    #region BOOT

    public static function boot(){
        parent::boot();

        // when creation
        self::saving(function($model){
            // set additional passwords
            if ( $model->is_local && is_null($model->local_password) ) {
                $model->local_password = $model->password;
            } elseif ( $model->is_ldap && is_null($model->ldap_password) ) {
                $model->ldap_password = $model->password;
            }
        });
    }

    #endregion
}
