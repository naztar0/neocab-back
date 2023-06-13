<?php

namespace App\Filters;

class AttendanceFilters extends QueryFilter
{
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
