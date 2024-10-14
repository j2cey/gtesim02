<?php

namespace App\Traits\EmailAddress;

use App\Models\Status;
use App\Models\Person\EmailAddress;

trait HasEmailAddresses
{
    // TODO: Manage EmailAddress Posi
    /**
     * Renvoie les e-mails (Adresseemail) de ce model.
     */
    public function emailaddresses()
    {
        return $this->morphMany(EmailAddress::class, 'hasemailaddress');
    }

    public function latestEmailAddress()
    {
        return $this->morphOne(EmailAddress::class, 'hasemailaddress')->latest('id');
    }

    public function oldestEmailAddress()
    {
        return $this->morphOne(EmailAddress::class, 'hasemailaddress')->oldest('id');
    }

    public function addNewEmailAddress($email_address) : ?EmailAddress
    {
        // TODO: Valider l'adresse mail
        if (empty($email_address)) {
            return null;
        }

        $adresseemail = $this->emailaddresses()->where('email_address', $email_address)->first();
        if ($adresseemail) {
            return $adresseemail;
        }

        $adresseemail_count = $this->emailaddresses()->count();

        $adresseemail = $this->emailaddresses()->create([
            'email_address' => $email_address,
            'posi' => $adresseemail_count,
            'status_id' => Status::active()->first()->id,
        ]);

        return $adresseemail;
    }

    public function hasThisEmail($email) {
        if ( $this->emailaddresses()->where('email', $email)->count() > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    public function removeEmailAddress($email) {
        $email = $this->emailaddresses()->where('email', $email)->first();
        if ($email) {
            $email->delete();
        }
    }
    public function removeEmailAddressesAll() {
        $this->emailaddresses()->each( function($email) {
            $email->delete();
        });
    }
    public function setEmailAddressList() {
        $sep = " - ";
        $email_address_list = "";

        foreach ($this->emailaddresses as $emailaddress) {
            if ($email_address_list === "") {
                $email_address_list = $emailaddress->email_address;
            } else {
                $email_address_list = $email_address_list . $sep . $emailaddress->email_address;
            }
            $this->email_address_list = $email_address_list;

            $this->save();
        }
    }

    /**
     * Add, dynamically, Eloquent relation (eager loading) to this model
     */
    protected function initializeHasEmailAddresses()
    {
        $this->with = array_unique(array_merge($this->with, ['emailaddresses','latestEmailAddress','oldestEmailAddress']));
        $this->appends = array_unique(array_merge($this->appends, ['intitule']));
    }

    public static function bootHasEmailAddresses()
    {
        static::deleting(function ($model) {
            $model->removeEmailAddressesAll();
        });
    }
}
