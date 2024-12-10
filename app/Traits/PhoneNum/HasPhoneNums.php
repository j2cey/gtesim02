<?php

namespace App\Traits\PhoneNum;

use App\Models\Status;
use App\Models\Person\PhoneNum;

/**
 * @property PhoneNum $principalPhonenum
 */
trait HasPhoneNums
{
    public function phonenums()
    {
        return $this->morphMany(PhoneNum::class, 'hasphonenum');
    }

    public function principalPhonenum()
    {
        return $this->morphOne(PhoneNum::class, 'hasphonenum')->ofMany('posi', 'min');
    }

    public function latestPhonenum()
    {
        return $this->morphOne(PhoneNum::class, 'hasphonenum')->latest('id');
    }

    public function oldestPhonenum()
    {
        return $this->morphOne(Phonenum::class, 'hasphonenum')->oldest('id');
    }

    public function getPhonenumMaxposiAttribute()
    {
        return $this->phonenums()->count();
    }

    public function addNewPhoneNum($num, $attach_esim = false, $esim_id = null) : ?Phonenum
    {
        // TODO: Valider le numéro de Phone
        if (empty($num)) {
            return null;
        }

        $phonenum = $this->phonenums()->where('phone_number', $num)->first();
        if ($phonenum) {
            return $phonenum;
        }

        $phonenum_count = $this->phonenums()->count();

        $phonenum = $this->phonenums()->create([
            'phone_number' => $num,
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

    public function setPrincipalPhoneNum() {
        $principal_phone_num = $this->principalPhonenum;
        if ( ! is_null($principal_phone_num) ) {
            $this->phone_number = $principal_phone_num->phone_number;
            $this->save();
        }
    }

    public function setPhonenumList() {
        $sep = " - ";
        $phone_number_list = "";

        foreach ($this->phonenums as $phonenum) {
            if ($phone_number_list === "") {
                $phone_number_list = $phonenum->phone_number;
            } else {
                $phone_number_list = $phone_number_list . $sep . $phonenum->phone_number;
            }
            $this->phone_number_list = $phone_number_list;

            $this->save();
        }
    }

    public function switchPhonenumsPosi(PhoneNum $switched_phonenum) {
        $phonenums = $this->phonenums()->orderBy('posi', 'asc')->get();

        foreach ($phonenums as $phonenum) {
            if ($phonenum->id !== $switched_phonenum->id) {
                if ( $this->phonenums()->where('posi', $phonenum->posi)->count() > 1 ) {
                    $phonenum->posi++;
                    $phonenum->save();
                }
            }
        }
    }

    /**
     * Add, dynamically, Eloquent relation (eager loading) to this model
     */
    protected function initializeHasPhoneNums()
    {
        $this->with = array_unique(array_merge($this->with, ['phonenums','latestPhonenum','oldestPhonenum']));
        $this->appends = array_unique(array_merge($this->appends, ['intitule','phonenum_maxposi']));
    }

    public static function bootHasPhoneNums()
    {
        static::deleting(function ($model) {
            $model->removePhonenumsAll();
        });
    }
}
