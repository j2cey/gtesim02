<?php

namespace App\Enums\Auth;

abstract class Permissions
{
    public static function Status() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions =  ['change' => 1];
        return new PermissionAction("statuses", $customlevels, $additionalactions);
    }
    public static function Setting() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = null;
        return new PermissionAction("settings", $customlevels, $additionalactions);
    }
    public static function Profile() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = null;
        return new PermissionAction("profile", $customlevels, $additionalactions);
    }
    public static function Dashboard() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions =  ['agence-show' => 3];
        return new PermissionAction("dashboards", $customlevels, $additionalactions);
    }
    public static function LdapUser() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions =  ['integrate' => 1];
        return new PermissionAction("ldapusers", $customlevels, $additionalactions);
    }

    public static function User() : BaseModelAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = [
            'reset-password' => 1, 'accountinfos-sendmail' => 2
        ];
        return new BaseModelAction("users", $customlevels, $additionalactions);
    }
    public static function Role() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = [
            'assign' => 1,
            'revoke' => 1,
        ];
        return new PermissionAction("roles", $customlevels, $additionalactions);
    }
    public static function Permission() : PermissionAction {
        $customlevels = ['create' => 1,'update' => 1,'delete' => 1];
        $additionalactions = null;
        return new PermissionAction("permissions", $customlevels, $additionalactions);
    }
    public static function StatutEsim() : BaseModelAction {
        $additionalactions = null;
        return new BaseModelAction("statutesims");
    }
    public static function TechnologieEsim() : BaseModelAction {
        $additionalactions = null;
        return new BaseModelAction("technologieesims");
    }
    public static function ClientEsim() : BasePersonAction {
        $additionalactions = [
            'previewpdf' => 4,'print' => 4,
            'esim-attach' => 3,'esim-dettach' => 3,
            'creator-department-list' => 2
        ];
        return new BasePersonAction("clientesims", null, $additionalactions);
    }
    public static function Esim() : BaseModelAction {
        $additionalactions = [
            'attach' => 3,'import' => 3,
            'attributor-list' => 2,'attributor-department-list' => 2,
            'phonenum-update' => 2,'phonenum-delete' => 2
        ];
        return new BaseModelAction("esims", null, $additionalactions);
    }
    public static function EsimState() : BaseModelAction {
        $additionalactions = null;
        return new BaseModelAction("esimstates");
    }
    public static function EsimQrcode() : BaseModelAction {
        $additionalactions = null;
        return new BaseModelAction("esimqrcodes");
    }

    public static function ArisStatus() : BaseModelAction {
        $additionalactions = null;
        return new BaseModelAction("arisstatuses");
    }
    public static function PhoneNum() : BaseModelAction {
        $additionalactions = ['esim-recycle' => 2, 'esim-sendmail' => 2];
        return new BaseModelAction("phonenums", null, $additionalactions);
    }
    public static function EmailAddress() : BaseModelAction {
        $additionalactions = null;
        return new BaseModelAction("emailaddresses");
    }
    public static function Employe() : BasePersonAction {
        $additionalactions = [
            'creator-department-list' => 2
        ];
        return new BasePersonAction("employes", null, $additionalactions);
    }
    public static function Howto() : BaseModelAction {
        $additionalactions = ['update-code' => 1,'edithtml' => 1,'readhtml' => 2];
        return new BaseModelAction("howtos", null, $additionalactions);
    }
    public static function HowtoThread() : BaseModelAction {
        $additionalactions = ['update-code' => 1,'add-step' => 1,'read' => 1];
        return new BaseModelAction("howtothreads", null, $additionalactions);
    }
    public static function HowtoStep() : BaseModelAction {
        $additionalactions = ['update-code' => 1];
        return new BaseModelAction("howtosteps", null, $additionalactions);
    }

    public static function Creator() : PermissionAction {
        $customlevels = null;
        $additionalactions =  null;
        return new PermissionAction("creators", $customlevels, $additionalactions);
    }
    public static function Updator() : PermissionAction {
        $customlevels = null;
        $additionalactions =  null;
        return new PermissionAction("updators", $customlevels, $additionalactions);
    }

}
