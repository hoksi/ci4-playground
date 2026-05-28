<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneration extends BaseController
{
    private string $fontDir  = '';
    private string $fontFile = '';

    private string $fontCache = '';

    public function __construct()
    {
        $this->fontDir   = WRITEPATH . 'fonts/';                                // UFM/TTF 복사본 (쓰기 가능, 런타임)
        $this->fontFile  = ROOTPATH . 'resources/fonts/NotoSansKR-Regular.ttf'; // TTF 원본 (git 추적)
        $this->fontCache = WRITEPATH . 'fonts/';                                // installed-fonts.json
    }

    public function index(): string
    {
        return view('examples/pdf_generation/index');
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

    private function buildDompdf(): Dompdf
    {
        $hasBundledFont = file_exists($this->fontFile);

        if (!is_dir($this->fontDir)) {
            mkdir($this->fontDir, 0755, true);
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        // chroot: TTF 원본 경로 포함 (registerFont 시 file:// 로 읽음)
        $options->setChroot([FCPATH, APPPATH . 'Views/', ROOTPATH . 'resources/fonts/']);
        $options->setFontDir($this->fontDir);
        $options->setFontCache($this->fontCache);
        $options->set('defaultFont', $hasBundledFont ? 'NotoSansKR' : 'DejaVu Sans');

        $dompdf = new Dompdf($options);

        if ($hasBundledFont) {
            $this->registerKoreanFont($dompdf);
        }

        return $dompdf;
    }

    private function registerKoreanFont(Dompdf $dompdf): void
    {
        $fontMetrics = $dompdf->getFontMetrics();
        $families    = $fontMetrics->getFontFamilies();

        // bold 포함 4변형 모두 등록 — 미등록 시에만 실행 (이후엔 installed-fonts.json 자동 로드)
        if (isset($families['notosanskr']['bold'])) {
            return;
        }

        $uri      = 'file://' . $this->fontFile;
        $variants = [
            ['family' => 'NotoSansKR', 'style' => 'normal', 'weight' => 'normal'],
            ['family' => 'NotoSansKR', 'style' => 'normal', 'weight' => 'bold'],
            ['family' => 'NotoSansKR', 'style' => 'italic', 'weight' => 'normal'],
            ['family' => 'NotoSansKR', 'style' => 'italic', 'weight' => 'bold'],
        ];

        foreach ($variants as $v) {
            $fontMetrics->registerFont($v, $uri);
        }

        $fontMetrics->saveFontFamilies();
    }

    private function renderHtml(string $view, array $data = []): string
    {
        return view($view, $data, ['saveData' => false]);
    }

    private function streamPdf(string $html, string $filename): void
    {
        $download = $this->request->getGet('download') === '1';

        $dompdf = $this->buildDompdf();
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
}
