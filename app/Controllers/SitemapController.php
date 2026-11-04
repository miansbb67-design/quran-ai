<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Services/Database.php';

class SitemapController extends Controller
{
    public function xml(): void
    {
        header('Content-Type: application/xml; charset=utf-8');

        $baseUrl = 'https://xyz.careernexaa.com';
        $surahs = Database::fetchAll("SELECT id FROM surahs ORDER BY id ASC");

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        echo '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        echo '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
        echo '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        // Static core routes
        $staticRoutes = [
            ['loc' => '/', 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => '/quran', 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => '/recitations', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => '/ai-teacher', 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => '/hifz', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => '/login', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => '/register', 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        foreach ($staticRoutes as $r) {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($baseUrl . $r['loc']) . "</loc>\n";
            echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "    <changefreq>" . $r['changefreq'] . "</changefreq>\n";
            echo "    <priority>" . $r['priority'] . "</priority>\n";
            echo "  </url>\n";
        }

        // All 114 Surahs
        foreach ($surahs as $s) {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($baseUrl . '/quran/' . $s['id']) . "</loc>\n";
            echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }
}
