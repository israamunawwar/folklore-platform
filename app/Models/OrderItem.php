<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'heritage_item_id',
        'quantity',
        'price'
    ];

    // هذه العلاقة ضرورية جداً لربط عنصر الطلب بالمنتج الأصلي
    public function heritageItem()
    {
        return $this->belongsTo(HeritageItem::class, 'heritage_item_id');
    }

    // علاقة عكسية مع الطلب (اختياري لكن مفيد)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
