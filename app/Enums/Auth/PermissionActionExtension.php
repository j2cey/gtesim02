<?php

namespace App\Enums\Auth;

abstract class PermissionActionExtension extends PermissionAction
{
    public function __construct(array $extendedactions, string $permissionkey, array $customlevels = null, array $additionalactions = null)
    {
        $additionalactions = is_null($additionalactions) ? $extendedactions : array_merge($additionalactions, $extendedactions);
        parent::__construct($permissionkey, $customlevels, $additionalactions);
    }
}
