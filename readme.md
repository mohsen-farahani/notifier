## About Notifier

Notifier is a notifications service package which handles the process of sending various push, SMS, email, and other notifications types. It's based on Laravel 5.5 and send all your notifications asynchronously through Laravel Queue Manager on Redis broker.

## Installation

Install the package via Packagist:
```$xslt
composer require asanbarco/notifier
```

## Configuration

Publish the Notifier config file to your application and fill out configurations of the services you want to use: 
```$xslt
php artisan vendor:publish
```

Run the migration command to create notifiers schemas:
```$xslt
php artisan migrate
```

Add the following environments to your application `.env` in comma-separated order in order to set the priority of providers in notify job:
```$xslt
SMS_PROVIDERS_PRIORITY=sms0098,smsir

SMS0098_FROM=YOUR-NUMBER
SMS0098_USERNAME=YOUR-USERNAME
SMS0098_PASSWORD=YOUR-PASSWORD

SMSIR_URI=http://restfulsms.com/api/MessageSend
SMSIR_TOKEN=http://restfulsms.com/api/Token
SMSIR_API_KEY=YOUR-API-KEY
SMSIR_SECRET_KEY=YOUR-SECRET-KEY
SMSIR_LINE_NUMBER=YOUR-NUMBER

PUSH_PROVIDERS_PRIORITY=chabok,onesignal

CHABOK_URI_DEV=https://sandbox.push.adpdigital.com/api/
CHABOK_APP_ID_DEV=YOUR-ID
CHABOK_ACCESS_TOKEN_DEV=YOUR-TOKEN

CHABOK_URI=https://YOUR-ID.push.adpdigital.com/api/
CHABOK_APP_ID=YOUR-ID
CHABOK_ACCESS_TOKEN=YOUR-TOKEN
```

Remember to have the queue artisan command running on the server:
```$xslt
php artisan queue:work
```

Set your application `.env` key `QUEUE_DRIVER` to `redis` to run jobs asynchronously

## How to use

use the `Asanbar\Notifier\Notifier` interface in your application, following methods are available:
`sendPush`, `sendSms`

```PHP
public function sendSMS(array $numbers, string $txt)
{
    Notifier::onQueue('sms'); //if you do not set queue name it run send immediately and return result
    return Notifier::sendSMS($txt, $numbers);
}

public function sendPush(array $tokens, string $title, string $txt, ?array $data = [])
{
    Notifier::onQueue('push');
    return Notifier::sendPush($title, $txt, $tokens, $data);
}

```

Set that a notification read by user

```PHP
Notifier::read($id);
```

## Read data
You can use counts of notfications base on status and type

```PHP
Notifier::getCounts();
Notifier::getCounts($userId); // with user iddentifier (id or mobile ...)
```

get reads or unreads notifications

```PHP
Notifier::getUnReads(); // or getReads()
Notifier::getUnReads($identifier, $type, $limit); // all parameters are optional. If set limit it make paginator
```

## Developer

[Mehrad Aladini](mailto:aladini@asanbar.ir)

refactor by:
[Mehrdad Dadkhah](https://github.com/Mehrdad-Dadkhah)

## License

[AsanBar](https://asanbar.ir).