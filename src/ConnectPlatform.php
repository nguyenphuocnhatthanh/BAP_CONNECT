<?php
namespace Bap\ConnectPlatform;


use Bap\ConnectPlatform\Contracts\ConnectPlatformInterface;
use Bap\ConnectPlatform\Exceptions\PlatformAccessTokenInvalid;
use Bap\ConnectPlatform\Exceptions\PlatformException;
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
    public function isFriend($uid, $friendUID)
    {
        $request = $this->get('/api/user/'.$uid.'/friend/'.$friendUID);

        $result = $this->getData($request);

        if (! isset($result->relation)) {
            throw new PlatformException('Server platform error');
        }

        return $result->result;
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
