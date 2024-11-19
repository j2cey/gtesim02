<?php

namespace App\Models;

use Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Jobs\LdapUserSavedJob;
use App\Models\Employes\Employe;
use App\Models\Employes\Departement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use LdapRecord\Laravel\Auth\HasLdapUser;
use App\Models\Employes\FonctionEmploye;
use Illuminate\Support\Facades\Validator;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use function PHPUnit\Framework\isEmpty;

/**
 * Class LdapUser
 * @package App\Models
 *
 * @property integer $id
 *
 * @property string $guid
 * @property string $name
 * @property string $firstname
 * @property string $lastname
 * @property string $login
 * @property string $email
 * @property string $domain
 * @property string $telephone
 * @property string $distinguishedname
 * @property string $department_tree
 * @property string $title
 *
 * @property string $password
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property FonctionEmploye $fonction
 * @property Departement $departement
 *
 * @method static LdapUser find($id)
 */
class LdapUser extends Model implements LdapAuthenticatable
{
    use HasFactory, AuthenticatesWithLdap, HasLdapUser;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'firstname' => ['required',],
            'lastname' => ['required',],
            'login' => ['required',],
            'email' => ['required','email',],
            'telephone' => ['required',],
            'domain' => ['required',],
        ];
    }
    public static function createRules()  {
        return array_merge(self::defaultRules(), [
            'guid' => ['required','unique:ldap_users,guid,NULL,id',],
        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [
            'guid' => ['required','unique:ldap_users,guid,'.$model->id.',id',],
        ]);
    }
    public static function messagesRules() {
        return [
            'firstname.required' => 'First Name is Required',
            'lastname.required' => 'Last Name is Required',
            'login.required' => 'Login is Required',
            'telephone.required' => 'Phone number is Required',
            'email.required' => 'Email is Required',
            'email.email' => 'Email must be a regular email',
            'domain.required' => 'Domain is Required',
        ];
    }

    #endregion

    #region Eloquent Relationships

    public function fonction() {
        return $this->belongsTo(FonctionEmploye::class, 'fonction_employe_id');
    }

    public function departement() {
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    #endregion

    #region Custom Functions

    /**
     * @param string $guid
     * @param string $firstname
     * @param string $lastname
     * @param string $login
     * @param string $email
     * @param string $telephone
     * @param string $domain
     * @param string|null $distinguishedname
     * @param Departement|null $departement
     * @param FonctionEmploye|null $fonction
     * @return $this
     */
    public function updateOne(string $guid, string $firstname, string $lastname, string $login, string $email, string $telephone, string $domain, string $distinguishedname = null, Departement $departement = null, FonctionEmploye $fonction = null): static
    {
        $data = [
            'guid' => $guid,
            'name' => $firstname . " " . $lastname,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'login' => $login,
            'email' => $email,
            'telephone' => $telephone,
            'domain' => $domain,
        ];
        if (! is_null($distinguishedname)) $data['distinguishedname'] = $distinguishedname;

        $this->update($data);

        if (! is_null($departement)) $this->departement()->associate($departement);
        if (! is_null($fonction)) $this->fonction()->associate($fonction);

        $this->save();

        return $this;
    }
    public function integrate(User $user = null): User {
        if ( is_null($user) ) {
            $user = User::updateOrNew($this->name, $this->login, $this->email, false, true, $this->guid, $this->domain);
            $user->password = $this->password;
        } else {
            $user->guid = $this->guid;
            $user->domain = $this->domain;
            $user->is_ldap = true;
        }
        $user->save();

        $user->load('employe');
        if ( ! $user->employe ) {
            Employe::createNew($this->lastname, $this->firstname, $this->guid, $this->fonction, $this->departement, null, $user->uuid);
        }

        $user->ldapAssociate(true);

        return $user;
    }

    public function formatTitle(): static
    {
        if ( ! $this->fonction && ! empty($this->title) ) {
            $intitule_fonctionemploye = strtolower($this->title);
            $intitule_fonctionemploye = ucwords($intitule_fonctionemploye);
            $fonctionemploye = FonctionEmploye::whereSlug(Str::slug($intitule_fonctionemploye))->first();
            if ( ! $fonctionemploye ) {
                $fonctionemploye_values = [
                    'intitule' => $intitule_fonctionemploye,
                    'slug' => Str::slug($intitule_fonctionemploye),
                    'description' => $this->title,
                    'status_id' => Status::active()->first()->id,
                ];
                $validator = Validator::make($fonctionemploye_values, FonctionEmploye::createRules());
                if (!$validator->fails()) {
                    $fonctionemploye = FonctionEmploye::create($fonctionemploye_values);
                    $this->fonction()->associate($fonctionemploye)->save();
                } else {
                    Log::info("FonctionEmploye " . $intitule_fonctionemploye . " NOT created!!!. validator->fails() : " . $validator->fails());
                    Log::info("Errors " . json_encode( $validator->errors()->all() ) );
                    //$this->logValidatorErrors($validator);
                }
            } else {
                $this->fonction()->associate($fonctionemploye)->save();
            }
        }
        return $this;
    }

    public function formatDepartment(): static
    {
        if ( ! $this->departement && ! empty($this->distinguishedname) && ! empty($this->name) ) {
            // remove the name
            $dpt_tree = str_replace("CN=" . $this->name, "", $this->distinguishedname);
            $this->department_tree = str_replace(["OU=UTILISATEURS", "DC=groupegt", "DC=ga", "OU="], "", $dpt_tree);
            $dpt = $this->parseDepartementTree();
            if ($dpt) {
                $this->departement()->associate($dpt)->save();
            }
        }
        return $this;
    }

    /**
     * Parse le chemin d'un département
     * @return Departement|null
     */
    private function parseDepartementTree(): ?Departement
    {
        try {
            $tree_trimmed = $this->trimCharLeftAndRight($this->department_tree, ",");
            $tree_tab = explode(",", $tree_trimmed);
            $prev_dept = null;
            $first_dept = null;
            foreach ($tree_tab as $dept) {
                if (!empty($dept)) {
                    $dept = $this->formatDepartementIntitule($dept);
                    $curr_dept = Departement::whereIntitule($dept)->first();
                    if (!$curr_dept) {
                        // création d'un nouveau département
                        $curr_dept = Departement::create([
                            'intitule' => $dept,
                            'status_id' => Status::active()->first()->id,
                        ]);
                        // Recherche du type de dépertement en fonction de l'intitulé
                        $type_dpt_id = $this->parseDepartementType($dept);
                        if ($type_dpt_id) {
                            $curr_dept->type_departement_id = $type_dpt_id;
                        }
                    }
                    // Set du parent du précédent
                    if ($prev_dept) {
                        $prev_dept->departement_parent_id = $curr_dept->id;
                        $prev_dept->save();
                    } else {
                        $first_dept = $curr_dept;
                    }
                    $curr_dept->description = $dept;
                    $curr_dept->save();
                    // On assigne le précédent
                    $prev_dept = $curr_dept;
                }
            }
            return $first_dept;
        } catch (\Exception $e) {
            Log::info("Error parseDepartementTree " . $e->getMessage() . " Line: " . $e->getLine());
            return null;
        }
    }

    /**
     * Essaie de déduire le type d'un département
     * @param $intitule string intitulé du département
     * @return TypeDepartement|null
     */
    private function parseDepartementType(string $intitule): ?TypeDepartement
    {
        if (str_contains(strtolower($intitule), 'direction')) {
            $type = TypeDepartement::whereIntitule('Direction')->first();
            return $type->id;
        } elseif (str_contains(strtolower($intitule), 'division')) {
            $type = TypeDepartement::whereIntitule('Division')->first();
            return $type->id;
        } elseif (str_contains(strtolower($intitule), 'zone')) {
            $type = TypeDepartement::whereIntitule('Zone')->first();
            return $type->id;
        } elseif (str_contains(strtolower($intitule), 'service')) {
            $type = TypeDepartement::whereIntitule('Service')->first();
            return $type->id;
        } elseif (str_contains(strtolower($intitule), 'agence')) {
            $type = TypeDepartement::whereIntitule('Agence')->first();
            return $type->id;
        } else {
            return null;
        }
    }

    /**
     * Formate l'intitulé d'un département
     * @param string $intitule
     * @return string
     */
    private function formatDepartementIntitule(string $intitule): string
    {
        $sigles = config('Settings.ldap.liste_sigles');
        $intitule_tab = explode(' ', $intitule);

        for ($i = 0; $i < count($intitule_tab); $i++) {
            // Mettre en minuscules
            $intitule_tab[$i] = strtolower($intitule_tab[$i]);

            // Replaces: tous les sigles
            if ( ! is_null($sigles) ) {
                foreach ($sigles as $sigle) {
                    if (strlen($intitule_tab[$i]) == strlen($sigle)) {
                        $intitule_tab[$i] = str_replace(strtolower($sigle), strtoupper($sigle), $intitule_tab[$i]);
                    }
                }
            }

            // Mettre les debuts de mot en Majuscule
            $firs_car = substr($intitule_tab[$i], 0, 1);
            if (ctype_alpha($firs_car)) {
                // Le 1er caractère est alphabétique
                $intitule_tab[$i] = ucwords($intitule_tab[$i]);
            } else {
                // Le 1er caractère n'est alphabétique
                // Alors on met 1er caractère du reste en Majuscule
                $intitule_tab[$i] = $firs_car . ucwords(substr($intitule_tab[$i], 1, strlen($intitule_tab[$i]) - 1));
            }

            // Les sigles entre parenthèses
            if ( (str_starts_with($intitule_tab[$i], "(")) && (str_ends_with($intitule_tab[$i], ")")) && (strlen($intitule_tab[$i]) <= 7) ) {
                $intitule_tab[$i] = strtoupper($intitule_tab[$i]);
            }
        }
        return implode(' ', $intitule_tab);
    }

    private function trimCharLeftAndRight(string $str, string $char): string
    {
        $new_str = $str;
        while (substr($new_str, -1) === $char || substr($new_str, 0) === $char) {
            $new_str = ltrim($new_str, $char);
            $new_str = rtrim($new_str, $char);
        }
        return $new_str;
    }

    public static function getByGuid(string $guid): ?LdapUser
    {
        return LdapUser::whereGuid($guid)->first();
    }

    public static function import(string $email): ?LdapUser {
        $ldapuser = LdapUser::whereEmail($email)->first();

        if ( ! $ldapuser ) {
            Artisan::call('ldap:import', ['provider' => 'ldap_users', '--filter' => '(mail=' . $email .')', '--no-interaction']);
            $ldapuser = LdapUser::whereEmail($email)->first();
        }

        return $ldapuser;
    }

    #endregion

    #region BOOT

    public static function boot(){
        parent::boot();

        // when created
        self::created(function($model){
            //$model->formatTitle();
            //$model->formatDepartment();
            //LdapUserSavedJob::dispatch( $model->id );
        });
    }

    #endregion
}
