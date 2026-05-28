<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneration extends BaseController
{
    public function index(): string
    {
        return view('examples/pdf_generation/index');
    }

    /** 상품 목록 PDF */
    public function products(): void
    {
        $db       = \Config\Database::connect();
        $products = $db->table('playground_products')->orderBy('category')->get()->getResultArray();
        $total    = array_sum(array_column($products, 'price'));

        $html = $this->renderHtml('examples/pdf_generation/tpl_products', [
            'products'  => $products,
            'total'     => $total,
            'generated' => date('Y-m-d H:i:s'),
        ]);

        $download = $this->request->getGet('download') === '1';
        $this->streamPdf($html, 'products-report.pdf', $download);
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

        $html = $this->renderHtml('examples/pdf_generation/tpl_posts', [
            'posts'     => $posts,
            'generated' => date('Y-m-d H:i:s'),
        ]);

        $download = $this->request->getGet('download') === '1';
        $this->streamPdf($html, 'posts-report.pdf', $download);
    }

    /** 인보이스 샘플 PDF */
    public function invoice(): void
    {
        $html = $this->renderHtml('examples/pdf_generation/tpl_invoice', [
            'invoice_no' => 'INV-' . date('Ymd') . '-001',
            'date'       => date('Y-m-d'),
            'due_date'   => date('Y-m-d', strtotime('+30 days')),
            'items'      => [
                ['desc' => 'CI4 개발 컨설팅',   'qty' => 3, 'price' => 300000],
                ['desc' => 'PHP 교육 과정',     'qty' => 1, 'price' => 500000],
                ['desc' => 'CodeIgniter 셋업',  'qty' => 2, 'price' => 150000],
            ],
        ]);

        $download = $this->request->getGet('download') === '1';
        $this->streamPdf($html, 'invoice.pdf', $download);
    }

    // ─── 내부 헬퍼 ────────────────────────────────────────────────────────────

    private function renderHtml(string $view, array $data = []): string
    {
        return view($view, $data, ['saveData' => false]);
    }

    private function streamPdf(string $html, string $filename, bool $download = false): void
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->setChroot([FCPATH, APPPATH . 'Views/']);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // CI4 response 객체를 통해 전송 — dompdf->stream()의 직접 header() 호출을 피함
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
