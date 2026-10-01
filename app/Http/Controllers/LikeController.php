<?php

namespace App\Http\Controllers;

use App\Models\HeritageItem;
use App\Models\Like;

class LikeController extends Controller
{
    public function toggle($itemId)
    {
        $item = HeritageItem::where('status', 'approved')->findOrFail($itemId);
        $userId = auth()->id();

        $like = Like::where('user_id', $userId)
            ->where('heritage_item_id', $item->id)
            ->first();

        if ($like) {
            $like->delete();
            return response()->json(['status' => 'unliked']);
        }

        Like::create([
            'user_id' => $userId,
            'heritage_item_id' => $item->id,
        ]);

        return response()->json(['status' => 'liked']);
    }
}
