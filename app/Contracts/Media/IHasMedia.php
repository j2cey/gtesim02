<?php


namespace App\Contracts\Media;

use Spatie\MediaLibrary\HasMedia;
use OwenIt\Auditing\Contracts\Auditable;

interface IHasMedia extends Auditable, HasMedia
{

}
