<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CareerRoadmap;
use App\Models\EventKarir;
use App\Models\Lowongan;
use App\Models\PerusahaanProfile;
use App\Models\SkillAssessment;

class SeoController extends Controller
{
    public function robots()
    {
        $sitemap = url('/sitemap.xml');

        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /perusahaan/dashboard',
            'Disallow: /auth',
            'Disallow: /skill-assessment/sertifikat',
            '',
            'Sitemap: ' . $sitemap,
            '',
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }

    public function sitemap()
    {
        $urls = [];

        // Halaman statis
        $pages = [
            ['route' => 'home', 'priority' => '1.0'],
            ['route' => 'jobs.index', 'priority' => '0.9'],
            ['route' => 'companies.index', 'priority' => '0.8'],
            ['route' => 'events.index', 'priority' => '0.8'],
            ['route' => 'articles.index', 'priority' => '0.8'],
            ['route' => 'roadmaps.index', 'priority' => '0.7'],
            ['route' => 'assessment.index', 'priority' => '0.7'],
            ['route' => 'about', 'priority' => '0.5'],
            ['route' => 'contact', 'priority' => '0.5'],
            ['route' => 'faq', 'priority' => '0.5'],
        ];
        foreach ($pages as $p) {
            $urls[] = ['loc' => route($p['route']), 'priority' => $p['priority']];
        }

        // Lowongan aktif
        foreach (Lowongan::aktif()->get() as $lowongan) {
            $urls[] = ['loc' => route('jobs.show', $lowongan), 'priority' => '0.8'];
        }

        // Perusahaan terverifikasi
        foreach (PerusahaanProfile::where('status_verifikasi', 'verified')->get() as $perusahaan) {
            $urls[] = ['loc' => route('companies.show', $perusahaan), 'priority' => '0.7'];
        }

        // Event aktif
        foreach (EventKarir::where('status', 'aktif')->get() as $event) {
            $urls[] = ['loc' => route('events.show', $event), 'priority' => '0.7'];
        }

        // Artikel terbit
        foreach (Article::where('status', 'published')->get() as $article) {
            $urls[] = ['loc' => route('articles.show', $article), 'priority' => '0.6'];
        }

        // Career roadmap aktif
        foreach (CareerRoadmap::where('status', 'aktif')->get() as $roadmap) {
            $urls[] = ['loc' => route('roadmaps.show', $roadmap), 'priority' => '0.6'];
        }

        // Skill assessment aktif
        foreach (SkillAssessment::where('status', 'aktif')->get() as $assessment) {
            $urls[] = ['loc' => route('assessment.show', $assessment), 'priority' => '0.5'];
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . e($u['loc']) . "</loc>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= "</urlset>";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
