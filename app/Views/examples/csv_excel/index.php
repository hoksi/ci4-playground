<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center mb-4">
    <div>
        <h2 class="mb-1">CSV / Excel 내보내기·가져오기</h2>
        <p class="text-muted mb-0">순수 PHP <code>fputcsv</code>/<code>fgetcsv</code>와 <strong>PhpSpreadsheet</strong>로 CSV·XLSX 파일을 내보내고 가져오는 실무 패턴</p>
    </div>
</div>

<!-- 알림 메시지 -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- 통계 카드 -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#e7f0ff;">
                    <i class="bi bi-table fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-primary"><?= $count ?></div>
                    <div class="text-muted small">총 상품 수</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#e8f5e9;">
                    <i class="bi bi-tags fs-4 text-success"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-success"><?= $categories ?></div>
                    <div class="text-muted small">카테고리 수</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#e0f7fa;">
                    <i class="bi bi-file-earmark-spreadsheet fs-4 text-info"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-info">CSV + XLSX</div>
                    <div class="text-muted small">지원 포맷</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 탭 -->
<ul class="nav nav-tabs mb-4" id="mainTab">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-demo">
            <i class="bi bi-play-circle me-1"></i>라이브 데모
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-csv-code">
            <i class="bi bi-filetype-csv me-1"></i>CSV 코드
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-excel-code">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel 코드
        </a>
    </li>
</ul>

