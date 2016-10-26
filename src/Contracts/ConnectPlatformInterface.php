<?php
namespace Bap\ConnectPlatform\Contracts;

interface ConnectPlatformInterface
{
    /**
     * @param $accessToken
     * @return $this
     */
    public function make($accessToken);

    /**
     * @param array $scopes
     * @return array
     */
    public function profile($scopes = []);

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
