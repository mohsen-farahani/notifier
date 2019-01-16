<?php

namespace Asanbar\Notifier\Exceptions;

class SendPushFailedException extends \Exception
{
    /** @var strign message */
    protected $message;

    /** @var strign[] tokens */
    private $tokens;

    /** @var strign server error */
    private $serverError;

    /** @var strign push body */
    private $pushBody;

    /** @var strign provider name */
    private $providerName;

    /**
     * constructor function
     *
     * @param string $message
     * @param array $tokens
     * @param string $serverError
     * @param string $pushBody
     * @param string $providerName
     */
    public function __construct(string $message, array $tokens, string $serverError, string $pushBody, string $providerName)
    {
        $this->message      = $message;
        $this->tokens       = $tokens;
        $this->serverError  = $serverError;
        $this->pushBody     = $pushBody;
        $this->providerName = $providerName;
    }

    /**
     * Get the value of tokens
     *
     * @return string[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
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
     * Get the value of pushBody
     *
     * @return string
     */
    public function getSmsBody(): string
    {
        return $this->pushBody;
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
