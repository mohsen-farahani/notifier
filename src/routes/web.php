<?php

Route::group(["prefix" => "pushes"], function() {
   Route::post("/", "Asanbar\Notifier\Notifier@sendPush");
});

Route::group(["prefix" => "smses"], function() {
    Route::post("/", "Asanbar\Notifier\Notifier@sendSms");
});
