<?php

namespace App\Traits\PhoneNum;

use App\Models\Employes\PhoneNum;
use Illuminate\Database\Eloquent\Builder;

trait ModelPhoneNums
{
    /**
     * @param string $model_class
     * @param int $model_id
     * @param $searchQuery
     * @return Builder
     */
    public function modelPhoneNumQuery(string $model_class, int $model_id, $searchQuery = null)
    {
        return PhoneNum::query()
            ->where('hasphonenum_type', $model_class)
            ->where('hasphonenum_id', $model_id)
            ->where(function ($query) use ($searchQuery) {
                $query->when($searchQuery, function ($query, $searchQuery) {
                    $query->orWhere('numero', 'like', "%{$searchQuery}%")
                        ->orWhereHas('creator', function ($query) use ($searchQuery) {
                            $query->where( 'name', 'like', '%'.$searchQuery.'%' );
                        })
                    ;
                });
            });
    }
}
