<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CsvExcel extends BaseController
{
    private string $table = 'playground_products';

    public function index(): string
    {
        $db       = \Config\Database::connect();
        $this->ensureTable($db);

        $products   = $db->table($this->table)->orderBy('id')->get()->getResultArray();
        $categories = array_unique(array_column($products, 'category'));

        return view('examples/csv_excel/index', [
            'products'   => $products,
            'count'      => count($products),
            'categories' => count($categories),
        ]);
    }

    // ─── CSV 내보내기 ────────────────────────────────────────────────────────────

    public function exportCsv(): \CodeIgniter\HTTP\Response
    {
        $db       = \Config\Database::connect();
        $products = $db->table($this->table)->orderBy('id')->get()->getResultArray();

        ob_start();
        $fp = fopen('php://output', 'w');
        fputs($fp, "\xEF\xBB\xBF"); // UTF-8 BOM — Excel 한글 깨짐 방지
        fputcsv($fp, ['ID', '상품명', '카테고리', '가격', '재고', '등록일']);
        foreach ($products as $row) {
            fputcsv($fp, [$row['id'], $row['name'], $row['category'], $row['price'], $row['stock'], $row['created_at']]);
        }
        fclose($fp);
        $body = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="products_' . date('Ymd_His') . '.csv"')
            ->setHeader('Pragma', 'no-cache')
            ->setBody($body);
    }

    // ─── CSV 가져오기 ────────────────────────────────────────────────────────────

    public function importCsv(): \CodeIgniter\HTTP\RedirectResponse
    {
        $file = $this->request->getFile('csv_file');

        if (! $file || ! $file->isValid()) {
            return redirect()->to('examples/csv-excel')->with('error', '파일을 선택해주세요.');
        }
        if ($file->getClientExtension() !== 'csv') {
            return redirect()->to('examples/csv-excel')->with('error', 'CSV 파일만 업로드 가능합니다.');
        }

        $db    = \Config\Database::connect();
        $count = 0;
        $fp    = fopen($file->getTempName(), 'r');

        // BOM 제거
        $bom = fread($fp, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($fp);
        }

        fgetcsv($fp); // 헤더 행 스킵

        while (($row = fgetcsv($fp)) !== false) {
            if (count($row) < 5 || empty($row[1])) continue;
            $db->table($this->table)->insert([
                'name'       => trim($row[1]),
                'category'   => trim($row[2]),
                'price'      => (float) $row[3],
                'stock'      => (int)   $row[4],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $count++;
        }
        fclose($fp);

        return redirect()->to('examples/csv-excel')->with('success', "CSV에서 {$count}건을 가져왔습니다.");
    }

    // ─── Excel 내보내기 ──────────────────────────────────────────────────────────

    public function exportExcel(): \CodeIgniter\HTTP\Response
    {
        $db       = \Config\Database::connect();
        $products = $db->table($this->table)->orderBy('id')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('상품 목록');

        // 헤더 행
        $headers = ['ID', '상품명', '카테고리', '가격', '재고', '등록일'];
        foreach ($headers as $col => $header) {
            $sheet->setCellValue(chr(65 + $col) . '1', $header);
        }

        // 헤더 스타일
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // 데이터 행
        foreach ($products as $i => $row) {
            $r = $i + 2;
            $sheet->setCellValue('A' . $r, $row['id']);
            $sheet->setCellValue('B' . $r, $row['name']);
            $sheet->setCellValue('C' . $r, $row['category']);
            $sheet->setCellValue('D' . $r, $row['price']);
            $sheet->setCellValue('E' . $r, $row['stock']);
            $sheet->setCellValue('F' . $r, $row['created_at']);

            // 짝수 행 배경
            if ($i % 2 === 1) {
                $sheet->getStyle("A{$r}:F{$r}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }
        }

        // 가격 열 숫자 포맷
        $lastRow = count($products) + 1;
        $sheet->getStyle("D2:D{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        // 열 너비 자동 조정
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        ob_start();
        (new Xlsx($spreadsheet))->save('php://output');
        $body = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="products_' . date('Ymd_His') . '.xlsx"')
            ->setHeader('Pragma', 'no-cache')
            ->setBody($body);
    }

    // ─── Excel 가져오기 ──────────────────────────────────────────────────────────

    public function importExcel(): \CodeIgniter\HTTP\RedirectResponse
    {
        $file = $this->request->getFile('excel_file');

        if (! $file || ! $file->isValid()) {
            return redirect()->to('examples/csv-excel')->with('error', '파일을 선택해주세요.');
        }
        if (! in_array($file->getClientExtension(), ['xlsx', 'xls'])) {
            return redirect()->to('examples/csv-excel')->with('error', 'Excel 파일(.xlsx)만 업로드 가능합니다.');
        }

        $reader      = new XlsxReader();
        $spreadsheet = $reader->load($file->getTempName());
        $rows        = $spreadsheet->getActiveSheet()->toArray();

        $db    = \Config\Database::connect();
        $count = 0;

        foreach ($rows as $i => $row) {
            if ($i === 0 || empty($row[1])) continue;
            $db->table($this->table)->insert([
                'name'       => trim($row[1]),
                'category'   => trim($row[2]),
                'price'      => (float) $row[3],
                'stock'      => (int)   $row[4],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $count++;
        }

        return redirect()->to('examples/csv-excel')->with('success', "Excel에서 {$count}건을 가져왔습니다.");
    }

    // ─── 데이터 초기화 ───────────────────────────────────────────────────────────

    public function reset(): \CodeIgniter\HTTP\RedirectResponse
    {
        $db = \Config\Database::connect();
        $db->table($this->table)->truncate();
        $this->seedSampleData($db);

        return redirect()->to('examples/csv-excel')->with('success', '샘플 데이터로 초기화되었습니다.');
    }

    // ─── 헬퍼 ───────────────────────────────────────────────────────────────────

    private function ensureTable(\CodeIgniter\Database\BaseConnection $db): void
    {
        if (! $db->tableExists($this->table)) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'         => ['type' => 'INTEGER', 'constraint' => 11, 'auto_increment' => true],
                'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
                'category'   => ['type' => 'VARCHAR', 'constraint' => 50],
                'price'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
                'stock'      => ['type' => 'INTEGER', 'constraint' => 11, 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addPrimaryKey('id');
            $forge->createTable($this->table);
            $this->seedSampleData($db);
        } elseif ($db->table($this->table)->countAll() === 0) {
            $this->seedSampleData($db);
        }
    }

    private function seedSampleData(\CodeIgniter\Database\BaseConnection $db): void
    {
        $now  = date('Y-m-d H:i:s');
        $data = [
            ['name' => '애플 아이폰 15',      'category' => '스마트폰', 'price' => 1250000, 'stock' => 45,  'created_at' => $now],
            ['name' => '삼성 갤럭시 S24',      'category' => '스마트폰', 'price' => 1190000, 'stock' => 62,  'created_at' => $now],
            ['name' => 'LG 그램 노트북',       'category' => '노트북',   'price' => 1890000, 'stock' => 23,  'created_at' => $now],
            ['name' => '애플 맥북 에어 M3',    'category' => '노트북',   'price' => 1690000, 'stock' => 17,  'created_at' => $now],
            ['name' => 'Sony WH-1000XM5',      'category' => '헤드폰',   'price' =>  449000, 'stock' => 88,  'created_at' => $now],
            ['name' => '삼성 QLED TV 55인치',  'category' => 'TV',       'price' => 1350000, 'stock' => 12,  'created_at' => $now],
            ['name' => '다이슨 에어랩',         'category' => '미용가전', 'price' =>  699000, 'stock' => 34,  'created_at' => $now],
            ['name' => '닌텐도 스위치 OLED',   'category' => '게임기',   'price' =>  399000, 'stock' => 56,  'created_at' => $now],
            ['name' => '아이패드 프로 12.9',   'category' => '태블릿',   'price' => 1590000, 'stock' => 29,  'created_at' => $now],
            ['name' => '로지텍 MX Master 3',   'category' => '주변기기', 'price' =>  139000, 'stock' => 102, 'created_at' => $now],
        ];
        $db->table($this->table)->insertBatch($data);
    }
}
