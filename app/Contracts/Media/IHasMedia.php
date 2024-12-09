<?php


namespace App\Contracts\Media;

use App\Contracts\IsBaseModel;
use Spatie\MediaLibrary\HasMedia;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

interface IHasMedia extends Auditable, HasMedia, IsBaseModel
{
    public function saveBase64Image(array $image, string $collection_name, string $disk_name) : ?Media;
}
