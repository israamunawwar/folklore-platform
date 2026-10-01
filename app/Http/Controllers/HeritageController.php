<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\HeritageItem;
use Illuminate\View\View;

class HeritageController extends Controller
{
    public function index(): View
    {
        $approved = fn (Category $category) => HeritageItem::where('status', 'approved')
            ->where('category', $category->value)
            ->get();

        return view('welcome', [
            'clothing' => $approved(Category::Clothing),
            'tools'    => $approved(Category::Tools),
            'food'     => $approved(Category::Food),
        ]);
    }

    public function show($id): View
    {
        // القطع المعتمدة فقط، وإلا 404
        $item = HeritageItem::where('status', 'approved')->findOrFail($id);

        return view('show', compact('item'));
    }
}
