<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadItem extends BaseModel
{
    use HasFactory;

    public function Item(){
        return $this->belongsTo(Item::class,'item_id');
    }
}
