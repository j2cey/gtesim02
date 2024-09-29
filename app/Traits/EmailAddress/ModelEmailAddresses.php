<?php

namespace App\Traits\EmailAddress;


use App\Models\Employes\EmailAddress;
use Illuminate\Database\Eloquent\Builder;

trait ModelEmailAddresses
{
    /**
     * @param string $model_class
     * @param int $model_id
     * @param $searchQuery
     * @return Builder
     */
    public function modelEmailAddressQuery(string $model_class, int $model_id, $searchQuery = null)
    {
        return EmailAddress::query()
            ->where('hasemailaddress_type', $model_class)
            ->where('hasemailaddress_id', $model_id)
            ->where(function ($query) use ($searchQuery) {
                $query->when($searchQuery, function ($query, $searchQuery) {
                    $query->orWhere('email', 'like', "%{$searchQuery}%")
                        ->orWhereHas('creator', function ($query) use ($searchQuery) {
                            $query->where( 'name', 'like', '%'.$searchQuery.'%' );
                        })
                    ;
                });
            });
    }
}
