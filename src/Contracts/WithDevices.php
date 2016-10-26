<?php
namespace Bap\ConnectPlatform\Contracts;

interface WithDevices
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices();
}