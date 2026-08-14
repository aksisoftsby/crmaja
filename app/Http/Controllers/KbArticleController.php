<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKbArticleRequest;
use App\Http\Requests\UpdateKbArticleRequest;
use App\Models\KbArticle;
use App\Models\KbCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KbArticleController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', KbArticle::class);

        return view('knowledge-base.index', ['articles' => KbArticle::query()->with('category')->latest()->paginate(20)]);
    }

    public function create(): View
    {
        $this->authorize('create', KbArticle::class);

        return view('knowledge-base.create', ['categories' => KbCategory::query()->orderBy('name')->get()]);
    }

    public function store(StoreKbArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6));
        $data['is_published'] = $request->boolean('is_published');
        $data['is_client_only'] = $request->boolean('is_client_only');
        $data['created_by'] = $request->user()->id;
        $article = KbArticle::create($data);

        return to_route('kb-articles.show', $article)->with('status', 'Artikel Knowledge Base berhasil dibuat.');
    }

    public function show(KbArticle $kb_article): View
    {
        $this->authorize('view', $kb_article);
        $kb_article->load('category');
        $kb_article->increment('views_count');

        return view('knowledge-base.show', ['article' => $kb_article]);
    }

    public function edit(KbArticle $kb_article): View
    {
        $this->authorize('update', $kb_article);

        return view('knowledge-base.edit', ['article' => $kb_article, 'categories' => KbCategory::query()->orderBy('name')->get()]);
    }

    public function update(UpdateKbArticleRequest $request, KbArticle $kb_article): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        $data['is_client_only'] = $request->boolean('is_client_only');
        $kb_article->update($data);

        return to_route('kb-articles.show', $kb_article)->with('status', 'Artikel Knowledge Base berhasil diperbarui.');
    }

    public function destroy(KbArticle $kb_article): RedirectResponse
    {
        $this->authorize('delete', $kb_article);
        $kb_article->delete();

        return to_route('kb-articles.index')->with('status', 'Artikel telah diarsipkan.');
    }
}
