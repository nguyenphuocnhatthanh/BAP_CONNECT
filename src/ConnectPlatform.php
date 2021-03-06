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
    private $actionHistory = ['request', 'payment', 'withdraw'];

    /**
     * @var array
     */
    private $actionExchange = ['money_coin', 'coin_money'];

    /**
     * @var array
     */
    private $statusSuccess = [200, 201];

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
     * @param $friendUid
     * @return array
     */
    public function sendFriendRequest($uid, $friendUid)
    {
        $request = $this->post('/api/user/'. $uid. '/friend/' .$friendUid. '/request');

        return $this->getData($request);
    }

    /**
     * @param $uid
     * @param $friendUid
     * @return array
     */
    public function approveFriendRequest($uid, $friendUid)
    {
        $request = $this->put('/api/user/'. $uid. '/friend/' .$friendUid. '/approve');

        return $this->getData($request);
    }
    
    /**
     * @param $uid
     * @return array
     */
    public function getCoin($uid)
    {
        $request = $this->get('/api/coin/' . $uid);

        return $this->getDataOrThrowException($request);
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

        if (! isset($result->result)) {
            throw new PlatformException('Server platform error');
        }

        return $result->result;
    }

    /**
     * @param $uid
     * @param $coin
     * @return array
     * @throws PlatformException
     */
    public function requestCoin($uid, $coin)
    {
        $request = $this->post('/api/coin/'. $uid .'/request', [
            'json'  => [
                'client_id' => config('platform.client_id'),
                'value'     => $coin
            ]
        ]);

        return $this->getDataOrThrowException($request);
    }

    /**
     * @param $uid
     * @param $money
     * @return array
     * @throws PlatformException
     */
    public function withDrawMoney($uid, $money)
    {
        $request = $this->post('/api/coin/'. $uid .'/withdraw', [
            'json'  => [
                'client_id' => config('platform.client_id'),
                'value'     => $money
            ]
        ]);

        return $this->getDataOrThrowException($request);
    }

    /**
     * @param $uid
     * @param array $params
     * @return array
     * @throws PlatformException
     * @throws PlatformParamsException
     */
    public function requestPayment($uid, array $params)
    {
        $request = $this->post('/api/coin/'. $uid .'/payment', [
            'json' => [
                'item_id'       => ! empty($params['item_id']) ? $params['item_id'] : null,
                'item_value'    => ! empty($params['item_value']) ? $params['item_value'] : null,
                'item_cat_id'   => ! empty($params['item_cat_id']) ? $params['item_cat_id'] : null,
                'client_id'     => config('platform.client_id'),
                'token'         => $this->getPaymentToken($uid)
            ]
        ]);

        return $this->getDataOrThrowException($request);
    }

    /**
     * Get payment token
     *
     * @param $uid
     * @return
     * @throws PlatformException
     */
    private function getPaymentToken($uid)
    {
        $currentTime = time();
        $dataHash = sprintf(
            '%s.%s.payment_token.%s',
            config('platform.client_id'),
            config('platform.client_secret'),
            $currentTime
        );

        $request = $this->post('/api/coin/'. $uid .'/payment/token', [
            'json'  => [
                'hash'      => base64_encode(hash_hmac('SHA256', $dataHash, 'payment_token')),
                'type'      => 'payment_token',
                'callback'  => config('platform.url_callback'),
                'time'      => $currentTime
            ]
        ]);
        $data = $this->getDataOrThrowException($request);

        return $data->token;
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
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function put($url, $options = [])
    {
        $options = array_merge($this->setAuth(), $options);

        return $this->client->put($this->site.$url, $options);
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
     * @return mixed
     * @throws PlatformException
     */
    private function getDataOrThrowException($request)
    {
        $data = json_decode($this->getResponse($request));

        if (is_null($data)) {
            throw new PlatformException('Server platform error');
        }

        if (! in_array($data->status, $this->statusSuccess)) {
            throw new PlatformException($data->message, $data->status);
        }

        return $data->data;
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

        if (is_null($data)) {
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
