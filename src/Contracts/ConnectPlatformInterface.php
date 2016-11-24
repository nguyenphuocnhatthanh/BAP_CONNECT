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
    public function getRelation($uid, $friendUID);

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

    /**
     * Get current coin
     *
     * @param $uid
     * @return array
     */
    public function getCoin($uid);

    /**
     * Get history transaction coin
     *
     * @param $uid
     * @param $action
     * @return array
     * @throws \Bap\ConnectPlatform\Exceptions\PlatformActionHistoryException
     * @throws PlatformException
     */
    public function getHistoryCoin($uid, $action);

    /**
     * Exchange money <-> coin
     *
     * @param $uid
     * @param $action
     * @param array $params
     * @return mixed
     * @throws \Bap\ConnectPlatform\Exceptions\PlatformActionHistoryException
     * @throws PlatformException
     * @throws \Bap\ConnectPlatform\Exceptions\PlatformParamsException
     */
    public function exchange($uid, $action, array $params);

    /**
     * Send request coin
     *
     * @param $uid
     * @param $coin
     * @return array
     */
    public function requestCoin($uid, $coin);

    /**
     * Send request withdraw money
     *
     * @param $uid
     * @param $money
     * @return array
     */
    public function withDrawMoney($uid, $money);

    /**
     * Send payment
     *
     * @param $uid
     * @param array $params
     * @return array
     */
    public function requestPayment($uid, array $params);
}
