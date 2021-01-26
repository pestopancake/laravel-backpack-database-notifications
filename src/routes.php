<?php

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'Pestopancake\LaravelBackpackNotifications\Http\Controllers',
], function () {
    Route::get('notification/unreadcount', [
        'uses' => 'NotificationCrudController@unreadCount',
        'as' => 'crud.notification.unreadcount',
    ]);
    Route::get('notification/dismissall', [
        'uses' => 'NotificationCrudController@dismissAll',
        'as' => 'crud.notification.dismissall',
    ]);
    Route::get('notification/{notification_id}/dismiss', [
        'uses' => 'NotificationCrudController@dismiss',
        'as' => 'crud.notification.dismiss',
    ]);
    Route::crud('notification', 'NotificationCrudController');
});