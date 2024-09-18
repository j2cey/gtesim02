<?php

namespace App\Http\Requests\HowToThread;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\ISearchFormRequest;

class FetchRequest extends HowToThreadRequest implements ISearchFormRequest
{
    use SearchRequest;

    /**
     * @inheritDoc
     */
    protected function orderByFields(): array
    {
        return ['id','title'];
    }

    /**
     * @inheritDoc
     */
    protected function defaultOrderByField(): string
    {
        return 'title';
    }

    protected function getCustomPayload()
    {
        return $this->search;
    }
}
