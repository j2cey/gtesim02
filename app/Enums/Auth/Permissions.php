<?php

namespace App\Enums\Auth;

abstract class Permissions
{
    public static function Status() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions =  ['model-change' => 1];
        return new PermissionAction("status", $customlevels, $additionalactions);
    }
    public static function Setting() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = null;
        return new PermissionAction("setting", $customlevels, $additionalactions);
    }
    public static function Profile() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = null;
        return new PermissionAction("profile", $customlevels, $additionalactions);
    }

    public static function User() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = null;
        return new PermissionAction("user", $customlevels, $additionalactions);
    }
    public static function Role() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = null;
        return new PermissionAction("role", $customlevels, $additionalactions);
    }
    public static function Permission() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = null;
        return new PermissionAction("permission", $customlevels, $additionalactions);
    }
    public static function StatutEsim() : PermissionAction {
        $additionalactions = null;
        return new PermissionAction("statutesim");
    }
    public static function TechnologieEsim() : PermissionAction {
        $additionalactions = null;
        return new PermissionAction("technologieesim");
    }
    public static function ClientEsim() : PermissionAction {
        $additionalactions = [
            'show' => 4,
            'previewpdf' => 4,'print' => 4,
            'esim-attach' => 3,'esim-dettach' => 3,
            'phonenum-add' => 3,'phonenum-delete' => 2,'phonenum-update' => 2,
            'emailaddress-add' => 3,'emailaddress-update' => 2,'emailaddress-delete' => 2,
            'creator-list' => 2,'creator-department-list' => 2
        ];
        return new PermissionAction("clientesim", null, $additionalactions);
    }
    public static function Esim() : PermissionAction {
        $additionalactions = [
            'attach' => 3,'import' => 3,
            'attributor-list' => 2,'attributor-department-list' => 2,
            'phonenum-update' => 2,'phonenum-delete' => 2
        ];
        return new PermissionAction("esim", null, $additionalactions);
    }
    public static function EsimState() : PermissionAction {
        $additionalactions = null;
        return new PermissionAction("esimstate");
    }
    public static function EsimQrcode() : PermissionAction {
        $additionalactions = null;
        return new PermissionAction("esimqrcode");
    }
    public static function PhoneNum() : PermissionAction {
        $additionalactions = ['esim-recycle' => 2];
        return new PermissionAction("phonenum", null, $additionalactions);
    }
    public static function EmailAddress() : PermissionAction {
        $additionalactions = null;
        return new PermissionAction("emailaddress");
    }
    public static function Howto() : PermissionAction {
        $additionalactions = ['update-code' => 1,'edithtml' => 1];
        return new PermissionAction("howto", null, $additionalactions);
    }
    public static function HowtoThread() : PermissionAction {
        $additionalactions = ['update-code' => 1];
        return new PermissionAction("howtothread", null, $additionalactions);
    }

}
