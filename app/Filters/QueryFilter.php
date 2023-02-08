<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected Builder $builder;
    public int $pageNum = 1;
    public int $itemsNum = 10;

    public function __construct(protected Request $request)
    {
    }

    public function apply(string $model)
    {
        $this->builder = $model::query();
        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], [$value]);
            }
        }
        return $this->builder;
    }

    public function filters()
    {
        return $this->request->query();
    }
}
