<?php

namespace App\Service;

use Illuminate\Support\Facades\Facade;

class BaseService extends Facade
{
    public function query($where, $orderBy)
    {
        dd($where, $orderBy);
    }
}
