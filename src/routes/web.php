<?php

Route::group(["prefix" => "pushes"], function() {
   Route::post("/", "Asanbar\Notifier\Notifier@sendPush");
});
