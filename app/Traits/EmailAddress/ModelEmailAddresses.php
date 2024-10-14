<?php

namespace App\Traits\EmailAddress;


use App\Models\Person\EmailAddress;
use Illuminate\Database\Eloquent\Builder;

trait ModelEmailAddresses
{
    /**
     * @param string|null $searchQuery
     * @param string|null $model_class
     * @param int|null $model_id
     * @return Builder
     */
    public function modelEmailAddressQuery(string $searchQuery = null, string $model_class = null, int $model_id = null): Builder
    {
        $builder = EmailAddress::query()
            ->with(["hasemailaddress","creator"]);
        if ( ! is_null($model_class) ) {
            $builder->where('hasemailaddress_type', $model_class);
        }
        if ( ! is_null($model_id) ) {
            $builder->where('hasemailaddress_id', $model_id);
        }
        $builder->where(function ($query) use ($searchQuery) {
            $query->when($searchQuery, function ($query, $searchQuery) {
                $query->orWhere('email_address', 'like', "%{$searchQuery}%")
                    ->orWhereHas('creator', function ($query) use ($searchQuery) {
                        $query->where( 'name', 'like', '%'.$searchQuery.'%' );
                    })
                ;
            });
        });

        return $builder;
    }
}
