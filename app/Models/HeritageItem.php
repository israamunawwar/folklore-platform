<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HeritageItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'category', 'image', 'status', 'user_id', 'price', 'stock'];

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** القطع المعتمدة مع عدّادات الإعجاب والتعليقات المقبولة */
    public function scopeStorefront($query)
    {
        return $query->where('status', 'approved')->withCount(['approvedComments', 'likes']);
    }
}
