<?php

namespace App\Http\Controllers;

use App\Models\HeritageItem;
use Illuminate\View\View;

class HeritageController extends Controller
{
    public function index(): View
    {
        // 1. جلب الملابس (Approved)
        $clothing = HeritageItem::where('status', 'approved')
            ->where('category', 'أزياء وحلي تراثية')
            ->get();

        // 2. جلب الطعام (Approved)
        $food = HeritageItem::where('status', 'approved')
            ->where('category', 'أكلات شعبية')
            ->get();

        // 3. جلب الكتب والروايات (Approved)
        // لاحظ سمينا المتغير $tools عشان يشتغل مع كود الويلكام اللي عندك بدون مشاكل
        $tools = HeritageItem::where('status', 'approved')
            ->where('category', 'الكتب والروايات')
            ->get();

        return view('welcome', compact('clothing', 'food', 'tools'));
    }

    public function show($id): \Illuminate\View\View
{
    // جلب القطعة حسب الرقم، وإذا مو موجودة بيعطي خطأ 404
    $item = HeritageItem::findOrFail($id);

    return view('show', compact('item'));
}
}
