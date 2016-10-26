<?php
namespace Bap\ConnectPlatform\Contracts;

interface WithDevicesInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices();
}