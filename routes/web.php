<?php

use Illuminate\Support\Facades\Route;
use JustBetter\MagentoClient\Http\Controllers\OAuthController;

Route::get('identity', [OAuthController::class, 'identity'])
    ->middleware(config('magento.oauth.middleware'))
    ->name('magento.oauth.identity');

Route::post('callback', [OAuthController::class, 'callback'])
    ->name('magento.oauth.callback');
