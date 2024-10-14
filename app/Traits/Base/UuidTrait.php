<?php

namespace App\Traits\Base;

use Illuminate\Support\Str;
use App\Contracts\IsBaseModel;

trait UuidTrait
{
    public static function generateUuid()
    {
        return Str::orderedUuid();
    }

    public static function getByUuid(string $uuid): ?IsBaseModel {
        return self::where('uuid', $uuid)->first();
    }
}
