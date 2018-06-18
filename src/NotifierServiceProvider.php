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
        $current_dir = __DIR__;

        include $current_dir . "/routes/web.php";

        $this->publishes([
            $current_dir . "/config/notifier.php" => config_path("notifier.php")
        ]);

        $this->loadMigrationsFrom($current_dir . "/migrations");
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
