<?php

namespace App\Enums\Auth;

class BasePersonAction extends BaseModelAction
{
    public function __construct(string $permissionkey, array $customlevels = null, array $additionalactions = null)
    {
        $baseemployeactions = [
            'phonenum-show' => 2,
            'phonenum-add' => 3,
            'phonenum-update' => 3,
            'phonenum-delete' => 2,

            'emailaddress-show' => 2,
            'emailaddress-add' => 3,
            'emailaddress-update' => 3,
            'emailaddress-delete' => 2,
        ];
        parent::__construct($permissionkey, $customlevels, $additionalactions, $baseemployeactions);
    }
}
