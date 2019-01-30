<?php
namespace Asanbar\Notifier\NotificationService;

use Carbon\Carbon;

interface NotificationInterface
{
    /**
     * set expire at function
     *
     * @param Carbon|null $time
     * @return self
     */
    public function setExpireAt(?Carbon $time = null): self;

    /**
     *  set title function
     *
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self;

    /**
     * set body function
     *
     * @param string $txt
     * @return self
     */
    public function setBody(string $txt): self;

    /**
     * set receivers identifiers function
     *
     * @param array $identifiers
     * @return self
     */
    public function receivers(array $identifiers): self;

    /**
     * send notification function
     *
     * @return array
     */
    public function send(): array;
}
