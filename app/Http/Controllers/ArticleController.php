<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $articles = $query->withCount([])->latest('published_at')->paginate(9)->withQueryString();

        return view('articles.index', [
            'articles' => $articles,
            'kategoriList' => Article::KATEGORI,
            'activeKategori' => $request->kategori,
        ]);
    }

    public function show(Article $article)
    {
        abort_if($article->status !== 'published', 404);
        $article->increment('views');

        $related = Article::published()
            ->where('id', '!=', $article->id)
            ->where('kategori', $article->kategori)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('articles.show', [
            'article' => $article,
            'related' => $related,
        ]);
    }
}
