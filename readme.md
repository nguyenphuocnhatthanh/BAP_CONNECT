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
Or add `PLATFORM_URL` to `.env`
```
    PLATFORM_URL=my_url
    PLATFORM_GRANT=grant
    PLATFORM_CLIENT_ID=CLIENT_ID
    PLATFORM_CLIENT_SECRET=CLIENT_SECRET
    PLATFORM_SCOPE=SCOPE
```

# Usage

We need to set `access_token` of user before call method


### Get profile
Get user profile from Platform with options `$attributes = ['id', 'username', 'telephone']`

```php
<?php
use ConnectPlatform;
   
ConnectPlatform::make($accessToken)->profile(array $attributes);
```

OR

```php
<?php
app('platform')->make($accessToken)->profile(array $attribuites);
```

### Get list a friend
```php
ConnectPlatform::make($accessToken)->getFriends($uid);
```

### Get list a friend has been block
```php
ConnectPlatform::make($accessToken)->getBlockFriends($uid);
```

### Get list ID friend waiting request
```php
ConnectPlatform::make($accessToken)->getListIdFriendWaiting($uid);
```

### Get list ID friend request 
```php
ConnectPlatform::make($accessToken)->getListIdFriendRequest($uid);
``````

### Search Telephone
```php
ConnectPlatform::make($accessToken)->searchTelephone($uid, array $params);
``````
###### With `$params = ['phone_code' => '', 'telephone' => '']` 

### Check list user is friend
```php
ConnectPlatform::make($accessToken)->isFriends($uid, array $uids)
``````
###### With `$uids` is list `USER ID` of platform

#####With `$uid` is `ID` of platform
