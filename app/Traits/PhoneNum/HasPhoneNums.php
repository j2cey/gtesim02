<?php

namespace App\Traits\PhoneNum;

use App\Models\Status;
use App\Models\Employes\PhoneNum;

trait HasPhoneNums
{
    public function phonenums()
    {
        return $this->morphMany(PhoneNum::class, 'hasphonenum');
    }

    public function latestPhonenum()
    {
        return $this->morphOne(PhoneNum::class, 'hasphonenum')->latest('id');
    }

    public function oldestPhonenum()
    {
        return $this->morphOne(Phonenum::class, 'hasphonenum')->oldest('id');
    }

    public function addNewPhoneNum($num, $attach_esim = false, $esim_id = null) : ?Phonenum
    {
        // TODO: Valider le numéro de Phone
        if (empty($num)) {
            return null;
        }

        $phonenum = $this->phonenums()->where('numero', $num)->first();
        if ($phonenum) {
            return $phonenum;
        }

        $phonenum_count = $this->phonenums()->count();

        $phonenum = $this->phonenums()->create([
            'numero' => $num,
            'posi' => $phonenum_count,
            'status_id' => Status::active()->first()->id,
        ]);

        if ($attach_esim) {
            $phonenum->attachEsim($esim_id);
        }

        return $phonenum;
    }

    public function hasThisPhone($num) {
        if ( $this->phonenums()->where('numero', $num)->count() > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    public function removePhonenum($num) {
        $rresult = null;

        $phonenum = $this->phonenums()->where('numero', $num)->first();
        if ($phonenum) {
            $rresult = $phonenum->delete();
        }
        return $rresult;
    }
    public function removePhonenumsAll() {
        $this->phonenums()->each( function($phonenum) {
            $phonenum->delete();
        });
    }

    /**
     * Add, dynamically, Eloquent relation (eager loading) to this model
     */
    protected function initializeHasPhoneNums()
    {
        $this->with = array_unique(array_merge($this->with, ['phonenums','latestPhonenum','oldestPhonenum']));
    }

    public static function bootHasPhoneNums()
    {
        static::deleting(function ($model) {
            $model->removePhonenumsAll();
        });
    }
}
