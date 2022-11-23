<?php

namespace Epmnzava\Userwallet;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Epmnzava\Userwallet\Skeleton\SkeletonClass
 */
class UserwalletFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'userwallet';
    }
}
