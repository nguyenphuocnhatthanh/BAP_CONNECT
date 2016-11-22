<?php
namespace Bap\ConnectPlatform;


use Bap\ConnectPlatform\Contracts\ConnectPlatformInterface;
use Bap\ConnectPlatform\Exceptions\PlatformAccessTokenInvalid;
use Bap\ConnectPlatform\Exceptions\PlatformActionHistoryException;
use Bap\ConnectPlatform\Exceptions\PlatformException;
use Bap\ConnectPlatform\Exceptions\PlatformParamsException;
use GuzzleHttp\Client;

class ConnectPlatform implements ConnectPlatformInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var
     */
    private $site;

    /**
     * @var array
     */
    private $actionHistory = ['request', 'payment', 'withraw'];

    /**
     * @var array
     */
    private $actionExchange = ['money_coin', 'coin_money'];

    /**
     * Platform constructor.
     * @param AccessToken $accessToken
     * @param $site
     */
    public function __construct(AccessToken $accessToken, $site)
    {
        $this->client = new Client();
        $this->site = $site;
        $this->accessToken = $accessToken;
    }

    /**
     * @param array $scopes
     * @return array
     */
    public function profile($scopes = [])
    {
        $request = $this->get('/api/user/me');

        return $this->getData($request, $scopes);
    }


    /**
     * @param $uid
     * @return mixed
     * @throws \Exception
     */
    public function getFriends($uid)
    {
        $request = $this->get('/api/user/'.$uid.'/friend');

        $result = $this->getData($request);

        if (! isset($result->list_friends) || ! isset($result->total)) {
            throw new PlatformException('Server platform error');
        }

        return $result;
    }

    /**
     * @param $uid
     * @return array
     * @throws \Exception
     */
    public function getBlockFriends($uid)
    {
        $request = $this->get('/api/user/'.$uid.'/friend/block');

        $result = $this->getData($request);

        if (! isset($result->list_friends) || ! isset($result->total)) {
            throw new PlatformException('Server platform error');
        }

        return $result;
    }

    /**
     * @param $uid
     * @return array
     * @throws \Exception
     */
    public function getListIdFriendWaiting($uid)
    {
        $request = $this->get('/api/user/'.$uid.'/friend/waiting');
        $result = $this->getData($request);

        if (! isset($result->list_friends) || ! isset($result->total)) {
            throw new PlatformException('Server platform error');
        }

        return $result;
    }

    /**
     * @param $uid
     * @return array
     * @throws \Exception
     */
    public function getListIdFriendRequest($uid)
    {
        $request = $this->get('/api/user/'.$uid.'/friend/request');
        $result = $this->getData($request);

        if (! isset($result->list_friends) || ! isset($result->total)) {
            throw new PlatformException('Server platform error');
        }

        return $result;
    }

    /**
     * @param $uid
     * @param $search
     * @return array
     * @throws \Exception
     */
    public function searchFriend($uid, $search)
    {
        $request = $this->get('/api/user/'.$uid.'/friend/search?key='.$search);

        $result = $this->getData($request);

        if (! isset($result->list_friends) || ! isset($result->total)) {
            throw new PlatformException('Server platform error');
        }

        return $result;
    }

    /**
     * @param $uid
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function searchTelephone($uid, array $params)
    {
        $request = $this->post('/api/user/'.$uid.'/search/phone', [
            'json'  => $params
        ]);

        $result = $this->getData($request);

        if (! isset($result->relation)) {
            throw new PlatformException('Server platform error');
        }

        return $result;
    }

    /**
     * @param $uid
     * @param $friendUID
     * @return mixed
     * @throws PlatformException
     */
    public function getRelation($uid, $friendUID)
    {
        $request = $this->get('/api/user/'.$uid.'/friend/'.$friendUID);

        $result = $this->getData($request);

        if (! isset($result->relation)) {
            throw new PlatformException('Server platform error');
        }

        return $result->relation;
    }

    /**
     * @param $uid
     * @param array $uids
     * @return mixed
     * @throws \Exception
     */
    public function isFriends($uid, array $uids)
    {
        $request = $this->post('/api/user/'.$uid.'/friend/check', [
            'json'  => ['list_friends' => $uids]
        ]);

        $result = $this->getData($request);

        if (! isset($result->result)) {
            throw new PlatformException('Server platform error');
        }

        return $result->result;
    }

    /**
     * Check list friend detail
     *
     * @param $uid
     * @param array $uids
     * @return mixed
     * @throws PlatformException
     */
    public function checkListFriends($uid, array $uids)
    {
        $request = $this->post('/api/user/'.$uid.'/friend/check_list', [
            'json'  => ['list_friends' => $uids]
        ]);

        return $this->getData($request);
    }

    /**
     * @param $uid
     * @return array
     */
    public function getCoin($uid)
    {
        $request = $this->get('/api/coin/' . $uid);

        return $this->getData($request);
    }

    /**
     * @param $uid
     * @param $action
     * @return array
     * @throws PlatformActionHistoryException
     * @throws PlatformException
     */
    public function getHistoryCoin($uid, $action)
    {
        if (! in_array($action, $this->actionHistory)) {
            throw new PlatformActionHistoryException('Action History is invalid');
        }

        $request = $this->get('/api/coin/' . $uid. '/history/'. $action);
        $result = $this->getData($request);

        if (! isset($result->total, $result->items)) {
            throw new PlatformException('Server platform error');
        }

        return $result;
    }

    /**
     * @param $uid
     * @param $action
     * @param array $params
     * @return mixed
     * @throws PlatformActionHistoryException
     * @throws PlatformException
     * @throws PlatformParamsException
     */
    public function exchange($uid, $action, array $params)
    {
        if (! in_array($action, $this->actionExchange)) {
            throw new PlatformActionHistoryException('Exchange Action is invalid');
        }
        if (empty($params['src']) || empty($params['des'])) {
            throw new PlatformParamsException('Missing param `srs` or `des`');
        }

        $request = $this->post('/api/coin/'. $uid .'/exchange/'.$action, [
            'json' => [
                'src' => $params['src'],
                'des' => $params['des'],
            ]
        ]);
        $result = $this->getData($request);

        if (! isset($result->resut)) {
            throw new PlatformException('Server platform error');
        }

        return $result->result;
    }

    /**
     * @param $uid
     * @param $coin
     * @return array
     */
    public function requestCoin($uid, $coin)
    {
        $request = $this->post('/api/coin/'. $uid .'/request', [
            'json'  => [
                'client_id' => config('platform.client_id'),
                'value'     => $coin
            ]
        ]);

        return $this->getData($request);
    }

    /**
     * @param $uid
     * @param $money
     * @return array
     */
    public function withRawMoney($uid, $money)
    {
        $request = $this->post('/api/coin/'. $uid .'/withdraw', [
            'json'  => [
                'client_id' => config('platform.client_id'),
                'value'     => $money
            ]
        ]);

        return $this->getData($request);
    }

    /**
     * @param $uid
     */
    public function getPaymentToken($uid)
    {
        $currentTime = time();
        $dataHash = config('platform.client_id'). config('platform.client_secret') . $currentTime;

        $this->post('/api/coin/'. $uid .'/payment/token', [
            'json'  => [
                'hash'      => base64_encode(hash_hmac('SHA256', $dataHash, 'payment_token')),
                'type'      => 'payment_token',
                'callback'  => config('platform.url_callback'),
                'time'      => $currentTime
            ]
        ]);
    }

    /**
     * @param $uid
     * @param array $params
     * @return array
     * @throws PlatformParamsException
     */
    public function requestPayment($uid, array $params)
    {
        if (empty($params['item_id']) || $params['item_value'] || $params['token']) {
            throw new PlatformParamsException('Missing param `item_id` or `item_value` or `token`');
        }

        $request = $this->post('/api/coin/'. $uid .'/payment', [
            'json' => [
                'item_id'    => $params['item_id'],
                'item_value' => $params['item_value'],
                'client_id'  => config('platform.client_id'),
                'token'      => $params['token'],
            ]
        ]);

        return $this->getData($request);
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function get($url, $options = [])
    {
        $options = array_merge($this->setAuth(), $options);

        return $this->client->get($this->site.$url, $options);
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function post($url, $options = [])
    {
        $options = array_merge($this->setAuth(), $options);

        return $this->client->post($this->site.$url, $options);
    }

    /**
     * @return array
     * @throws PlatformAccessTokenInvalid
     */
    private function setAuth()
    {
        if (! $this->accessToken) {
            throw new PlatformAccessTokenInvalid('Access Token is required');
        }

        return [
            'headers' => [
                'Authorization' => 'Bearer '.$this->accessToken->get()
            ],
            'timeout'   => config('platform.request.timeout')
        ];
    }

    /**
     * @param $request
     * @return mixed
     */
    private function getResponse($request)
    {
        return $request->getBody()->getContents();
    }

    /**
     * @param $request
     * @param $scopes
     * @return array
     * @throws \Exception
     */
    private function getData($request, $scopes = [])
    {
        $data = json_decode($this->getResponse($request));

        if (is_null($data) || 200 !== $data->status) {
            throw new PlatformException('Server platform error');
        }
        if ($scopes) {
            $result = [];
            foreach ($scopes as $scope) {
                $result[$scope] = $data->data->{$scope};
            }

            return $result;
        }

        return  $data->data;
    }
}