<div class="tab-content">

    <!-- ── 라이브 데모 탭 ──────────────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-demo">
        <div class="row g-4">

            <!-- 내보내기 -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-download me-2"></i><strong>내보내기 (Export)</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">현재 DB에 있는 <?= $count ?>건의 상품 데이터를 파일로 내보냅니다.</p>

                        <div class="d-grid gap-2">
                            <a href="<?= base_url('examples/csv-excel/export-csv') ?>" class="btn btn-outline-success">
                                <i class="bi bi-filetype-csv me-2"></i>CSV 다운로드
                                <span class="badge bg-success ms-1">UTF-8 BOM</span>
                            </a>
                            <a href="<?= base_url('examples/csv-excel/export-excel') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark-excel me-2"></i>Excel(XLSX) 다운로드
                                <span class="badge bg-primary ms-1">PhpSpreadsheet</span>
                            </a>
                        </div>

                        <hr>
                        <h6 class="text-muted mb-2"><i class="bi bi-info-circle me-1"></i>CSV vs Excel</h6>
                        <table class="table table-sm table-bordered small">
                            <thead class="table-light"><tr><th>항목</th><th>CSV</th><th>Excel(XLSX)</th></tr></thead>
                            <tbody>
                                <tr><td>구현</td><td>순수 PHP</td><td>PhpSpreadsheet</td></tr>
                                <tr><td>스타일링</td><td>불가</td><td>가능 (색상, 폰트)</td></tr>
                                <tr><td>수식</td><td>불가</td><td>가능</td></tr>
                                <tr><td>파일 크기</td><td>작음</td><td>큼</td></tr>
                                <tr><td>한글 처리</td><td>BOM 필요</td><td>기본 지원</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 가져오기 -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-upload me-2"></i><strong>가져오기 (Import)</strong>
                    </div>
                    <div class="card-body">

                        <!-- CSV 가져오기 -->
                        <h6 class="fw-bold mb-2"><i class="bi bi-filetype-csv me-1 text-success"></i>CSV 파일 가져오기</h6>
                        <form action="<?= base_url('examples/csv-excel/import-csv') ?>" method="post" enctype="multipart/form-data" class="mb-3">
                            <?= csrf_field() ?>
                            <div class="input-group">
                                <input type="file" name="csv_file" class="form-control form-control-sm" accept=".csv" required>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-upload"></i> 가져오기
                                </button>
                            </div>
                            <div class="form-text text-muted">.csv 파일만 허용 · 헤더 행(첫 줄) 자동 스킵</div>
                        </form>

                        <hr>

                        <!-- Excel 가져오기 -->
                        <h6 class="fw-bold mb-2"><i class="bi bi-file-earmark-excel me-1 text-primary"></i>Excel 파일 가져오기</h6>
                        <form action="<?= base_url('examples/csv-excel/import-excel') ?>" method="post" enctype="multipart/form-data" class="mb-3">
                            <?= csrf_field() ?>
                            <div class="input-group">
                                <input type="file" name="excel_file" class="form-control form-control-sm" accept=".xlsx,.xls" required>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-upload"></i> 가져오기
                                </button>
                            </div>
                            <div class="form-text text-muted">.xlsx 파일만 허용 · 첫 번째 시트 사용</div>
                        </form>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>가져오기는 기존 데이터에 <strong>추가</strong>됩니다.</small>
                            <a href="<?= base_url('examples/csv-excel/reset') ?>"
                               class="btn btn-outline-secondary btn-sm"
                               onclick="return confirm('샘플 데이터로 초기화하시겠습니까?')">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>초기화
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 데이터 테이블 -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <span><i class="bi bi-table me-2"></i><strong>현재 DB 데이터</strong> <span class="badge bg-secondary"><?= $count ?>건</span></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 small">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center" style="width:60px;">ID</th>
                                        <th>상품명</th>
                                        <th>카테고리</th>
                                        <th class="text-end">가격</th>
                                        <th class="text-end">재고</th>
                                        <th>등록일</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                            데이터가 없습니다. 초기화 버튼을 눌러 샘플 데이터를 추가하세요.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $p['id'] ?></td>
                                        <td><?= esc($p['name']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= esc($p['category']) ?></span></td>
                                        <td class="text-end fw-bold"><?= number_format($p['price']) ?>원</td>
                                        <td class="text-end"><?= number_format($p['stock']) ?></td>
                                        <td class="text-muted"><?= $p['created_at'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ── CSV 코드 탭 ────────────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-csv-code">
        <div class="row g-4">

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-download me-2"></i><strong>CSV 내보내기</strong>
                    </div>
                    <div class="card-body p-0">
                        <pre class="mb-0" style="max-height:480px;"><code class="language-php">// CSV 내보내기 — 순수 PHP
public function exportCsv()
{
    $products = $db->table('products')
                   ->get()->getResultArray();

    ob_start();
    $fp = fopen('php://output', 'w');

    // UTF-8 BOM: Excel에서 한글 깨짐 방지
    fputs($fp, "\xEF\xBB\xBF");

    // 헤더 행
    fputcsv($fp, ['ID', '상품명', '카테고리', '가격', '재고']);

    // 데이터 행
    foreach ($products as $row) {
        fputcsv($fp, [
            $row['id'],
            $row['name'],
            $row['category'],
            $row['price'],
            $row['stock'],
        ]);
    }
    fclose($fp);
    $body = ob_get_clean();

    return $this->response
        ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->setHeader('Content-Disposition',
            'attachment; filename="products.csv"')
        ->setBody($body);
}</code></pre>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <i class="bi bi-upload me-2"></i><strong>CSV 가져오기</strong>
                    </div>
                    <div class="card-body p-0">
                        <pre class="mb-0" style="max-height:480px;"><code class="language-php">// CSV 가져오기 — 순수 PHP
public function importCsv()
{
    $file = $this->request->getFile('csv_file');

    // 파일 유효성 검사
    if (!$file || !$file->isValid()) {
        return redirect()->back()
            ->with('error', '파일을 선택해주세요.');
    }

    $fp = fopen($file->getTempName(), 'r');

    // UTF-8 BOM 제거
    $bom = fread($fp, 3);
    if ($bom !== "\xEF\xBB\xBF") {
        rewind($fp); // BOM 없으면 처음으로
    }

    fgetcsv($fp); // 헤더 행 스킵
    $count = 0;

    while (($row = fgetcsv($fp)) !== false) {
        if (count($row) < 5) continue;

        $db->table('products')->insert([
            'name'     => trim($row[1]),
            'category' => trim($row[2]),
            'price'    => (float) $row[3],
            'stock'    => (int)   $row[4],
        ]);
        $count++;
    }
    fclose($fp);

    return redirect()->back()
        ->with('success', "{$count}건 가져오기 완료");
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- BOM 설명 -->
            <div class="col-12">
                <div class="card border-0 shadow-sm border-warning border-start border-4">
                    <div class="card-body">
                        <h6 class="fw-bold"><i class="bi bi-lightbulb text-warning me-2"></i>UTF-8 BOM이란?</h6>
                        <p class="mb-2 small">BOM(Byte Order Mark)은 파일 첫 3바이트 <code>0xEF 0xBB 0xBF</code>로, UTF-8 인코딩임을 알리는 서명입니다.</p>
                        <ul class="small mb-0">
                            <li>Excel은 BOM이 없으면 CSV를 ANSI(EUC-KR)로 읽어 <strong>한글이 깨집니다.</strong></li>
                            <li>내보낼 때 <code>fputs($fp, "\xEF\xBB\xBF")</code>로 BOM을 먼저 씁니다.</li>
                            <li>가져올 때 <code>fread($fp, 3)</code>으로 BOM 3바이트를 확인 후 스킵합니다.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ── Excel 코드 탭 ──────────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-excel-code">
        <div class="row g-4">

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-download me-2"></i><strong>Excel 내보내기 (PhpSpreadsheet)</strong>
                    </div>
                    <div class="card-body p-0">
                        <pre class="mb-0" style="max-height:520px;"><code class="language-php">use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

public function exportExcel()
{
    $products = $db->table('products')
                   ->get()->getResultArray();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('상품 목록');

    // 헤더 행 데이터
    $headers = ['ID', '상품명', '카테고리', '가격', '재고'];
    foreach ($headers as $col => $header) {
        $sheet->setCellValue(chr(65 + $col).'1', $header);
    }

    // 헤더 스타일 (파란 배경 + 흰 글자)
    $sheet->getStyle('A1:E1')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '0D6EFD'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
    ]);

    // 데이터 입력
    foreach ($products as $i => $row) {
        $r = $i + 2;
        $sheet->setCellValue('A'.$r, $row['id']);
        $sheet->setCellValue('B'.$r, $row['name']);
        $sheet->setCellValue('C'.$r, $row['category']);
        $sheet->setCellValue('D'.$r, $row['price']);
        $sheet->setCellValue('E'.$r, $row['stock']);
    }

    // 가격 열 숫자 포맷
    $sheet->getStyle('D2:D'.($i+2))
          ->getNumberFormat()
          ->setFormatCode('#,##0');

    // 열 너비 자동
    foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    ob_start();
    (new Xlsx($spreadsheet))->save('php://output');
    $body = ob_get_clean();

    return $this->response
        ->setHeader('Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->setHeader('Content-Disposition',
            'attachment; filename="products.xlsx"')
        ->setBody($body);
}</code></pre>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-upload me-2"></i><strong>Excel 가져오기 (PhpSpreadsheet)</strong>
                    </div>
                    <div class="card-body p-0">
                        <pre class="mb-0" style="max-height:280px;"><code class="language-php">use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

public function importExcel()
{
    $file = $this->request->getFile('excel_file');

    $reader = new XlsxReader();
    $spreadsheet = $reader->load($file->getTempName());

    // 첫 번째 시트를 배열로 변환
    $rows = $spreadsheet->getActiveSheet()->toArray();

    $count = 0;
    foreach ($rows as $i => $row) {
        if ($i === 0) continue; // 헤더 스킵

        $db->table('products')->insert([
            'name'     => trim($row[1]),
            'category' => trim($row[2]),
            'price'    => (float) $row[3],
            'stock'    => (int)   $row[4],
        ]);
        $count++;
    }

    return redirect()->back()
        ->with('success', "{$count}건 가져오기 완료");
}</code></pre>
                    </div>

                    <div class="card-header bg-light border-top mt-0">
                        <strong class="small">설치 방법</strong>
                    </div>
                    <div class="card-body p-0">
                        <pre class="mb-0"><code class="language-bash"># PhpSpreadsheet 설치
composer require phpoffice/phpspreadsheet

# 지원 포맷
# 읽기: xlsx, xls, csv, ods, html ...
# 쓰기: xlsx, xls, csv, ods, html, pdf ...</code></pre>
                    </div>
                </div>

                <!-- PhpSpreadsheet 주요 기능 -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <i class="bi bi-star me-2"></i><strong class="small">PhpSpreadsheet 주요 기능</strong>
                    </div>
                    <div class="card-body p-3">
                        <ul class="small mb-0">
                            <li><strong>다중 시트</strong> — <code>$spreadsheet->createSheet()</code></li>
                            <li><strong>셀 병합</strong> — <code>$sheet->mergeCells('A1:C1')</code></li>
                            <li><strong>수식</strong> — <code>$sheet->setCellValue('A1', '=SUM(B1:B10)')</code></li>
                            <li><strong>차트</strong> — <code>PhpOffice\PhpSpreadsheet\Chart\*</code></li>
                            <li><strong>이미지 삽입</strong> — <code>Drawing</code> 클래스 사용</li>
                            <li><strong>비밀번호 보호</strong> — <code>$sheet->getProtection()</code></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
