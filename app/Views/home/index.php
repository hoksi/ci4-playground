<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-play-circle me-2"></i>CI4 Playground</h1>
    <p>CodeIgniter 4의 주요 기능을 코드와 실행 결과로 함께 배우는 예제 모음입니다.</p>
</div>

<!-- 섹션 카드 -->
<div class="row g-4">

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #dd4814 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#fff3ef;">
                        <i class="bi bi-sign-turn-right fs-4" style="color:#dd4814;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">라우팅</h5>
                        <small class="text-muted">Routing</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">기본 라우트, URL 파라미터, 그룹 라우팅, Named Route, HTTP 메서드 제한까지 CI4 라우팅의 모든 것을 다룹니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">GET/POST</span>
                    <span class="badge bg-light text-dark border">파라미터</span>
                    <span class="badge bg-light text-dark border">그룹</span>
                    <span class="badge bg-light text-dark border">Named Route</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/routing') ?>" class="demo-btn">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #0d6efd !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#eff6ff;">
                        <i class="bi bi-cpu fs-4" style="color:#0d6efd;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">컨트롤러</h5>
                        <small class="text-muted">Controllers</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">요청 처리, 응답 반환, Request/Response 객체 활용, RESTful 컨트롤러 구조를 예제로 설명합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">Request</span>
                    <span class="badge bg-light text-dark border">Response</span>
                    <span class="badge bg-light text-dark border">JSON</span>
                    <span class="badge bg-light text-dark border">Redirect</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/controllers') ?>" class="demo-btn" style="background:#0d6efd;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #198754 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f0fdf4;">
                        <i class="bi bi-window fs-4" style="color:#198754;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">뷰</h5>
                        <small class="text-muted">Views</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">뷰 파일 렌더링, 레이아웃 시스템, 파셜(include), View Cell을 활용한 재사용 컴포넌트를 다룹니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">Layout</span>
                    <span class="badge bg-light text-dark border">Partial</span>
                    <span class="badge bg-light text-dark border">View Cell</span>
                    <span class="badge bg-light text-dark border">esc()</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/views') ?>" class="demo-btn" style="background:#198754;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #6f42c1 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f5f0ff;">
                        <i class="bi bi-database fs-4" style="color:#6f42c1;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">모델 & 데이터베이스</h5>
                        <small class="text-muted">Models & Database</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">Model CRUD, Query Builder, 페이지네이션, Entity 클래스, 마이그레이션/시더를 실제 SQLite DB로 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">CRUD</span>
                    <span class="badge bg-light text-dark border">QueryBuilder</span>
                    <span class="badge bg-light text-dark border">Pagination</span>
                    <span class="badge bg-light text-dark border">Entity</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/models') ?>" class="demo-btn" style="background:#6f42c1;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #fd7e14 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#fff8f0;">
                        <i class="bi bi-funnel fs-4" style="color:#fd7e14;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">필터</h5>
                        <small class="text-muted">Filters</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">Before/After 필터, 인증 체크, 라우트 그룹별 필터 적용 방법을 세션 기반 예제로 보여줍니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">Before</span>
                    <span class="badge bg-light text-dark border">After</span>
                    <span class="badge bg-light text-dark border">Auth</span>
                    <span class="badge bg-light text-dark border">Session</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/filters') ?>" class="demo-btn" style="background:#fd7e14;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #0dcaf0 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f0fdff;">
                        <i class="bi bi-braces fs-4" style="color:#0dcaf0;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">RESTful API</h5>
                        <small class="text-muted">API Development</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">JSON 응답, ResourceController, API 에러 처리, Content-Type 협상을 이용한 RESTful API 개발 패턴을 다룹니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">JSON</span>
                    <span class="badge bg-light text-dark border">ResourceController</span>
                    <span class="badge bg-light text-dark border">HTTP Status</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/api') ?>" class="demo-btn" style="background:#0dcaf0;color:#000;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #20c997 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f0fdf9;">
                        <i class="bi bi-cloud-upload fs-4" style="color:#20c997;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">파일 업로드</h5>
                        <small class="text-muted">File Upload</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">단일/다중 파일 업로드, 유효성 검사, 파일 이동 및 관리 방법을 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">getFile()</span>
                    <span class="badge bg-light text-dark border">다중업로드</span>
                    <span class="badge bg-light text-dark border">확장자검사</span>
                    <span class="badge bg-light text-dark border">파일목록</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/fileupload') ?>" class="demo-btn" style="background:#20c997;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #6610f2 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f5f0ff;">
                        <i class="bi bi-archive fs-4" style="color:#6610f2;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">세션 & 쿠키</h5>
                        <small class="text-muted">Session & Cookie</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">세션 저장/읽기/삭제, Flash 데이터, 쿠키 설정과 삭제를 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">session()</span>
                    <span class="badge bg-light text-dark border">Flash</span>
                    <span class="badge bg-light text-dark border">setCookie()</span>
                    <span class="badge bg-light text-dark border">getCookie()</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/session') ?>" class="demo-btn" style="background:#6610f2;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #d63384 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#fff0f6;">
                        <i class="bi bi-shield-check fs-4" style="color:#d63384;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">유효성 검사</h5>
                        <small class="text-muted">Validation</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">내장 유효성 검사 규칙, 커스텀 에러 메시지, 폼 재입력(old value) 처리를 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">validate()</span>
                    <span class="badge bg-light text-dark border">규칙 체인</span>
                    <span class="badge bg-light text-dark border">커스텀메시지</span>
                    <span class="badge bg-light text-dark border">matches</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/validation') ?>" class="demo-btn" style="background:#d63384;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #0d6efd !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#eff6ff;">
                        <i class="bi bi-globe fs-4" style="color:#0d6efd;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">HTTP 클라이언트</h5>
                        <small class="text-muted">HTTP Client</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">CURLRequest로 외부 API에 GET/POST 요청을 보내고 JSON 응답을 처리합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">curlrequest</span>
                    <span class="badge bg-light text-dark border">GET/POST</span>
                    <span class="badge bg-light text-dark border">JSON</span>
                    <span class="badge bg-light text-dark border">쿼리파라미터</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/httpclient') ?>" class="demo-btn" style="background:#0d6efd;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #fd7e14 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#fff8f0;">
                        <i class="bi bi-envelope fs-4" style="color:#fd7e14;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">이메일 발송</h5>
                        <small class="text-muted">Email</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">HTML/텍스트 이메일 작성, SMTP 설정, 첨부 파일, 수신자 관리를 다룹니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">SMTP</span>
                    <span class="badge bg-light text-dark border">HTML메일</span>
                    <span class="badge bg-light text-dark border">첨부파일</span>
                    <span class="badge bg-light text-dark border">디버거</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/email') ?>" class="demo-btn" style="background:#fd7e14;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #495057 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f8f9fa;">
                        <i class="bi bi-layers fs-4" style="color:#495057;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">서비스 레이어</h5>
                        <small class="text-muted">Service Layer Pattern</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">비즈니스 로직을 서비스 클래스로 분리하고 Config/Services에 등록하는 패턴을 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">Services</span>
                    <span class="badge bg-light text-dark border">의존성주입</span>
                    <span class="badge bg-light text-dark border">비즈니스로직</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/servicelayer') ?>" class="demo-btn" style="background:#495057;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #198754 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f0fdf4;">
                        <i class="bi bi-tools fs-4" style="color:#198754;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">커스텀 헬퍼</h5>
                        <small class="text-muted">Custom Helpers</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">헬퍼 파일 작성, 로드, 자동로드 설정과 내장 헬퍼 활용법을 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">helper()</span>
                    <span class="badge bg-light text-dark border">format_filesize</span>
                    <span class="badge bg-light text-dark border">time_ago</span>
                    <span class="badge bg-light text-dark border">highlight</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/helper') ?>" class="demo-btn" style="background:#198754;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #ffc107 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#fffbeb;">
                        <i class="bi bi-lightning-charge fs-4" style="color:#ffc107;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">캐싱</h5>
                        <small class="text-muted">Caching</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">파일/Redis 캐시 드라이버, TTL 관리, 캐시 히트/미스를 성능 비교와 함께 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">cache()->save()</span>
                    <span class="badge bg-light text-dark border">remember()</span>
                    <span class="badge bg-light text-dark border">TTL</span>
                    <span class="badge bg-light text-dark border">히트/미스</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/cache') ?>" class="demo-btn" style="background:#ffc107;color:#000;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #0dcaf0 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f0fdff;">
                        <i class="bi bi-translate fs-4" style="color:#0dcaf0;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">다국어 (i18n)</h5>
                        <small class="text-muted">Internationalization</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">Language 파일 작성, lang() 함수, 파라미터 치환, 런타임 로케일 변경을 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">lang()</span>
                    <span class="badge bg-light text-dark border">ko/en/ja</span>
                    <span class="badge bg-light text-dark border">파라미터치환</span>
                    <span class="badge bg-light text-dark border">로케일전환</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/lang') ?>" class="demo-btn" style="background:#0dcaf0;color:#000;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #6f42c1 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f5f0ff;">
                        <i class="bi bi-bell fs-4" style="color:#6f42c1;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">이벤트 시스템</h5>
                        <small class="text-muted">Events</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">Events::on(), trigger(), 우선순위, 체인 중단을 라이브 실행 로그와 함께 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">Events::on()</span>
                    <span class="badge bg-light text-dark border">trigger()</span>
                    <span class="badge bg-light text-dark border">우선순위</span>
                    <span class="badge bg-light text-dark border">체인중단</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/events') ?>" class="demo-btn" style="background:#6f42c1;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #343a40 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f8f9fa;">
                        <i class="bi bi-terminal fs-4" style="color:#343a40;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">CLI 커맨드</h5>
                        <small class="text-muted">Spark Commands</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">BaseCommand로 커스텀 spark 명령어를 만들고 인자/옵션/테이블 출력을 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">BaseCommand</span>
                    <span class="badge bg-light text-dark border">CLI::write()</span>
                    <span class="badge bg-light text-dark border">--options</span>
                    <span class="badge bg-light text-dark border">table()</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/cli') ?>" class="demo-btn" style="background:#343a40;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #20c997 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f0fdf9;">
                        <i class="bi bi-check2-circle fs-4" style="color:#20c997;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">테스팅</h5>
                        <small class="text-muted">Testing</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">CIUnitTestCase, DatabaseTestTrait로 단위/통합 테스트를 작성하고 PHPUnit으로 실행하는 방법을 학습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">CIUnitTestCase</span>
                    <span class="badge bg-light text-dark border">DatabaseTestTrait</span>
                    <span class="badge bg-light text-dark border">PHPUnit</span>
                    <span class="badge bg-light text-dark border">단위/통합</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/testing') ?>" class="demo-btn" style="background:#20c997;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #0d6efd !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#eff6ff;">
                        <i class="bi bi-arrow-left-right fs-4" style="color:#0d6efd;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">DB 트랜잭션</h5>
                        <small class="text-muted">Database Transactions</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">계좌 이체 시나리오로 transStart/transComplete, 수동 롤백, 예외 기반 자동 롤백 패턴을 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">transStart()</span>
                    <span class="badge bg-light text-dark border">transRollback()</span>
                    <span class="badge bg-light text-dark border">try/catch</span>
                    <span class="badge bg-light text-dark border">커밋/롤백</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/transaction') ?>" class="demo-btn" style="background:#0d6efd;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm" style="border-top: 4px solid #6f42c1 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#f5f0ff;">
                        <i class="bi bi-journal-text fs-4" style="color:#6f42c1;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">로깅</h5>
                        <small class="text-muted">Logging</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">PSR-3 레벨별 log_message() 사용법, 로그 파일 관리, Logger 설정을 실습합니다.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">log_message()</span>
                    <span class="badge bg-light text-dark border">PSR-3</span>
                    <span class="badge bg-light text-dark border">FileHandler</span>
                    <span class="badge bg-light text-dark border">threshold</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <a href="<?= base_url('examples/logging') ?>" class="demo-btn" style="background:#6f42c1;">
                    <i class="bi bi-arrow-right-circle"></i> 예제 보기
                </a>
            </div>
        </div>
    </div>

    <!-- 실전 예제 -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-top: 4px solid #dc3545 !important; background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i class="bi bi-trophy fs-2" style="color:#dc3545;"></i>
                            <div>
                                <h4 class="mb-0">실전 예제: 게시판 CRUD</h4>
                                <small class="text-muted">Practical Example — Board with Create/Read/Update/Delete</small>
                            </div>
                        </div>
                        <p class="text-muted mb-0">모든 핵심 기능을 통합한 게시판 예제입니다. SQLite DB, Model, Migration, View, 라우팅이 실제 애플리케이션처럼 연결되어 동작합니다. 소스코드와 실행 결과를 함께 확인할 수 있습니다.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="<?= base_url('examples/board') ?>" class="demo-btn" style="background:#dc3545; font-size:1rem; padding:.6rem 1.4rem;">
                            <i class="bi bi-arrow-right-circle"></i> 게시판 바로가기
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- 설치 안내 -->
<div class="card border-0 shadow-sm mt-4" style="background:#1a1a2e; color:#fff;">
    <div class="card-body p-4">
        <h5 class="mb-3"><i class="bi bi-terminal me-2"></i>빠른 시작</h5>
        <pre style="background:#0d1117; border-radius:8px; padding:1rem; margin:0;"><code class="language-bash">git clone https://github.com/hoksi/ci4-playground.git
cd ci4-playground
composer install
php spark migrate --all
php spark db:seed AllSeeder
php spark serve</code></pre>
        <p class="mt-3 mb-0" style="opacity:.7; font-size:.85rem;">
            <i class="bi bi-info-circle me-1"></i>
            SQLite를 사용하므로 별도 DB 설치 없이 바로 실행됩니다. PHP 8.1+ 필요.
        </p>
    </div>
</div>

<?= $this->endSection() ?>
