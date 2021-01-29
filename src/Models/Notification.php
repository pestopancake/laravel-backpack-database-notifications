<?php

namespace Pestopancake\LaravelBackpackNotifications\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Venturecraft\Revisionable\RevisionableTrait;

class Notification extends Model
{
    use CrudTrait;

    public $timestamps = true;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // protected $casts = ['data' => 'array'];


    public function getDisplayNameAttribute()
    {
        return $this->id;
    }

    public function getDataAttribute()
    {
        return json_decode($this->attributes['data']);
    }

    public function dismissAllButton($crud)
    {
        if (backpack_user()->unreadNotifications()->count()) {
            return '<a href="' . route('crud.notification.dismissall') . '" class="btn btn-default ladda-button">Dismiss All</a>';
        }
    }

    public function dismissButton($crud)
    {
        if ($this->read_at) return '';
        return '<a href="' . route('crud.notification.dismiss', ['notification_id' => $this->id]) . '" class="btn btn-xs btn-default ladda-button">Dismiss</a>';
    }

    public function actionButton()
    {
        $str = '';
        if (!empty($this->data->action)) {
            $str = '<a href="' . $this->data->action->url . '" class="btn btn-primary btn-xs mb-1">' . $this->data->action->title . '</a><br>';
        } else if ($this->data->action_href ?? false) {
            $str = '<a href="' . $this->data->action_href . '" class="btn btn-primary btn-xs mb-1">' . $this->data->action_text . '</a><br>';
        }
        return $str;
    }
}
