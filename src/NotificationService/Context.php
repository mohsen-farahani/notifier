<?php
namespace Asanbar\Notifier\NotificationService;

class Context
{
    /** @var NotificationInterface */
    private $strategy;

    /**
     * context constructor function
     *
     * @param NotificationInterface $strategy
     */
    public function __construct(NotificationInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * strategy executor function
     *
     * @return mixed
     */
    public function executeSendStrategy()
    {
        return $this->strategy->send();
    }

    /**
     * strategy executor function
     *
     * @return mixed
     */
    public function executeReceiveStrategy()
    {
        return $this->strategy->receive();
    }
}
