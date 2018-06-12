<?php

namespace Asanbar\Notifier;

use Illuminate\Support\ServiceProvider;

class NotifierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . "/routes/web.php";

        $this->publishes([
            __DIR__ . "/config/notifier.php" => config_path("notifier.php")
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . "/Notifier.php";
    }
}
