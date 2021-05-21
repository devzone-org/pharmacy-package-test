<?php

namespace Devzone\Pharmacy\Facades;

use Illuminate\Support\Facades\Facade;

class Pharmacy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'pharmacy';
    }
}
