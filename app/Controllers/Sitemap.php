<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Sitemap extends Controller
{
    /** GET /sitemap.xml */
    public function index(): void
    {
        $urls = $this->buildUrls();

        $this->response
            ->setContentType('application/xml; charset=UTF-8')
            ->setBody($this->renderXml($urls))
            ->send();
        exit;
    }

    /** GET /robots.txt */
    public function robots(): void
    {
        $sitemap = base_url('sitemap.xml');

        $this->response
            ->setContentType('text/plain; charset=UTF-8')
            ->setBody("User-agent: *\nAllow: /\n\nSitemap: {$sitemap}\n")
            ->send();
        exit;
    }

    private function buildUrls(): array
    {
        // 인기 예제 (priority 0.9)
        $popular = [
            'examples/routing', 'examples/controllers', 'examples/views',
            'examples/models', 'examples/api', 'examples/board',
            'examples/auth', 'examples/validation', 'examples/filters',
            'examples/chat', 'examples/cat-game',
        ];

        // 일반 예제 (priority 0.8)
        $standard = [
            'examples/entityadvanced', 'examples/apiv2',
            'examples/fileupload', 'examples/fileupload-advanced', 'examples/tinymce',
            'examples/session', 'examples/httpclient', 'examples/email',
            'examples/servicelayer', 'examples/repository', 'examples/helper',
            'examples/cache', 'examples/lang', 'examples/events', 'examples/cli',
            'examples/testing', 'examples/transaction', 'examples/logging',
            'examples/exception', 'examples/throttler', 'examples/modelcallback',
            'examples/configenv', 'examples/advancedvalidation', 'examples/apiauth',
            'examples/securitydemo', 'examples/querybuilderadvanced',
            'examples/paginationadvanced', 'examples/multidb', 'examples/imageprocess',
            'examples/encryption', 'examples/queue', 'examples/csv-excel',
            'examples/official-queue', 'examples/taskscheduler', 'examples/pdfgeneration',
            'examples/sse', 'examples/notification', 'examples/aggrid',
            'examples/ajax-pagination', 'examples/sync-editor', 'examples/chart',
            'examples/spam-admin',
        ];

        $urls = [['loc' => base_url('/'), 'priority' => '1.0', 'changefreq' => 'weekly']];

        foreach ($popular as $path) {
            $urls[] = ['loc' => base_url($path), 'priority' => '0.9', 'changefreq' => 'monthly'];
        }
        foreach ($standard as $path) {
            $urls[] = ['loc' => base_url($path), 'priority' => '0.8', 'changefreq' => 'monthly'];
        }

        return $urls;
    }

    private function renderXml(array $urls): string
    {
        $today = date('Y-m-d');
        $items = '';

        foreach ($urls as $url) {
            $items .= sprintf(
                "\n  <url>\n    <loc>%s</loc>\n    <lastmod>%s</lastmod>\n    <changefreq>%s</changefreq>\n    <priority>%s</priority>\n  </url>",
                htmlspecialchars($url['loc']),
                $today,
                $url['changefreq'],
                $url['priority']
            );
        }

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . $items . "\n</urlset>\n";
    }
}
