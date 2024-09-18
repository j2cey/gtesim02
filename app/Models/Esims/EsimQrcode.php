<?php

namespace App\Models\Esims;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Esim
 *
 * @package App\Models\Esims
 * @property integer $id
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 * @property string|null $raw_value
 * @property string|null $qrcode_img
 * @property integer|null $esim_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $status_id status reference
 * @property int|null $created_by user creator reference
 * @property int|null $updated_by user updator reference
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Esims\Esim|null $esim
 * @property-read mixed $image_folderpath
 * @property-read mixed $image_fullpath
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel default($exclude = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode query()
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereEsimId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereQrcodeImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereRawValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EsimQrcode whereUuid($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Status|null $status
 */

class EsimQrcode extends BaseModel implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    public const CONFIG_DIR = "esim_fichier_qrcode";

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

    public function esim() {
        return $this->belongsTo(Esim::class, 'esim_id');
    }

    #endregion

    #region Accessors

    public function getImageFullpathAttribute() {
        $separator = "/";
        if ( ! is_null($this->qrcode_img) ) {
            return public_path('/') . config('app.' . self::CONFIG_DIR) . $separator . $this->qrcode_img;
        }
        return null;
    }

    public function getImageFolderpathAttribute() {
        $separator = "/";
        return public_path('/') . config('app.' . self::CONFIG_DIR) . $separator;
    }

    #endregion

    #region Custom Functions

    public static function createNew(Esim $esim, $raw_value)
    {
        $esimqrcode = EsimQrcode::create([
            'raw_value' => $raw_value,
        ]);

        $esimqrcode->esim()->associate($esim);

        $esimqrcode->save();

        $esimqrcode->generateQrcodeImage();

        return $esimqrcode;
    }

    public function generateQrcodeImage() {
        // <img src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(150)->generate($client->esim->ac)) }} ">
        //$directory = "esim_fichier_qrcode";

        //$file_dir = config('app.' . self::CONFIG_DIR);
        $file_name = md5(self::CONFIG_DIR . '_' . time()) . '.png';
        //$output_file = $this->image_folderpath . $file_name;

        $this->qrcode_img = $file_name;
        $this->save();

        $image = QrCode::format('png')
            //->merge('img/jpg', 0.1, true)
            ->size(200)->errorCorrection('H')
            ->generate($this->raw_value, $this->image_fullpath);

        //Storage::disk('public')->put($output_file, $image);
    }

    #endregion

    public static function boot ()
    {
        parent::boot();

        // juste avant suppression
        self::creating(function($model){

        });
    }
}
