<?php

namespace App\Contracts;

use App\Models\Status;

interface IsBaseModel
{
    public function setDefaultStatus();
    public function activateStatus();
    public function deactivateStatus();
    public function setStatus(Status $status = null, $save = false);

    public function setDefault($new_val = 1);
    public function unsetDefault($id);
    public static function getDefault($val = 1, $exclude = []);

    public static function getByUuid(string $uuid): ?IsBaseModel;
}
