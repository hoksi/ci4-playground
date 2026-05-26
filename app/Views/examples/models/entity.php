<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">홈</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('examples/models') ?>">모델</a></li>
        <li class="breadcrumb-item active text-white">Entity</li>
    </ol></nav>
    <h1><i class="bi bi-box me-2"></i>Entity 클래스</h1>
    <p>데이터를 단순 배열이 아닌 객체로 다루며, 커스텀 메서드를 추가합니다.</p>
</div>

<div class="example-card">
    <div class="example-card-header"><h5>Entity 클래스 구조</h5></div>
    <div class="example-card-body">
        <pre><code class="language-php">// app/Entities/Post.php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Post extends Entity
{
    // 타입 자동 캐스팅
    protected $casts = [
        'id'    => 'integer',
        'views' => 'integer',
    ];

    // 커스텀 메서드 - 본문 요약
    public function getExcerpt(int $length = 100): string
    {
        return mb_strlen($this->content) > $length
            ? mb_substr($this->content, 0, $length) . '...'
            : $this->content;
    }

    // 커스텀 메서드 - 날짜 포맷
    public function getFormattedDate(): string
    {
        return $this->created_at?->format('Y-m-d H:i') ?? '';
    }
}</code></pre>
    </div>
</div>

<?php if ($post): ?>
<div class="example-card">
    <div class="example-card-header"><h5><i class="bi bi-check-circle text-success me-2"></i>실제 Entity 객체 활용 결과</h5></div>
    <div class="example-card-body">
        <div class="code-label">컨트롤러</div>
        <pre><code class="language-php">$post = (new PostModel())->first(); // Post 엔티티 반환</code></pre>

        <div class="row mt-3 g-3">
            <div class="col-md-6">
                <div class="result-box">
                    <div class="code-label">$post->id (integer 캐스팅)</div>
                    <code><?= esc($post->id) ?></code> <small class="text-muted">(<?= gettype($post->id) ?>)</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="result-box">
                    <div class="code-label">$post->title</div>
                    <code><?= esc($post->title) ?></code>
                </div>
            </div>
            <div class="col-md-6">
                <div class="result-box info">
                    <div class="code-label">$post->getExcerpt(50)</div>
                    <code><?= esc($post->getExcerpt(50)) ?></code>
                </div>
            </div>
            <div class="col-md-6">
                <div class="result-box info">
                    <div class="code-label">$post->getFormattedDate()</div>
                    <code><?= esc($post->getFormattedDate()) ?></code>
                </div>
            </div>
            <div class="col-md-6">
                <div class="result-box">
                    <div class="code-label">$post->views (integer 캐스팅)</div>
                    <code><?= esc($post->views) ?></code> <small class="text-muted">(<?= gettype($post->views) ?>)</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="result-box">
                    <div class="code-label">$post->author</div>
                    <code><?= esc($post->author) ?></code>
                </div>
            </div>
        </div>

        <div class="result-box info mt-3">
            <i class="bi bi-lightbulb me-2"></i>
            <strong>Entity vs 배열:</strong> Entity는 타입 캐스팅, 커스텀 getter/setter, 비즈니스 로직을 담을 수 있습니다.
            Model에서 <code>protected $returnType = Post::class</code>로 설정하면 자동으로 Entity를 반환합니다.
        </div>
    </div>
</div>
<?php endif; ?>

<div class="mt-3"><a href="<?= base_url('examples/models') ?>" class="demo-btn" style="background:#6f42c1;">← 모델로</a></div>

<?= $this->endSection() ?>
