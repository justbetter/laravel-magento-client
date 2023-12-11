<?php

use Illuminate\Support\Facades\Route;
use JustBetter\MagentoClient\Http\Controllers\OAuthController;

Route::post('callback/{connection}', [OAuthController::class, 'callback'])
    ->name('magento.oauth.callback');

Route::get('identity/{connection}', [OAuthController::class, 'identity'])
    ->middleware(config('magento.oauth.middleware'))
    ->name('magento.oauth.identity');
