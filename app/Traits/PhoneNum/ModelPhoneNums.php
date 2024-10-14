<?php

namespace App\Traits\PhoneNum;

use App\Models\Person\PhoneNum;
use Illuminate\Database\Eloquent\Builder;

trait ModelPhoneNums
{
    /**
     * @param string|null $searchQuery
     * @param string|null $model_class
     * @param int|null $model_id
     * @return Builder
     */
    public function modelPhoneNumQuery(string $searchQuery = null, string $model_class = null, int $model_id = null): Builder
    {
        $builder = PhoneNum::query()
            ->with(["hasphonenum","creator","esim"]);
        if ( ! is_null($model_class) ) {
            $builder->where('hasphonenum_type', $model_class);
        }
        if ( ! is_null($model_id) ) {
            $builder->where('hasphonenum_id', $model_id);
        }
        $builder->where(function ($query) use ($searchQuery) {
            $query->when($searchQuery, function ($query, $searchQuery) {
                $query->orWhere('phone_number', 'like', "%{$searchQuery}%")
                    ->orWhereHas('creator', function ($query) use ($searchQuery) {
                        $query->where( 'name', 'like', '%'.$searchQuery.'%' );
                    })
                ;
            });
        });

        return $builder;
    }
}
