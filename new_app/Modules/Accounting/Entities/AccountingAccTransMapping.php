<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;

class AccountingAccTransMapping extends Model
{
    protected $guarded = ['id'];


    protected static function boot()
    {
        parent::boot();

        // Listen for the creating event, which occurs before a new model is saved to the database.
        static::creating(function ($model) {
            // Retrieve the last record's number and add 1
            $lastRecord = self::orderBy('id', 'desc')->first();
            $model->number = $lastRecord ? $lastRecord->number + 1 : 1;
        });
    }

}
