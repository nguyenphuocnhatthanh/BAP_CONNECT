#Connect Platform for BAP Service
### Install
Add the following line to composer.json file and run `composer update`
```json
{
      "require": {
        "bap/connect": "1.*"
      } 
}
```
Or install with CLI:
```
    composer require bap/connect
```

Open up `config/app.php` and add the following to the `provires` key.
```
    Bap\ConnectPlatform\ConnectPlatformServiceProvide::class
```
You can register the ConnectPlatform Facade `aliases` key with:
```
     'ConnectPlatform'  => Bap\ConnectPlatform\ConnectPlatformFacade::class
```

# Configuration
You'll need to publish all vendor assets:
```
    php artisan vendor:publish --provider="Bap\ConnectPlatform\ConnectPlatformServiceProvide"

```
And also run migrations
```
    php artisan migrate
```

And add `PLATFORM_URL` to `.env`
```
    PLATFORM_URL=my_url
    PLATFORM_GRANT=grant
    PLATFORM_CLIENT_ID=CLIENT_ID
    PLATFORM_CLIENT_SECRET=CLIENT_SECRET
    PLATFORM_SCOPE=SCOPE
```

#### Add relation devices and contract
```php
...
use Bap\ConnectPlatform\Traits\WithDevices;
use Bap\ConnectPlatform\Contracts\WithDevicesInterface;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract,
                                    WithDevicesInterface
{
    use Authenticatable,
        Authorizable,
        CanResetPassword,
        RelationDevices;
```

# Usage
* Add middleware jwt auth in `construsctor` method Controller:
```php
    public function __constructor()
    {
        $this->middleware('jwt.auth');
    }
```

* Or app/Http/routes.php
```php
    Route::post('me', ['before' => 'jwt-auth', function() {
        // Todo
    }]);
```

* Add `device` to options claims for jwt

## Profile
### Get profile
Get user profile from Platform with options `$attributes = ['id', 'username', 'telephone']`

```php
<?php
use ConnectPlatform;
   
ConnectPlatform::profile(array $attributes);
```

OR

```php
<?php
app('platform')->profile(array $attribuites);
```

## Friend
### Get list a friend
```php
ConnectPlatform::getFriends($uid);
```

### Get list a friend has been block
```php
ConnectPlatform::getBlockFriends($uid);
```

### Get list ID friend waiting request
```php
ConnectPlatform::getListIdFriendWaiting($uid);
```

### Get list ID friend request 
```php
ConnectPlatform::getListIdFriendRequest($uid);
``````

### Get relation 
```php
ConnectPlatform::getRelation($uid, $friendUID)
``````

### Check list user is friend
```php
ConnectPlatform::isFriends($uid, array $uids)
``````

### Check relation list friend
```php
ConnectPlatform::checkListFriends($uid, array $uids)
``````

###### With `$uids` is list `USER ID` of platform

## Search

### Search Telephone
```php
ConnectPlatform::searchTelephone($uid, array $params);
``````
###### With `$params = ['phone_code' => '', 'telephone' => '']` 

## Coin

### Get asset
```php
ConnectPlatform::getCoin($uid);
``````

### Get History
```php
ConnectPlatform::getHistoryCoin($uid, $action);
``````
With `$action in array ['request', 'payment', 'withdraw']`  

### Exchange
```php
ConnectPlatform::exchange($uid, $action, array $params);
``````
With `$action in array ['money_coin', 'coin_money']` and `$params = ['src' => '' , 'des' => '']`

### Request
```php
ConnectPlatform::requestCoin($uid, $coin);
``````
### Withdraw money
```php
ConnectPlatform::withDrawMoney($uid, $money);
``````
### Get token payment
```php
ConnectPlatform::getPaymentToken($uid);
``````
### Send payment
```php
ConnectPlatform::requestPayment($uid, array $params);
``````
With `$params = ['item_id' => '' , 'cat_id' => '', 'item_value' => '', 'token' => '']`

#####With `$uid` is `ID` of platform


#Config file
You can change model, list devices, timeout request at config file.
