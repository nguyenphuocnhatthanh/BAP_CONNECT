<?php
namespace Bap\ConnectPlatform\Contracts;

use Bap\ConnectPlatform\Exceptions\PlatformException;

interface ConnectPlatformInterface
{
    /**
     * @param array $scopes
     * @return array
     */
    public function profile($scopes = []);

    /**
     * @param $uid
     * @param $friendUID
     * @return mixed
     * @throws PlatformException
     */
    public function isFriend($uid, $friendUID);

    /**
     * @param $uid
     * @return array
     */
    public function getFriends($uid);

    /**
     * @param $uid
     * @return array
     * @throws \Exception
     */
    public function getBlockFriends($uid);

    /**
     * @param $uid
     * @return array
     * @throws \Exception
     */
    public function getListIdFriendWaiting($uid);

    /**
     * @param $uid
     * @return array
     * @throws \Exception
     */
    public function getListIdFriendRequest($uid);

    /**
     * @param $uid
     * @param $search
     * @return array
     */
    public function searchFriend($uid, $search);

    /**
     * @param $uid
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function searchTelephone($uid, array $params);

    /**
     * @param $uid
     * @param array $uids
     * @return mixed
     */
    public function isFriends($uid, array $uids);
}
