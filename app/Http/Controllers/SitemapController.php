<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\SitePage;
use App\Services\Quran\QuranApiService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap for search engines.
     */
    public function index(QuranApiService $quranApi): Response
    {
        $xml = Cache::remember('sitemap_xml_content', 3600, function () use ($quranApi) {
            $urls = [];
            $now = now()->toAtomString();

            // 1. Core Platform Pages
            $urls[] = [
                'loc' => url('/'),
                'lastmod' => $now,
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];

            $urls[] = [
                'loc' => url('/quran'),
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];

            $urls[] = [
                'loc' => url('/ai-teacher'),
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];

            $urls[] = [
                'loc' => url('/hadith'),
                'lastmod' => $now,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];

            $urls[] = [
                'loc' => url('/hifz'),
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];

            $urls[] = [
                'loc' => url('/login'),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];

            $urls[] = [
                'loc' => url('/register'),
                'lastmod' => $now,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];

            // 2. All 114 Quran Surahs
            for ($i = 1; $i <= 114; $i++) {
                $urls[] = [
                    'loc' => url("/quran/{$i}"),
                    'lastmod' => $now,
                    'changefreq' => 'monthly',
                    'priority' => ($i <= 10 || in_array($i, [18, 36, 55, 56, 67, 112, 113, 114])) ? '0.9' : '0.8',
                ];
            }

            // 3. Hadith Individual Pages
            $hadiths = Hadith::select('id', 'updated_at')->get();
            foreach ($hadiths as $hadith) {
                $urls[] = [
                    'loc' => url("/hadith/{$hadith->id}"),
                    'lastmod' => $hadith->updated_at ? $hadith->updated_at->toAtomString() : $now,
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            }

            // 4. Public Active Site Pages
            $pages = SitePage::active()->get();
            foreach ($pages as $page) {
                $urls[] = [
                    'loc' => url("/page/{$page->slug}"),
                    'lastmod' => $page->updated_at ? $page->updated_at->toAtomString() : $now,
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            }

            return view('sitemap', compact('urls'))->render();
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('X-Robots-Tag', 'noindex'); // Prevent sitemap itself from being indexed as a search result
    }
}
