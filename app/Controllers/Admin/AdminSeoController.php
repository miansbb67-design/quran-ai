<?php

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../Services/Database.php';
require_once __DIR__ . '/../../Services/Auth.php';

class AdminSeoController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();

        $routes = Database::fetchAll("SELECT * FROM seo_metadata ORDER BY route_path ASC");
        $settings = Database::fetchAll("SELECT * FROM site_settings WHERE setting_group = 'seo'");

        $siteSettings = [];
        foreach ($settings as $s) {
            $siteSettings[$s['setting_key']] = $s['setting_value'];
        }

        // Read robots.txt
        $robotsPath = __DIR__ . '/../../../public/robots.txt';
        $robotsContent = file_exists($robotsPath) ? file_get_contents($robotsPath) : "User-agent: *\nAllow: /\nSitemap: https://xyz.careernexaa.com/sitemap.xml";

        $this->view('admin/seo/index', [
            'title' => 'SEO & Search Console Center — Bayan AI Admin',
            'routes' => $routes,
            'settings' => $siteSettings,
            'robotsContent' => $robotsContent
        ]);
    }

    public function update(): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $routeId = (int) ($_POST['route_id'] ?? 0);
        $title = trim($_POST['meta_title'] ?? '');
        $description = trim($_POST['meta_description'] ?? '');
        $keywords = trim($_POST['meta_keywords'] ?? '');

        if ($routeId) {
            Database::update('seo_metadata', [
                'meta_title' => $title,
                'meta_description' => $description,
                'meta_keywords' => $keywords
            ], 'id = :id', ['id' => $routeId]);
        }

        // Update verification tags if supplied
        if (isset($_POST['google_verification'])) {
            Database::query(
                "INSERT INTO site_settings (setting_key, setting_value, setting_group) 
                 VALUES ('google_verification', :val, 'seo') 
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
                ['val' => trim($_POST['google_verification'])]
            );
        }

        $this->redirect('/admin/seo');
    }

    public function updateRobots(): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $content = $_POST['robots_content'] ?? '';
        $robotsPath = __DIR__ . '/../../../public/robots.txt';
        file_put_contents($robotsPath, $content);

        $this->redirect('/admin/seo');
    }
}
