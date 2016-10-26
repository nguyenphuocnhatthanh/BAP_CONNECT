<?php
namespace Bap\ConnectPlatform;

use Bap\ConnectPlatform\Exceptions\InvalidDeviceException;

class AccessToken
{
    /**
     * @return mixed
     */
    private function user()
    {
        $userId = app('tymon.jwt.auth')->getPayload()->get('sub');

        return app('cache')->get(sprintf('users.%s', $userId)) ?: app('tymon.jwt.auth')->authenticate();
    }

    /**
     * @param null $currentDevice
     * @return mixed
     * @throws InvalidDeviceException
     */
    private function getCurrentDevice($currentDevice = null)
    {
        $currentDevice = $currentDevice ?: app('tymon.jwt.auth')->getPayload()->get('device');

        $user = $this->user()->load(['devices' => function($q) use($currentDevice) {
            $q->where('device', $currentDevice)->first();
        }]);

        if (! $devices = $user->devices->first()) {
            throw new InvalidDeviceException('Device is invalid');
        }

        return $devices;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        if (function_exists('get_access_token_platform')) {
            return get_access_token_platform($this->user());
        }

        return $this->getCurrentDevice()->access_token_platform;
    }
}