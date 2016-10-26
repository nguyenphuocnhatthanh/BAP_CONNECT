<?php
namespace Bap\ConnectPlatform\Traits;

class WithDevices
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(config('platform.devices.model'));
    }
}