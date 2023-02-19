<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('command/{type}', function ($type) {
  $types = [
    'migrate'       => 'migrate',
    'seed'          => 'db:seed',
    'createFile'    => 'command:cf'
  ];

  if (key_exists($type, $types)) {
    Artisan::call($types[$type]);
  }
});
