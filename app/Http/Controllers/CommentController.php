<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // 1. التأكد من البيانات
        $request->validate([
            'heritage_item_id' => 'required|exists:heritage_items,id',
            'comment' => 'required|string|max:500',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // 2. حفظ التعليق في قاعدة البيانات
        Comment::create([
            'user_id' => Auth::id(), // الشخص اللي مسجل دخوله
            'heritage_item_id' => $request->heritage_item_id,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'status' => 'pending', // بانتظار موافقة المدير (كما طلبت)
        ]);

        return back()->with('success', 'تم إرسال تعليقك، سيظهر بعد مراجعة الإدارة.');
    }

    public function getComments($itemId)
{
    // جلب التعليقات المقبولة فقط مع اسم المستخدم
    $comments = Comment::where('heritage_item_id', $itemId)
                        ->where('status', 'approved')
                        ->with('user')
                        ->latest()
                        ->get();

    return response()->json($comments);
}
}
