<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\HeritageItem;
use Illuminate\View\View;

class HeritageController extends Controller
{
    public function index(): View
    {
        $approved = fn (Category $category) => HeritageItem::storefront()
            ->where('category', $category->value)
            ->get();

        $likedIds = auth()->check() ? auth()->user()->likedItemIds() : [];

        return view('welcome', [
            'likedIds' => $likedIds,
            'clothing' => $approved(Category::Clothing),
            'tools'    => $approved(Category::Tools),
            'food'     => $approved(Category::Food),
        ]);
    }

    public function show($id): View
    {
        // القطع المعتمدة فقط، وإلا 404
        $item = HeritageItem::storefront()->findOrFail($id);
        $liked = auth()->check() && in_array($item->id, auth()->user()->likedItemIds());

        return view('show', compact('item', 'liked'));
    }
}
