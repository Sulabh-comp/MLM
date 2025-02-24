<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];

    public function to()
    {
        $model = $this->model;
        return $this->belongsTo($model::class,"user_id","id");
    }
}
