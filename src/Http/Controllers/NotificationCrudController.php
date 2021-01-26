<?php

namespace Pestopancake\LaravelBackpackNotifications\Http\Controllers;

use Alert;
use App\Models\BackpackUser;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Carbon\Carbon;
use CRUD;
use Pestopancake\LaravelBackpackNotifications\Models\Notification;

class NotificationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('Pestopancake\LaravelBackpackNotifications\Models\Notification');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/notification');
        $this->crud->setEntityNameStrings('notification', 'notifications');

        $this->crud->addClause('orderBy', 'created_at', 'desc');

        // $this->crud->addClause('where', 'notifiable_type', 'App\Models\BackpackUser');
        // $this->crud->addClause('where', 'notifiable_id', backpack_user()->id);
        $showAllUsers = backpack_user()->hasRole('super admin') && \Request::get('show_all');
        if (!$showAllUsers) {
            $this->crud->addClause('where', 'notifiable_id', backpack_user()->id);
            $this->crud->addClause('where', 'notifiable_type', config('backpack.base.user_model_fqn'));
            
        }

        if (!\Request::get('show_dismissed')) {
            $this->crud->addClause('whereNull', 'read_at');
        }

        // if(backpack_user()->hasRole('super admin')){
        // $this->crud->addButtonFromModelFunction('top', 'show_all', 'showAllButton', 'beginning');
        // }
        $this->crud->addButtonFromModelFunction('top', 'dismiss_all', 'dismissAllButton', 'beginning');

        $this->crud->addButtonFromModelFunction('line', 'action', 'actionButton', 'end');
        $this->crud->addButtonFromModelFunction('line', 'dismiss', 'dismissButton', 'end');

        $this->crud->denyAccess(['create', 'delete', 'update', 'show']);

        // $this->crud->addButton('line', 'show', 'view', 'crud::buttons.preview', 'beginning');
        // $this->crud->allowAccess('show');
        // $this->crud->setShowView('admin/notification/view');

    }

    protected function setupListOperation()
    {
        $this->crud->setActionsColumnPriority(-1);
        // $this->crud->disableResponsiveTable();

        // Filters

        $this->crud->addFilter(
            [
                'type' => 'simple',
                'name' => 'show_dismissed',
                'label' => 'Show Dismissed'
            ],
            false,
            function () {
                $this->crud->addClause('whereNotNull', 'read_at');
            }
        );

        if (backpack_user()->hasRole('super admin')) {
            $this->crud->addFilter(
                [
                    'type' => 'simple',
                    'name' => 'show_all',
                    'label' => 'Show notifications for all users (admin only)'
                ],
                false,
                function () {
                }
            );
        }

        // columns

        $this->crud->addColumn([
            'label' => "Date",
            'type' => 'datetime',
            'name' => 'created_at',
        ]);

        $this->crud->addColumn([
            'name' => 'message',
            'label' => 'Message',
            'type' => 'closure',
            'priority' => -1,
            'function' => function ($entry) {
                return '<div style="display:inline-block; max-width:100%; white-space: pre-wrap;">' .
                    ($entry->data->message_long ?? $entry->data->message ?? '-') .
                    '</div>';
            }
        ]);

        if (backpack_user()->hasRole('super admin') && \Request::get('show_all')) {
            $this->crud->addColumn([
                'label' => "For",
                'type' => 'closure',
                'name' => 'notifiable_id',
                'function' => function ($entry) {
                    $user = BackpackUser::find($entry->notifiable_id);
                    return $user->displayName ?? '-';
                }
            ]);
        }

        // $this->crud->addColumn([
        //     'label' => 'Type',
        //     'type' => 'closure',
        //     'function' => function ($entry) {
        //         if ($entry->data->type) {
        //             switch ($entry->data->type ?? '') {
        //                 case 'member':
        //                     $color = 'blue';
        //                     break;
        //                 case 'error':
        //                     $color = 'red';
        //                     break;
        //                 case 'warning':
        //                     $color = 'orange';
        //                     break;
        //                 default:
        //                     $color = 'green';
        //             }
        //             return '<small class="label bg-' . $color . '">' . $entry->data->type . '</small>';
        //         }
        //     }
        // ]);


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
        $notification = Notification::findOrFail($notificationId);

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
            'last_notification' => $lastNotification ? $lastNotification->data : null
        ]);
    }
}
