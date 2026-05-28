<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneration extends BaseController
{
    private string $fontDir  = '';
    private string $fontFile = '';

    public function __construct()
    {
        $this->fontDir  = WRITEPATH . 'fonts/';
        $this->fontFile = $this->fontDir . 'NotoSansKR-Regular.ttf';
    }

    public function index(): string
    {
        return view('examples/pdf_generation/index', [
            'fontInstalled' => $this->isFontInstalled(),
        ]);
    }

    /** 한글 폰트 설치 상태 JSON */
    public function fontStatus(): \CodeIgniter\HTTP\ResponseInterface
    {
        $installed = $this->isFontInstalled();
        return $this->response->setJSON([
            'installed' => $installed,
            'size'      => $installed ? round(filesize($this->fontFile) / 1024 / 1024, 1) . ' MB' : null,
        ]);
    }

    /** NotoSansKR 폰트 다운로드 및 DOMPDF 등록 */
    public function installFont(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!is_dir($this->fontDir)) {
            mkdir($this->fontDir, 0755, true);
        }

        // ① 폰트 다운로드 (jsDelivr → GitHub google/fonts 미러)
        if (!file_exists($this->fontFile)) {
            $urls = [
                'https://cdn.jsdelivr.net/gh/google/fonts@main/ofl/notosanskr/static/NotoSansKR-Regular.ttf',
                'https://github.com/google/fonts/raw/main/ofl/notosanskr/static/NotoSansKR-Regular.ttf',
            ];

            $data = false;
            foreach ($urls as $url) {
                $data = $this->fetchUrl($url);
                if ($data && strlen($data) > 10000) {
                    break;
                }
            }

            if (!$data || strlen($data) < 10000) {
                return $this->response->setJSON([
                    'ok'    => false,
                    'error' => '폰트 다운로드 실패. 수동 설치 방법을 참고하세요.',
                ]);
            }

            file_put_contents($this->fontFile, $data);
        }

        // ② DOMPDF에 폰트 메트릭스 등록
        try {
            $options = $this->buildOptions(true);
            $dompdf  = new Dompdf($options);
            $metrics = $dompdf->getFontMetrics();
            $metrics->registerFont(
                ['family' => 'NotoSansKR', 'style' => 'normal', 'weight' => 'normal'],
                $this->fontFile
            );
        } catch (\Throwable $e) {
            return $this->response->setJSON(['ok' => false, 'error' => $e->getMessage()]);
        }

        return $this->response->setJSON([
            'ok'      => true,
            'message' => 'NotoSansKR 폰트 설치 완료! 이제 PDF에서 한글이 출력됩니다.',
            'size'    => round(filesize($this->fontFile) / 1024 / 1024, 1) . ' MB',
        ]);
    }

    /** 상품 목록 PDF */
    public function products(): void
    {
        $db       = \Config\Database::connect();
        $products = $db->table('playground_products')->orderBy('category')->get()->getResultArray();

        $this->streamPdf(
            $this->renderHtml('examples/pdf_generation/tpl_products', [
                'products'  => $products,
                'total'     => array_sum(array_column($products, 'price')),
                'generated' => date('Y-m-d H:i:s'),
            ]),
            'products-report.pdf'
        );
    }

    /** 게시글 목록 PDF */
    public function posts(): void
    {
        $db    = \Config\Database::connect();
        $posts = $db->table('posts')
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        $this->streamPdf(
            $this->renderHtml('examples/pdf_generation/tpl_posts', [
                'posts'     => $posts,
                'generated' => date('Y-m-d H:i:s'),
            ]),
            'posts-report.pdf'
        );
    }

    /** 인보이스 샘플 PDF */
    public function invoice(): void
    {
        $this->streamPdf(
            $this->renderHtml('examples/pdf_generation/tpl_invoice', [
                'invoice_no' => 'INV-' . date('Ymd') . '-001',
                'date'       => date('Y-m-d'),
                'due_date'   => date('Y-m-d', strtotime('+30 days')),
                'items'      => [
                    ['desc' => 'CI4 개발 컨설팅',  'qty' => 3, 'price' => 300000],
                    ['desc' => 'PHP 교육 과정',    'qty' => 1, 'price' => 500000],
                    ['desc' => 'CodeIgniter 셋업', 'qty' => 2, 'price' => 150000],
                ],
            ]),
            'invoice.pdf'
        );
    }

    // ─── 내부 헬퍼 ────────────────────────────────────────────────────────────

    private function isFontInstalled(): bool
    {
        return file_exists($this->fontFile) && filesize($this->fontFile) > 10000;
    }

    private function buildOptions(bool $forInstall = false): Options
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->setChroot([FCPATH, APPPATH . 'Views/']);

        if (is_dir($this->fontDir)) {
            $options->setFontDir($this->fontDir);
            $options->setFontCache($this->fontDir);
        }

        $options->set('defaultFont', $this->isFontInstalled() ? 'NotoSansKR' : 'DejaVu Sans');
        return $options;
    }

    private function renderHtml(string $view, array $data = []): string
    {
        return view($view, $data, ['saveData' => false]);
    }

    private function streamPdf(string $html, string $filename): void
    {
        $download = $this->request->getGet('download') === '1';

        $dompdf = new Dompdf($this->buildOptions());
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $disposition = $download ? 'attachment' : 'inline';
        $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', "{$disposition}; filename=\"{$filename}\"")
            ->setHeader('Cache-Control', 'private, max-age=0, must-revalidate')
            ->setHeader('Pragma', 'public')
            ->setBody($dompdf->output())
            ->send();
        exit;
    }

    private function fetchUrl(string $url): string|false
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_USERAGENT      => 'CI4-Playground/1.0',
            ]);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }

        return @file_get_contents($url);
    }
}
