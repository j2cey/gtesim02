<?php

namespace App\Models\Esims;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use App\Contracts\IsBaseModel;
use App\Models\Person\PhoneNum;
use Illuminate\Support\Facades\Mail;
use App\Traits\PhoneNum\HasPhoneNums;
use App\Mail\NotifyClientEsimProfile;
use App\Contracts\Persons\IHasPhoneNums;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Contracts\Persons\IHasEmailAddresses;
use App\Traits\EmailAddress\HasEmailAddresses;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ClientEsim
 *
 * @package App\Models\Esims
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property string $nom_raison_sociale
 * @property string $prenom
 * @property string $email_address
 * @property string $email_address_list
 * @property string $phone_number
 * @property string $phone_number_list
 * @property string $pin
 * @property string $puk
 * @property integer|null $esim_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $status_id status reference
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Person\EmailAddress[] $emailaddresses
 * @property-read int|null $emailaddresses_count
 * @property-read \App\Models\Esims\Esim|null $esim
 * @property-read PhoneNum|null $latestPhonenum
 * @property-read \App\Models\Person\EmailAddress|null $oldestEmailAddress
 * @property-read PhoneNum|null $oldestPhonenum
 * @property-read \Illuminate\Database\Eloquent\Collection|PhoneNum[] $phonenums
 * @property-read int|null $phonenums_count
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereEsimId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereNomRaisonSociale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereNumeroTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim wherePin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim wherePuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEsim whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class ClientEsim extends BaseModel implements IsBaseModel, IHasPhoneNums, IHasEmailAddresses
{
    use HasPhoneNums, HasEmailAddresses, SoftDeletes, HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'nom_raison_sociale' => ['required'],
        ];
    }
    public static function createRules($phone_number) {
        return array_merge(self::defaultRules(), PhoneNum::createRules($phone_number,self::class), [
            'email_address' => ['required','email'],
            'phone_number' => ['starts_with:060,065,066'],
        ]);
    }
    public static function updateRules($model,$phone_number) {
        return array_merge(self::defaultRules(), PhoneNum::updateRules($model,$phone_number,self::class), [

        ]);
    }
    public static function messagesRules() {
        return array_merge(PhoneNum::messagesRules(), [
            'nom_raison_sociale.required' => 'Nom ou Raison Sociale du client requis',
            'email_address.required' => 'Adresse e-mail requise',
            'email_address.email' => 'Adresse e-mail non valide',
            'phone_number.starts_with' => 'Le numéro de téléphone doit commencer par 060,065,066',
        ]);
    }

    #endregion

    #region Accessors

    public function getNomcompletAttribute() {
        return $this->nom_raison_sociale . " " . $this->prenom;
    }

    #endregion

    #region Eloquent Relationships

    public function esim() {
        return $this->belongsTo(Esim::class, 'esim_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    #endregion

    #region Custom Functions

    public static function createNew($nom_raison_sociale, $prenom, $email_address, $phone_number): ClientEsim
    {
        $clientesim = ClientEsim::create([
            'nom_raison_sociale' => strtoupper($nom_raison_sociale),
            'prenom' => ucwords($prenom),
            'email_address' => $email_address,
            'phone_number' => $phone_number,
        ]);

        return $clientesim;
    }

    public function updateOne($esim_id, $nom_raison_sociale, $prenom, $email_address, $phone_number)
    {
        //$esim = Esim::getFirstFree($esim_id);

        //$esim->setStatutFree();

        $this->update([
            'nom_raison_sociale' => $nom_raison_sociale,
            'prenom' => $prenom,
            //'email_address' => email_address,
            //'phone_number' => phone_number,
        ]);

        //$this->esim()->associate($esim);

        $this->save();

        //$esim->setStatutAttribue();

        return $this;
    }

    public function sendmailprofile(PhoneNum $phonenum)
    {
        $this->sendMailDirect($phonenum);
    }

    private function sendMailDirect(PhoneNum $phonenum) {
        Mail::to($this->firstEmailAddress->email_address)
            ->send(new NotifyClientEsimProfile($this, $phonenum));

        Mail::to("J.NGOMNZE@moov-africa.ga")
            ->send(new NotifyClientEsimProfile($this, $phonenum));
    }

    private function sendMailRemote(PhoneNum $phonenum) {
        $error_message = "";
        try {
            $post_link = "http://192.168.5.174/clientesims.sendmail";
            $directory = "esim_fichier_qrcode";

            $phonenum->esim->saveQrcode();

            $file_name = public_path('/') . config('app.' . $directory) . '/' . $phonenum->esim->qrcode->qrcode_img;

            $qrcode_img = $file_name;

            $client = new Client(['headers' => ['Authorization' => 'auth_trusted_header']]);
            $options = [
                'multipart' => [
                    [
                        'Content-type' => 'multipart/form-data',
                        'name' => 'file',
                        'contents' => file_get_contents($qrcode_img),//base64_encode( file_get_contents($qrcode_img) ), // fopen('data:image/png;base64,' . $qrcode_img, 'r'), // data://text/plain;base64
                        'filename' => 'qrcode_image.png',
                    ],
                    ['name' => 'nom', 'contents' => $this->nom_raison_sociale . ' ' .$this->prenom],
                    ['name' => 'email', 'contents' => $this->latestEmailAddress->email_address,],
                    ['name' => 'telephone', 'contents' => $phonenum->phone_number,],
                    ['name' => 'imsi', 'contents' => $phonenum->esim->imsi,],
                    ['name' => 'iccid', 'contents' => $phonenum->esim->iccid,],
                    ['name' => 'pin', 'contents' => $phonenum->esim->pin,],
                    ['name' => 'puk', 'contents' => $phonenum->esim->puk,],
                    ['name' => 'ac', 'contents' => $phonenum->esim->ac,],
                ]
            ];

            return $client->post($post_link, $options);
        } catch (ConnectException $e) {
            $error_message = 'sendmailprofile FAILS ! status: ' . 404;
            $error_message = $error_message . '; msg: ' . $e->getMessage();
        } catch (RequestException $e) {
            $error_message = 'sendmailprofile FAILS ! status: ' . $e->getResponse()->getStatusCode();
            $error_message = $error_message . '; msg: ' . $e->getMessage();
        } catch (\Exception $e) {
            $error_message = 'sendmailprofile FAILS ! status: ' . 0;
            $error_message = $error_message . '; msg: ' . $e->getMessage();
        } finally {
            if ($error_message !== "") {
                \Log::error($error_message);
            }
        }
    }

    #endregion
    public function getIntituleAttribute()
    {
        return $this->nom_raison_sociale . " " . $this->prenom;
    }
}
