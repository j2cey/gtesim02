<?php

namespace App\Traits\Media;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\InvalidBase64Data;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

trait HasMedia
{
    use InteractsWithMedia;

    public function saveBase64Image(array $image, string $collection_name, string $disk_name) : ?Media
    {
        if ( ! empty($image) ) {
            $file_name_arr = explode("-",Str::orderedUuid());
            $file_name = $file_name_arr[count($file_name_arr) - 1];

            // Create a new image from base64 string and attach it to article in article-images collection
            try {
                $path_info = pathinfo($image['name']);
                $this->addMediaFromBase64($image['base64data'])
                    ->usingFileName( $file_name . '.' . $path_info['extension'])
                    ->toMediaCollection($collection_name,$disk_name);
            } catch (FileDoesNotExist|FileIsTooBig|InvalidBase64Data|FileCannotBeAdded $e) {
                
            }

            // Get all images as we will need the last one uploaded
            $mediaItems = $this->load('media')->getMedia($collection_name);

            return $mediaItems[count($mediaItems) - 1];
        }
        return null;
    }
}
