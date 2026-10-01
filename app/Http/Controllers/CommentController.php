<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\HeritageItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'heritage_item_id' => 'required|exists:heritage_items,id',
            'comment' => 'required|string|max:500',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // التعليق مسموح فقط على القطع المعتمدة
        HeritageItem::where('status', 'approved')->findOrFail($request->heritage_item_id);

        Comment::create([
            'user_id' => Auth::id(),
            'heritage_item_id' => $request->heritage_item_id,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'status' => 'pending', // بانتظار موافقة الإدارة
        ]);

        return back()->with('success', 'تم إرسال تعليقك، سيظهر بعد مراجعة الإدارة.');
    }

    public function getComments($itemId)
    {
        $item = HeritageItem::where('status', 'approved')->findOrFail($itemId);

        // نرجّع اسم المستخدم فقط (بدون الإيميل أو الرتبة)
        $comments = $item->comments()
            ->where('status', 'approved')
            ->with('user:id,name')
            ->latest()
            ->get(['id', 'user_id', 'heritage_item_id', 'comment', 'rating', 'created_at']);

        return response()->json($comments);
    }
}
