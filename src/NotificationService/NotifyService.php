<?php

namespace Asanbar\Notifier\NotificationService;

use Asanbar\Notifier\NotificationService\Context;
use Asanbar\Notifier\NotificationService\Strategies\MessageStrategy;
use Asanbar\Notifier\NotificationService\Strategies\PushStrategy;
use Asanbar\Notifier\NotificationService\Strategies\SMSStrategy;
use Carbon\Carbon;

class NotifyService
{
    /** @var string provider name */
    private $providerName = null;

    /** @var string title  */
    private $title;

    /** @var string body */
    private $body;

    /** @var string[] receivers */
    private $receivers;

    /** @var mixed[] extra data */
    private $extra;

    /** @var Carbon expire date time */
    private $expireAt;

    /**
     * strategy decision function
     *
     * @param string $type
     * @return void
     */
    public function sendNotification(string $type)
    {
        switch (strtolower($type)) {
            case 'sms':
                $strategy = new SMSStrategy();
                $strategy->receivers($this->receivers);
                $strategy->setBody($this->body);

                return (new Context($strategy))->executeSendStrategy();
            case 'push':
                $strategy = new PushStrategy();
                $strategy->receivers($this->receivers);
                $strategy->setTitle($this->title);
                $strategy->setBody($this->body);
                $strategy->setExpireAt($this->expireAt);

                return (new Context($strategy))->executeSendStrategy();
            case 'message':
                $strategy = new MessageStrategy();
                $strategy->receivers($this->receivers);
                $strategy->setTitle($this->title);
                $strategy->setBody($this->body);
                $strategy->setExpireAt($this->expireAt);

                return (new Context($strategy))->executeSendStrategy();

        }
    }

    /**
     * strategy decision function
     *
     * @param string $type
     * @return void
     */
    public function receiveNotification(string $type)
    {
        switch (strtolower($type)) {
            case 'sms':
                $strategy = new SMSStrategy();

                return (new Context($strategy))->executeReceiveStrategy();
            case 'push':
                $strategy = new PushStrategy();

                return (new Context($strategy))->executeReceiveStrategy();
            case 'message':
                $strategy = new MessageStrategy();

                return (new Context($strategy))->executeReceiveStrategy();

        }
    }

    /**
     * set expire at function
     *
     * @param Carbon|null $time
     * @return self
     */
    public function setExpireAt(?Carbon $time = null): self
    {
        $this->expireAt = $time;

        return $this;
    }

    /**
     *  set title function
     *
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * set body function
     *
     * @param string $txt
     * @return self
     */
    public function setBody(string $txt): self
    {
        $this->body = $txt;

        return $this;
    }

    /**
     * set receivers identifiers function
     *
     * @param array $identifiers
     * @return self
     */
    public function receivers(array $identifiers): self
    {
        $this->receivers = $identifiers;

        return $this;
    }
}
