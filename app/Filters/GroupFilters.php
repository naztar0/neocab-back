<?php

namespace App\Filters;

class GroupFilters extends QueryFilter
{
    // sorting
    public function ordName($order='asc')
    {
        return $this->builder->orderBy('name', $order);
    }

    // filtering
    public function search($text)
    {
        return $this->builder->where('name', 'like', "%$text%");
    }

    // paginate
    public function page($num)
    {
        $this->pageNum = $num;
    }
    public function perPage($num)
    {
        $this->itemsNum = ($num ?: 1) > 100 ? 100 : $num;
    }
}
