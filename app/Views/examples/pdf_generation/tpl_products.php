<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: "NotoSansKR", "DejaVu Sans", sans-serif; font-size: 11px; color: #333; }

.header { background: #1a1a2e; color: #fff; padding: 18px 24px; margin-bottom: 20px; }
.header h1 { font-size: 18px; letter-spacing: 1px; }
.header .sub { font-size: 10px; color: #aaa; margin-top: 4px; }

.meta { margin: 0 24px 16px; display: table; width: calc(100% - 48px); }
.meta-item { display: table-cell; padding: 8px 12px; background: #f4f6fb; border-left: 3px solid #dd4814; margin-right: 8px; }
.meta-item .label { font-size: 9px; color: #888; text-transform: uppercase; }
.meta-item .value { font-size: 14px; font-weight: bold; color: #1a1a2e; }

table { width: calc(100% - 48px); margin: 0 24px; border-collapse: collapse; }
thead tr { background: #1a1a2e; color: #fff; }
thead th { padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
tbody tr:nth-child(even) { background: #f9fafb; }
tbody tr:nth-child(odd)  { background: #fff; }
tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
.text-right { text-align: right; }
.text-center { text-align: center; }

.badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: bold; }
.badge-blue   { background: #dbeafe; color: #1e40af; }
.badge-green  { background: #dcfce7; color: #166534; }
.badge-orange { background: #ffedd5; color: #9a3412; }
.badge-purple { background: #ede9fe; color: #6b21a8; }

.footer { margin-top: 20px; padding: 10px 24px; border-top: 2px solid #eee; font-size: 9px; color: #aaa; }
.total-row td { font-weight: bold; background: #1a1a2e; color: #fff; font-size: 12px; }
</style>
</head>
<body>

<div class="header">
    <h1>Product List Report</h1>
    <div class="sub">CI4 Playground &mdash; <?= esc($generated) ?></div>
</div>

<div class="meta">
    <div class="meta-item" style="width:33%">
        <div class="label">Total Products</div>
        <div class="value"><?= count($products) ?></div>
    </div>
    <div class="meta-item" style="width:33%">
        <div class="label">Total Value</div>
        <div class="value">&#8361; <?= number_format($total) ?></div>
    </div>
    <div class="meta-item" style="width:33%">
        <div class="label">Generated</div>
        <div class="value" style="font-size:11px"><?= date('Y/m/d') ?></div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:8%">#</th>
            <th style="width:35%">Product Name</th>
            <th style="width:20%">Category</th>
            <th style="width:17%" class="text-right">Price (KRW)</th>
            <th style="width:10%" class="text-center">Stock</th>
        </tr>
    </thead>
    <tbody>
    <?php $categories = ['전자제품'=>'blue','의류'=>'purple','식품'=>'green','도서'=>'orange']; ?>
    <?php foreach ($products as $i => $p): ?>
    <?php $cat = $p['category'] ?? '기타'; $color = $categories[$cat] ?? 'blue'; ?>
    <tr>
        <td class="text-center" style="color:#999"><?= $i + 1 ?></td>
        <td><?= esc($p['name']) ?></td>
        <td><span class="badge badge-<?= $color ?>"><?= esc($cat) ?></span></td>
        <td class="text-right">&#8361; <?= number_format((int)$p['price']) ?></td>
        <td class="text-center"><?= (int)$p['stock'] ?></td>
    </tr>
    <?php endforeach ?>
    <tr class="total-row">
        <td colspan="3" class="text-right">TOTAL</td>
        <td class="text-right">&#8361; <?= number_format($total) ?></td>
        <td></td>
    </tr>
    </tbody>
</table>

<div class="footer">
    This document was generated automatically by CI4 Playground &bull; <?= esc($generated) ?>
    &bull; <strong>Note:</strong> Korean characters require a CJK font (e.g. NotoSansKR).
</div>

</body>
</html>
