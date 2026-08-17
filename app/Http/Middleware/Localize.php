<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

/**
 * Middleware lokalitas aplikasi.
 *
 * - Menentukan locale dari session (fallback: cookie, lalu config).
 * - Menetapkan locale aplikasi (berlaku juga untuk tanggal Carbon/translatedFormat).
 * - Saat locale = 'en', menerjemahkan teks pada respons HTML menggunakan kamus
 *   lang/en.php. Hanya node teks (di antara tag) yang diterjemahkan — atribut,
 *   URL, CSS, dan JSON/API tidak tersentuh.
 *
 * Terjemahan memakai preg_replace dengan alternation yang diurutkan dari frasa
 * terpanjang, sehingga frasa majemuk menang dan nama brand ("MagangKu") tidak
 * pernah rusak oleh terjemahan kata parsial ("Magang").
 */
class Localize
{
    protected static ?array $compiled = null;

    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $request->session()->get('locale', $request->cookie('locale', config('app.locale', 'id')));

        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        $response = $next($request);

        if ($locale === 'en' && $response instanceof Response && $this->isHtml($response)) {
            $content = $response->getContent();

            if (is_string($content) && $content !== '') {
                $response->setContent($this->translate($content));
            }
        }

        return $response;
    }

    protected function isHtml(Response $response): bool
    {
        return str_contains($response->headers->get('Content-Type', ''), 'text/html');
    }

    protected function translate(string $html): string
    {
        // Frasa yang terpecah tag HTML (mis. <span> di tengah kalimat).
        foreach ($this->htmlRules() as $pattern => $replacement) {
            $html = preg_replace($pattern, $replacement, $html) ?? $html;
        }

        [$pattern, $map] = $this->compiled();

        // Terjemahkan hanya node teks (antara tag) agar atribut & URL aman.
        $result = preg_replace_callback('/>[^<]+</', function (array $match) use ($pattern, $map): string {
            $translated = preg_replace_callback($pattern, fn (array $m): string => $map[$m[0]] ?? $m[0], $match[0]);

            return $translated ?? $match[0];
        }, $html);

        return $result ?? $html;
    }

    /**
     * Aturan level HTML untuk frasa yang terpecah tag (mis. <span class="grad">).
     */
    protected function htmlRules(): array
    {
        return [
            // Hero landing: Temukan [Magang] dan [Pekerjaan] Impianmu
            '~>\s*Temukan <span class="grad">Magang</span> dan <span class="grad">Pekerjaan</span> Impianmu\s*<~' => '>Find Your Dream <span class="grad">Internship</span> & <span class="grad">Job</span><',
            // Halaman lowongan: Temukan [Lowongan] & [Magang] Impianmu
            '~>\s*Temukan <span class="grad">Lowongan</span> & <span class="grad">Magang</span> Impianmu\s*<~' => '>Find Your Dream <span class="grad">Job</span> & <span class="grad">Internship</span><',
        ];
    }

    /**
     * Pattern alternation (frasa terpanjang dulu) + peta kunci → terjemahan.
     */
    protected function compiled(): array
    {
        if (self::$compiled === null) {
            $dictionary = require base_path('lang/en.php');

            uksort($dictionary, fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));

            $keys = array_keys($dictionary);
            $quoted = array_map(fn (string $key): string => preg_quote($key, '/'), $keys);

            self::$compiled = [
                '/(?:'.implode('|', $quoted).')/u',
                array_combine($keys, array_values($dictionary)),
            ];
        }

        return self::$compiled;
    }
}
