<?php

namespace ijeffro\Airlines;

use Illuminate\Support\Facades\Facade;

/**
 * AirlinesFacade
 *
 */
class AirlinesFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'airlines'; }

}
