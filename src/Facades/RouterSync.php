<?php

namespace Luqta\RouterSync\Facades;

use Illuminate\Support\Facades\Facade;

class RouterSync extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'routersync';
    }
}
