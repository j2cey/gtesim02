<?php

namespace App\Contracts\Persons;

use App\Models\Person\EmailAddress;

interface IHasEmailAddresses
{
    public function emailaddresses();
    public function latestEmailAddress();
    public function oldestEmailAddress();

    public function addNewEmailAddress($email_address) : ?EmailAddress;
    public function hasThisEmail($email);
    public function removeEmailAddress($email);
    public function removeEmailAddressesAll();

    public function getIntituleAttribute();
}
