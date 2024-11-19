<?php

namespace App\Enums\Settings\Branches;

use App\Enums\Settings\SettingNode;

/**
 * Class Ldap. Raw Date settings
 * @package App\Enums\Settings\Branches
 *
 * @method liste_sigles()
 */
class Ldap extends SettingNode
{
    public function __construct()
    {
        parent::__construct("ldap",null,null,null,null,"LDAP Settings");

        $this->addChild("liste_sigles", "gt,rh,si,it,sav,in,bss,msan,rva,erp,dr", "array", "liste des sigles (à prendre en compte dans l importation LDAP).");
    }
}
