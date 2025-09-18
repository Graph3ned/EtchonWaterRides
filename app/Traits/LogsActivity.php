<?php

namespace App\Traits;

use App\Models\StaffLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            StaffLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'old_values' => null,
                'new_values' => $model->getAttributes()
            ]);
        });

        static::updated(function ($model) {
            StaffLog::create([
                'user_id' => Auth::id(),
                'action' => 'edit',
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'old_values' => $model->getOriginal(),
                'new_values' => $model->getChanges()
            ]);
        });

        // static::deleted(function ($model) {
        //     StaffLog::create([
        //         'user_id' => Auth::id(),
        //         'action' => 'delete',
        //         'model_type' => get_class($model),
        //         'model_id' => $model->getKey(),
        //         'old_values' => $model->getOriginal(),
        //         'new_values' => null
        //     ]);
        // });
    }
}


