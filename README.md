# Laravel Backpack Database Notifications

[![Packagist Version](https://img.shields.io/packagist/v/pestopancake/laravel-backpack-database-notifications)](https://packagist.org/packages/pestopancake/laravel-backpack-database-notifications) [![Build Status](https://travis-ci.com/pestopancake/laravel-backpack-database-notifications.svg?branch=main)](https://travis-ci.com/pestopancake/laravel-backpack-database-notifications) [![StyleCI](https://github.styleci.io/repos/333218543/shield?branch=main)](https://github.styleci.io/repos/333218543?branch=main)

![](https://raw.githubusercontent.com/pestopancake/laravel-backpack-database-notifications/main/preview.gif) 

## Prerequisites

 - Have Laravel Backpack installed [backpack/crud](https://github.com/Laravel-Backpack/CRUD) v4.0.* | 4.1.*
 - Follow the steps for laravel's [database notifications prerequisites](https://laravel.com/docs/8.x/notifications#database-notifications), e.g: 

<!-- x -->

    php artisan notifications:table
    php artisan migrate

## Installation

    composer require pestopancake/laravel-backpack-database-notifications

## Usage

Publish the config file:

    php artisan vendor:publish --provider="Pestopancake\\LaravelBackpackNotifications\\LaravelBackpackNotificationsServiceProvider" --tag=config

### Show in side menu

Edit 'resources/views/vendor/backpack/base/inc/sidebar_content.blade.php' and add:

    @include('backpack-database-notifications::sidebarMenuItem')

### Admin view

If you also have the [Permission Manager](https://github.com/Laravel-Backpack/PermissionManager) package installed then you can assign the permission 'notifications admin' to users for them to see admin functionality.

To change the permission name edit 'admin_permission_name' in the databasenotifications config file.

Currently users with the admin permission can see/dismiss notifications for all users.

### Create a notification

#### Use the included generic notification

    use Pestopancake\LaravelBackpackNotifications\Notifications\DatabaseNotification;
    
    $user = backpack_user();
    $user->notify(new DatabaseNotification(
        $type = 'info', // info / success / warning / error
        $message = 'test notification',
        $messageLong = 'this is a test notification', // optional
        $href = '/test', // optional, e.g. backpack_url('/example')
        $hrefText = 'test notification' // optional
    ));

#### Use in any other notification

The toArray method of the notification should be in this format:

    return [
        'type' => "info", // info / success / warning / error
        'message' => "",
        'message_long' => "", // optional
        'action_href' => "", // optional, e.g. backpack_url('/example')
        'action_text' => "", // optional
    ];

The type will affect the colour of the toast notification (if toasts are enabled in the config)

## Troubleshooting

### Notification not created

 - Make sure the model you are notifying matches the model defined in your backpack config at backpack.base.user_model_fqn (found in config/backpack/base.php)

## Change log

See the [changelog](/pestopancake/laravel-backpack-database-notifications/blob/main/changelog.md) for more information on what has changed recently.


## Todo

 - Add unit tests
 - translatable text
 - notification preferences