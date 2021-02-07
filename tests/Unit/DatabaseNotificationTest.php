<?php

namespace Tests\Unit;

use Pestopancake\LaravelBackpackNotifications\Models\Notification;
use Pestopancake\LaravelBackpackNotifications\Notifications\DatabaseNotification;
use Pestopancake\LaravelBackpackNotifications\Tests\TestCase;
use Pestopancake\LaravelBackpackNotifications\Tests\Unit\Models\User;

class DatabaseNotificationTest extends TestCase
{

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testNotifyUser()
    {
        $user = User::create([
            'name' => 'test user',
            'email' => 'test@test.com',
            'password' => bcrypt('test')
        ]);
        $user->notify(new DatabaseNotification(
            $type = 'info', // info / success / warning / error
            $message = 'test notification',
            $messageLong = 'this is a test notification', // optional
            $href = '/test', // optional, e.g. backpack_url('/example')
            $hrefText = 'test notification' // optional
        ));
        $this->assertDatabaseHas('notifications', [
            'type' => 'Pestopancake\\LaravelBackpackNotifications\\Notifications\\DatabaseNotification',
            'notifiable_type' => 'Pestopancake\\LaravelBackpackNotifications\\Tests\\Unit\\Models\\User',
            'notifiable_id' => $user->id
        ]);
    }
}
