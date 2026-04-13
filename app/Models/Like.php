<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    // هاد السطر هو اللي بيمنع إيرور 500 عند الإضافة
    protected $fillable = ['user_id', 'heritage_item_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(HeritageItem::class, 'heritage_item_id');
    }
}
