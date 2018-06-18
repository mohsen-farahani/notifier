<?php

Route::group(["prefix" => "pushes"], function() {
   Route::post("/", "Asanbar\Notifier\Controllers\NotifierController@sendPush");
   Route::get("/", "Asanbar\Notifier\Controllers\NotifierController@getPushes");
});

Route::group(["prefix" => "smses"], function() {
    Route::post("/", "Asanbar\Notifier\Controllers\NotifierController@sendSms");
    Route::get("/", "Asanbar\Notifier\Controllers\NotifierController@getSmses");
});
