<?php

namespace App\Http\Controllers;

use App\Models\HeritageItem;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle($itemId)
{
    // تأكد إن المستخدم مسجل دخول
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $userId = auth()->id();

    // البحث عن اللايك
    $like = \App\Models\Like::where('user_id', $userId)
                ->where('heritage_item_id', $itemId)
                ->first();

    if ($like) {
        $like->delete();
        return response()->json(['status' => 'unliked']);
    }

    // إضافة لايك جديد
    \App\Models\Like::create([
        'user_id' => $userId,
        'heritage_item_id' => $itemId
    ]);

    return response()->json(['status' => 'liked']);
}
}
