<?php


namespace App\Contracts\Esims;


use App\Models\Esims\Esim;
use OwenIt\Auditing\Contracts\Auditable;

interface IHasEsim extends Auditable
{
    public function esim();
    public function attachEsim($esim_id);
    public function changeEsim($esim_id);
    public function dettachEsim();
}
