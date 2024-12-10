<?php

namespace App\Traits\EmailAddress;

use App\Models\Status;
use App\Models\Person\EmailAddress;

/**
 * @property-read \App\Models\Person\EmailAddress|null $firstEmailAddress
 * @property-read \App\Models\Person\EmailAddress|null $latestEmailAddress
 */
trait HasEmailAddresses
{
    /**
     * Renvoie les e-mails (Adresseemail) de ce model.
     */
    public function emailaddresses()
    {
        return $this->morphMany(EmailAddress::class, 'hasemailaddress');
    }

    public function firstEmailAddress()
    {
        return $this->morphOne(EmailAddress::class, 'hasemailaddress')->ofMany('posi', 'min');
    }
    public function latestEmailAddress()
    {
        return $this->morphOne(EmailAddress::class, 'hasemailaddress')->latest('id');
    }

    public function oldestEmailAddress()
    {
        return $this->morphOne(EmailAddress::class, 'hasemailaddress')->oldest('id');
    }

    public function getEmailaddressMaxposiAttribute()
    {
        return $this->emailaddresses()->count();
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

    public function switchEmailaddressesPosi(EmailAddress $switched_emailaddress) {
        $emailaddresses = $this->emailaddresses()->orderBy('posi', 'asc')->get();

        foreach ($emailaddresses as $emailaddress) {
            if ($emailaddress->id !== $switched_emailaddress->id) {
                if ( $this->emailaddresses()->where('posi', $emailaddress->posi)->count() > 1 ) {
                    $emailaddress->posi++;
                    $emailaddress->save();
                }
            }
        }
    }

    /**
     * Add, dynamically, Eloquent relation (eager loading) to this model
     */
    protected function initializeHasEmailAddresses()
    {
        $this->with = array_unique(array_merge($this->with, ['emailaddresses','latestEmailAddress','oldestEmailAddress']));
        $this->appends = array_unique(array_merge($this->appends, ['intitule','emailaddress_maxposi']));
    }

    public static function bootHasEmailAddresses()
    {
        static::deleting(function ($model) {
            $model->removeEmailAddressesAll();
        });
    }
}
