<?php

namespace App\Contracts\Persons;

use App\Models\Person\PhoneNum;
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

    public function getIntituleAttribute();

    public function switchPhonenumsPosi(PhoneNum $switched_phonenum);
}
