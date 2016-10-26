<?php
namespace Bap\ConnectPlatform\Traits;

trait WithDevices
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(config('platform.devices.model'));
    }
}