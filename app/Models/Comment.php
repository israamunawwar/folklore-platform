<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'heritage_item_id', 'comment', 'rating', 'status', 'is_liked'];

    // العلاقة مع المستخدم
    public function user() {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع قطعة التراث
    public function heritageItem() {
        return $this->belongsTo(HeritageItem::class);
    }
}
