<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Followup extends BaseModel
{
       protected $guarded = [];

    public function lead(){
        return $this->belongsTo(Lead::class);
    }


    public function followupUser(){
        return $this->belongsTo(User::class, 'follow_up_user_id');
    }
}
