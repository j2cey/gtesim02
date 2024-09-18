<?php

namespace App\Http\Requests\ClientEsim;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\ISearchFormRequest;

class FetchRequest extends ClientEsimRequest implements ISearchFormRequest
{
    use SearchRequest;

    /**
     * @inheritDoc
     */
    protected function orderByFields(): array
    {
        return ['id','nom_raison_sociale'];//return ['nom_raison_sociale', 'email'];
    }

    /**
     * @inheritDoc
     */
    protected function defaultOrderByField(): string
    {
        return 'nom_raison_sociale';
    }

    protected function getCustomPayload()
    {
        return $this->search;
    }
}
