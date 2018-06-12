## About Notifier

Notifier is a notifications service package which handles the process of sending various push, SMS, email, and other notifications types. It's based on Laravel 5.6 and send all your notifications asynchronously through Laravel Queue Manager on Redis broker.

## Installation

Install the package via Packagist:
```$xslt
composer rerquire asanbar/notifier
```

## Configuration

Publish the Notifier config file to your application and fill out configurations of the services you want to use: 
```$xslt
php artisan vendor:publish
```

Add the following environments to your application `.env` in comma-separated order in order to set the priority of providers in notify job:
```$xslt
SMS_PROVIDERS_PRIORITY=sms0098,smsir
PUSH_PROVIDERS_PRIORITY=onesignal
```

Remember to have the queue artisan command running on the server:
```$xslt
php artisan queue:work
```

Set your application `.env` key `QUEUE_DRIVER` to `redis` to run jobs asynchronously

## How to use

use the `Asanbar\Notifier\Notifier` interface in your application, following methods are available:
`sendPush`, `sendSms`


## Developer

[Mehrad Aladini](mailto:aladini@asanbar.ir)

## License

[AsanBar](https://asanbar.ir).
