<?php

Route::group(["prefix" => "pushes"], function() {
   Route::post("/", "Asanbar\Notifier\Controllers\NotifierController@sendPush");
});

Route::group(["prefix" => "smses"], function() {
    Route::post("/", "Asanbar\Notifier\Controllers\NotifierController@sendSms");
});
