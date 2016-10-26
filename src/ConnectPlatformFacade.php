<?php
namespace Bap\ConnectPlatform;

use Illuminate\Support\Facades\Facade;

class ConnectPlatformFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'platform';
    }
}