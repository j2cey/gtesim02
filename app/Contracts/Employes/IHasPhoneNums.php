<?php

namespace App\Contracts\Employes;

use App\Models\Employes\PhoneNum;
use OwenIt\Auditing\Contracts\Auditable;

interface IHasPhoneNums extends Auditable
{
    public function phonenums();
    public function latestPhonenum(); 
    public function oldestPhonenum();

    public function addNewPhoneNum($num, $attach_esim = false, $esim_id = null) : ?Phonenum;
    public function hasThisPhone($num);
    public function removePhonenum($num);
    public function removePhonenumsAll();
    
}
