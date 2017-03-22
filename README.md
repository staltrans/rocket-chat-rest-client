# Rocket Chat REST API client in PHP

Use this client if you need to connect to Rocket Chat with a software written
in PHP, such as WordPress or Drupal.

## How to use

Create composer.json:
```
{
    "require": {
        "staltrans/rocket-chat-rest-client": "*"
    },
    "repositories":[
        {
            "type": "package",
            "package": {
                "name": "staltrans/rocket-chat-rest-client",
                "version": "0.1",
                "description": "Rocket Chat REST API client in PHP",
                "homepage": "https://github.com/staltrans/rocket-chat-rest-client",
                "require": {
                    "nategood/httpful": "*"
                },
                "source": {
                    "type": "git",
                    "url": "https://github.com/staltrans/rocket-chat-rest-client",
                    "reference": "master"
                },
                "autoload": {
                    "psr-0": {
                        "RocketChat": "src/"
                    }
                }
            }
        }
    ]
}
```

Run

```
$ composer install
```

Include

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Example

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$api = new \RocketChat\RocketChat('https://chat.example.com');
//$api->setUserId('xxxxxxxxxxxxxxxxx');
//$api->setAuthToken('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
var_dump($api->info());
echo "\n=============================\n";
var_dump($api->login('rocket.cat', 'xxxxxxxxxxxxxxxxx'));
echo "\n=============================\n";
var_dump($api->me());
echo "\n=============================\n";
```

## Credits
This REST client uses the excellent [Httpful](http://phphttpclient.com/) PHP library by [Nate Good](https://github.com/nategood) ([github repo is here](https://github.com/nategood/httpful)).
