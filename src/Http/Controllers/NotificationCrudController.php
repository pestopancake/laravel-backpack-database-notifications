<?php

namespace Pestopancake\LaravelBackpackNotifications\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Carbon\Carbon;
use CRUD;

class NotificationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function hasAdminAccess()
    {
        try {
            return backpack_user()->hasPermissionTo(
                config('backpack.databasenotifications.admin_permission_name'),
                config('auth.defaults.guard', 'web')
            );
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    public function setup()
    {
        $this->crud->setModel(config('backpack.databasenotifications.notification_model'));
        $this->crud->setRoute(config('backpack.base.route_prefix').'/notification');
        $this->crud->setEntityNameStrings('notification', 'notifications');

        $this->crud->addClause('orderBy', 'created_at', 'desc');

        $showAllUsers = $this->hasAdminAccess() && \Request::get('show_all');
        if (! $showAllUsers) {
            $this->crud->addClause('where', 'notifiable_id', backpack_user()->id);
            $this->crud->addClause('where', 'notifiable_type', config('backpack.base.user_model_fqn'));
        }

        if (! \Request::get('show_dismissed')) {
            $this->crud->addClause('whereNull', 'read_at');
        }

        $this->crud->addButtonFromModelFunction('top', 'dismiss_all', 'dismissAllButton', 'beginning');

        $this->crud->addButtonFromModelFunction('line', 'action', 'actionButton', 'end');
        $this->crud->addButtonFromModelFunction('line', 'dismiss', 'dismissButton', 'end');

        $this->crud->denyAccess(['create', 'delete', 'update', 'show']);
    }

    protected function setupListOperation()
    {
        $this->crud->setActionsColumnPriority(-1);
        // $this->crud->disableResponsiveTable();

        // Filters

        if (backpack_pro()) {
            $this->crud->addFilter(
                [
                    'type' => 'simple',
                    'name' => 'show_dismissed',
                    'label' => 'Show Dismissed',
                ],
                false,
                function () {
                    $this->crud->addClause('whereNotNull', 'read_at');
                }
            );
    
            if ($this->hasAdminAccess()) {
                $this->crud->addFilter(
                    [
                        'type' => 'simple',
                        'name' => 'show_all',
                        'label' => 'Show notifications for all users (admin only)',
                    ],
                    false,
                    function () {
                    }
                );
            }
        }

        // columns

        $this->crud->addColumn([
            'label' => 'Date',
            'type' => 'datetime',
            'name' => 'created_at',
        ]);

        $this->crud->addColumn([
            'name' => 'message',
            'label' => 'Message',
            'type' => 'custom_html',
            'priority' => -1,
            'value' => function ($entry) {
                return '<div style="display:inline-block; max-width:100%; white-space: pre-wrap;">'.
                    ($entry->data->message_long ?? $entry->data->message ?? '-').
                    '</div>';
            },
        ]);

        if ($this->hasAdminAccess() && \Request::get('show_all')) {
            $this->crud->addColumn([
                'label' => 'For',
                'type' => 'closure',
                'name' => 'notifiable_id',
                'function' => function ($entry) {
                    $user = backpack_user()::find($entry->notifiable_id);

                    return $user->displayName ?? '-';
                },
            ]);
        }
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->setupListOperation();
    }

    protected function setupCreateOperation()
    {
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function dismissAll()
    {
        backpack_user()->unreadNotifications->markAsRead();

        \Alert::success('All notifications dismissed')->flash();

        return redirect()->back();
    }

    public function dismiss($notificationId)
    {
        $notificationClass = config('backpack.databasenotifications.notification_model');
        $notification = $notificationClass::findOrFail($notificationId);

        $notification->read_at = Carbon::now();
        $notification->save();

        \Alert::success('Notification dismissed')->flash();

        return redirect()->back();
    }

    public function unreadCount()
    {
        $count = backpack_user()->unreadNotifications->count();

        $lastNotification = backpack_user()->unreadNotifications()->orderBy('created_at', 'desc')->first();

        return response()->json([
            'count' => $count,
            'last_notification' => $lastNotification ? $lastNotification->data : null,
        ]);
    }
}
