<?php

namespace App\Enums\Auth;

class BaseModelAction extends PermissionActionExtension
{
    public function __construct(string $permissionkey, array $customlevels = null, array $additionalactions = null, array $extendedactions = null)
    {
        $basemodelactions = [
            'status-change' => 2,
            'creator-show' => 2,
            'creator-list' => 2,
            'updator-show' => 2,
            'updator-list' => 2,
        ];
        $basemodelactions = is_null($extendedactions) ? $basemodelactions : array_merge($basemodelactions, $extendedactions);
        parent::__construct($basemodelactions, $permissionkey, $customlevels, $additionalactions);
    }
}
