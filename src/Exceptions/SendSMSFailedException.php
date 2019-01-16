<?php

namespace Asanbar\Notifier\Exceptions;

class SendSMSFailedException extends \Exception
{
    /** @var strign message */
    protected $message;

    /** @var strign[] numbers */
    private $numbers;

    /** @var strign server error */
    private $serverError;

    /** @var strign sms body */
    private $smsBody;

    /** @var strign provider name */
    private $providerName;

    /**
     * constructor function
     *
     * @param string $message
     * @param array $numbers
     * @param string $serverError
     * @param string $smsBody
     * @param string $providerName
     */
    public function __construct(string $message, array $numbers, string $serverError, string $smsBody, string $providerName)
    {
        $this->message      = $message;
        $this->numbers      = $numbers;
        $this->serverError  = $serverError;
        $this->smsBody      = $smsBody;
        $this->providerName = $providerName;
    }

    /**
     * Get the value of numbers
     *
     * @return string[]
     */
    public function getNumbers(): array
    {
        return $this->numbers;
    }

    /**
     * Get the value of serverError
     *
     * @return string
     */
    public function getServerError(): string
    {
        return $this->serverError;
    }

    /**
     * Get the value of smsBody
     *
     * @return string
     */
    public function getSmsBody(): string
    {
        return $this->smsBody;
    }

    /**
     * Get the value of providerName
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }
}
