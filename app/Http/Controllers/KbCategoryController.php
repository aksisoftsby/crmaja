<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKbCategoryRequest;
use App\Models\KbArticle;
use App\Models\KbCategory;
use Illuminate\Http\RedirectResponse;

class KbCategoryController extends Controller
{
    public function store(StoreKbCategoryRequest $request): RedirectResponse
    {
        KbCategory::create($request->validated());

        return to_route('kb-articles.index')->with('status', 'Kategori Knowledge Base berhasil dibuat.');
    }

    public function destroy(KbCategory $category): RedirectResponse
    {
        $this->authorize('create', KbArticle::class);
        $category->delete();

        return to_route('kb-articles.index')->with('status', 'Kategori telah diarsipkan.');
    }
}
