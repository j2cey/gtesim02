<?php

namespace App\Http\Requests\Esim;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\ISearchFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class FetchRequest extends EsimRequest implements ISearchFormRequest
{
    use SearchRequest;

    /**
     * @inheritDoc
     */
    protected function orderByFields() : array
    {
        return ['id', 'imsi'];
    }

    /**
     * @inheritDoc
     */
    protected function defaultOrderByField() : string
    {
        return 'id';
    }

    protected function getCustomPayload()
    {
        $payload = "";
        $payload = $this->addToPayload($payload, 'search', $this->search);
        $payload = $this->addToPayload($payload, 'statutesim', $this->statutesim);

        return $payload;
    }
}
