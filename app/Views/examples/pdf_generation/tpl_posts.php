<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #333; }

.header { border-bottom: 3px solid #dd4814; padding: 16px 24px; margin-bottom: 20px; display: table; width: 100%; }
.header h1 { font-size: 18px; color: #1a1a2e; display: table-cell; }
.header .date { display: table-cell; text-align: right; font-size: 10px; color: #999; vertical-align: bottom; }

table { width: calc(100% - 48px); margin: 0 24px; border-collapse: collapse; }
thead th { background: #1a1a2e; color: #fff; padding: 8px 10px; font-size: 10px; text-transform: uppercase; text-align: left; }
tbody tr:nth-child(even) { background: #f9fafb; }
tbody td { padding: 8px 10px; border-bottom: 1px solid #eee; vertical-align: top; }
.title { font-weight: bold; font-size: 11px; }
.preview { font-size: 10px; color: #666; margin-top: 2px; }
.text-center { text-align: center; }
.text-right  { text-align: right; }

.footer { margin-top: 16px; padding: 10px 24px; border-top: 1px solid #eee; font-size: 9px; color: #aaa; }
</style>
</head>
<body>

<div class="header">
    <h1>Board Posts Report</h1>
    <div class="date">Generated: <?= esc($generated) ?></div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:8%" class="text-center">#</th>
            <th style="width:52%">Title / Preview</th>
            <th style="width:20%">Author</th>
            <th style="width:20%" class="text-right">Date</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($posts)): ?>
    <tr><td colspan="4" class="text-center" style="color:#999;padding:20px">No posts found.</td></tr>
    <?php else: ?>
    <?php foreach ($posts as $i => $p): ?>
    <tr>
        <td class="text-center" style="color:#999"><?= $i + 1 ?></td>
        <td>
            <div class="title"><?= esc($p['title']) ?></div>
            <?php if (!empty($p['content'])): ?>
            <div class="preview"><?= esc(mb_strimwidth(strip_tags($p['content']), 0, 60, '...')) ?></div>
            <?php endif ?>
        </td>
        <td><?= esc($p['author'] ?? '—') ?></td>
        <td class="text-right"><?= isset($p['created_at']) ? date('Y-m-d', strtotime($p['created_at'])) : '—' ?></td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>

<div class="footer">
    Total <?= count($posts) ?> post(s) &bull; <?= esc($generated) ?> &bull;
    Note: Korean characters require a CJK font (e.g. NotoSansKR).
</div>

</body>
</html>
