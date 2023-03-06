<?php

namespace App\Filters;

class UserFilters extends QueryFilter
{
    // sorting
    public function ordFirstName($order='asc')
    {
        return $this->builder->orderBy('first_name', $order);
    }

    public function ordLastName($order='asc')
    {
        return $this->builder->orderBy('last_name', $order);
    }

    // filtering
    public function search($text)
    {
        $words = explode(' ', $text);

        if (count($words) == 1) {
            return $this->builder->where('first_name', 'like', "%$text%")
                ->orWhere('last_name', 'like', "%$text%");
        } elseif (count($words) == 2) {
            return $this->builder->where(function ($query) use ($words) {
                $query->where('first_name', 'like', "%$words[0]%")
                    ->where('last_name', 'like', "%$words[1]%");
            })->orWhere(function ($query) use ($words) {
                $query->where('first_name', 'like', "%$words[1]%")
                    ->where('last_name', 'like', "%$words[0]%");
            });
        } else {
            return $this->builder->where(function ($query) use ($words) {
                $query->where('first_name', 'like', "%$words[0]%")
                    ->where('last_name', 'like', "%$words[1]%")
                    ->where('middle_name', 'like', "%$words[2]%");
            })->orWhere(function ($query) use ($words) {
                $query->where('first_name', 'like', "%$words[0]%")
                    ->where('last_name', 'like', "%$words[2]%")
                    ->where('middle_name', 'like', "%$words[1]%");
            })->orWhere(function ($query) use ($words) {
                $query->where('first_name', 'like', "%$words[1]%")
                    ->where('last_name', 'like', "%$words[0]%")
                    ->where('middle_name', 'like', "%$words[2]%");
            })->orWhere(function ($query) use ($words) {
                $query->where('first_name', 'like', "%$words[1]%")
                    ->where('last_name', 'like', "%$words[2]%")
                    ->where('middle_name', 'like', "%$words[0]%");
            })->orWhere(function ($query) use ($words) {
                $query->where('first_name', 'like', "%$words[2]%")
                    ->where('last_name', 'like', "%$words[0]%")
                    ->where('middle_name', 'like', "%$words[1]%");
            })->orWhere(function ($query) use ($words) {
                $query->where('first_name', 'like', "%$words[2]%")
                    ->where('last_name', 'like', "%$words[1]%")
                    ->where('middle_name', 'like', "%$words[0]%");
            });
        }
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
