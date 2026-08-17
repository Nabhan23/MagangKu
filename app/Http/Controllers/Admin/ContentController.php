<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Category;
use App\Models\EventKarir;
use App\Models\Faq;
use App\Models\Skill;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    protected const RESOURCES = ['event', 'artikel', 'banner', 'faq', 'testimoni', 'kategori', 'skill'];

    public function index(Request $request)
    {
        $tab = in_array($request->get('tab'), self::RESOURCES, true) ? $request->get('tab') : 'event';

        return view('admin.content', [
            'tab' => $tab,
            'events' => EventKarir::latest()->get(),
            'articles' => Article::latest()->get(),
            'banners' => Banner::orderBy('urutan')->get(),
            'faqs' => Faq::orderBy('urutan')->get(),
            'testimonials' => Testimonial::latest()->get(),
            'categories' => Category::get(),
            'skills' => Skill::get(),
            'kategoriEvent' => EventKarir::KATEGORI,
            'kategoriArtikel' => Article::KATEGORI,
        ]);
    }

    public function store(Request $request, string $resource)
    {
        $this->normalizeUrlFields($request);

        return match ($resource) {
            'event' => $this->storeEvent($request),
            'artikel' => $this->storeArtikel($request),
            'banner' => $this->storeBanner($request),
            'faq' => $this->storeFaq($request),
            'testimoni' => $this->storeTestimoni($request),
            'kategori' => $this->storeKategori($request),
            'skill' => $this->storeSkill($request),
            default => abort(404),
        };
    }

    public function update(Request $request, string $resource, int $item)
    {
        $this->normalizeUrlFields($request);

        return match ($resource) {
            'event' => $this->updateEvent($request, $item),
            'artikel' => $this->updateArtikel($request, $item),
            'banner' => $this->updateBanner($request, $item),
            'faq' => $this->updateFaq($request, $item),
            'testimoni' => $this->updateTestimoni($request, $item),
            'kategori' => $this->updateKategori($request, $item),
            'skill' => $this->updateSkill($request, $item),
            default => abort(404),
        };
    }

    public function destroy(string $resource, int $item)
    {
        match ($resource) {
            'event' => EventKarir::findOrFail($item)->delete(),
            'artikel' => Article::findOrFail($item)->delete(),
            'banner' => Banner::findOrFail($item)->delete(),
            'faq' => Faq::findOrFail($item)->delete(),
            'testimoni' => Testimonial::findOrFail($item)->delete(),
            'kategori' => Category::findOrFail($item)->delete(),
            'skill' => Skill::findOrFail($item)->delete(),
            default => abort(404),
        };

        ActivityLog::record('content_delete', "Menghapus konten {$resource} #{$item}", auth()->id());

        return back()->with('success', 'Konten berhasil dihapus.');
    }

    /* ---------- Event ---------- */
    protected function storeEvent(Request $request)
    {
        $data = $this->validateEvent($request);
        $event = EventKarir::create($data + ['slug' => Str::slug($data['judul']) . '-' . Str::random(4)]);
        $this->handleImage($request, 'event', $event);

        return back()->with('success', 'Event berhasil dibuat.');
    }

    protected function updateEvent(Request $request, int $id)
    {
        $event = EventKarir::findOrFail($id);
        $event->update($this->validateEvent($request));
        $this->handleImage($request, 'event', $event);

        return back()->with('success', 'Event berhasil diperbarui.');
    }

    protected function validateEvent(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:webinar,job_fair,workshop,campus_hiring'],
            'deskripsi' => ['required', 'string', 'max:10000'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'waktu' => ['nullable'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'is_online' => ['nullable', 'boolean'],
            'link_event' => ['nullable', 'url', 'max:255'],
            'kapasitas' => ['nullable', 'integer', 'min:1'],
            'penyelenggara' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,selesai,draft'],
        ]);
    }

    /* ---------- Artikel ---------- */
    protected function storeArtikel(Request $request)
    {
        $data = $this->validateArtikel($request);
        $article = Article::create($data + [
            'slug' => Str::slug($data['judul']),
            'published_at' => $request->status === 'published' ? now() : null,
            'penulis' => $data['penulis'] ?? 'Tim MagangKu',
        ]);
        $this->handleImage($request, 'artikel', $article);

        return back()->with('success', 'Artikel berhasil dibuat.');
    }

    protected function updateArtikel(Request $request, int $id)
    {
        $article = Article::findOrFail($id);
        $article->update($this->validateArtikel($request) + [
            'published_at' => $request->status === 'published' ? ($article->published_at ?? now()) : null,
        ]);
        $this->handleImage($request, 'artikel', $article);

        return back()->with('success', 'Artikel berhasil diperbarui.');
    }

    protected function validateArtikel(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:50'],
            'ringkasan' => ['nullable', 'string', 'max:500'],
            'konten' => ['required', 'string', 'max:30000'],
            'penulis' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft'],
        ]);
    }

    /* ---------- Banner ---------- */
    protected function storeBanner(Request $request)
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'teks' => ['nullable', 'string', 'max:500'],
            'link' => ['nullable', 'url', 'max:255'],
            'posisi' => ['required', 'string', 'max:30'],
            'urutan' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $banner = Banner::create($data + ['is_active' => $request->boolean('is_active', true)]);
        $this->handleImage($request, 'banner', $banner);

        return back()->with('success', 'Banner berhasil dibuat.');
    }

    protected function updateBanner(Request $request, int $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update($request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'teks' => ['nullable', 'string', 'max:500'],
            'link' => ['nullable', 'url', 'max:255'],
            'posisi' => ['required', 'string', 'max:30'],
            'urutan' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active', $banner->is_active)]);
        $this->handleImage($request, 'banner', $banner);

        return back()->with('success', 'Banner berhasil diperbarui.');
    }

    /* ---------- FAQ ---------- */
    protected function storeFaq(Request $request)
    {
        Faq::create($request->validate([
            'pertanyaan' => ['required', 'string', 'max:500'],
            'jawaban' => ['required', 'string', 'max:3000'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'urutan' => ['nullable', 'integer'],
        ]) + ['is_active' => $request->boolean('is_active', true), 'urutan' => $request->urutan ?? 0]);

        return back()->with('success', 'FAQ berhasil dibuat.');
    }

    protected function updateFaq(Request $request, int $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->update($request->validate([
            'pertanyaan' => ['required', 'string', 'max:500'],
            'jawaban' => ['required', 'string', 'max:3000'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'urutan' => ['nullable', 'integer'],
        ]) + ['is_active' => $request->boolean('is_active', $faq->is_active), 'urutan' => $request->urutan ?? 0]);

        return back()->with('success', 'FAQ berhasil diperbarui.');
    }

    /* ---------- Testimoni ---------- */
    protected function storeTestimoni(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'universitas' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:50'],
            'komentar' => ['required', 'string', 'max:3000'],
        ]);

        $testimoni = Testimonial::create($data + ['is_active' => $request->boolean('is_active', true)]);
        $this->handleImage($request, 'testimoni', $testimoni);

        return back()->with('success', 'Testimoni berhasil dibuat.');
    }

    protected function updateTestimoni(Request $request, int $id)
    {
        $testimoni = Testimonial::findOrFail($id);
        $testimoni->update($request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'universitas' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:50'],
            'komentar' => ['required', 'string', 'max:3000'],
        ]) + ['is_active' => $request->boolean('is_active', $testimoni->is_active)]);
        $this->handleImage($request, 'testimoni', $testimoni);

        return back()->with('success', 'Testimoni berhasil diperbarui.');
    }

    /* ---------- Kategori & Skill ---------- */
    protected function storeKategori(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:lowongan,event,artikel'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        Category::create($data + ['slug' => Str::slug($data['nama'])]);

        return back()->with('success', 'Kategori berhasil dibuat.');
    }

    protected function updateKategori(Request $request, int $id)
    {
        $kategori = Category::findOrFail($id);
        $kategori->update($request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:lowongan,event,artikel'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]));

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    protected function storeSkill(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:' . implode(',', array_keys(Skill::KATEGORI))],
        ]);
        Skill::create($data + ['slug' => Str::slug($data['nama'])]);

        return back()->with('success', 'Skill berhasil dibuat.');
    }

    protected function updateSkill(Request $request, int $id)
    {
        $skill = Skill::findOrFail($id);
        $skill->update($request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:' . implode(',', array_keys(Skill::KATEGORI))],
        ]));

        return back()->with('success', 'Skill berhasil diperbarui.');
    }

    /* ---------- Helper URL ---------- */
    protected function normalizeUrlFields(Request $request): void
    {
        foreach (['link_event', 'link'] as $field) {
            if ($request->filled($field) && !preg_match('#^https?://#i', $request->input($field))) {
                $request->merge([$field => 'https://' . $request->input($field)]);
            }
        }
    }

    /* ---------- Helper gambar ---------- */
    protected function handleImage(Request $request, string $field, $model): void
    {
        $key = match ($field) {
            'event' => 'gambar',
            'artikel' => 'gambar',
            'banner' => 'gambar',
            'testimoni' => 'foto',
            default => null,
        };

        if ($key && $request->hasFile($key)) {
            $request->validate([$key => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048']]);
            $model->update([$key => $request->file($key)->store('konten-' . $field, 'public')]);
        }
    }
}
