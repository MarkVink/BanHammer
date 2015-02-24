<?php

namespace KikFinder\BanHammer\Facades;

use Illuminate\Support\Facades\Facade;

class BanHammer extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'banhammer.hammer';
    }
}