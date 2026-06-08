# CI4 Playground

CodeIgniter 4의 주요 기능을 **코드와 실행 결과로 함께** 배우는 예제 모음입니다.

## 특징

- **SQLite** — 별도 DB 설치 없이 바로 실행
- **Bootstrap 5 + Highlight.js** — 깔끔한 UI와 코드 하이라이팅
- **한국어 설명** — 각 예제마다 상세한 한국어 설명
- **실행 가능한 데모** — 설명과 실제 동작 결과를 한 화면에서 확인
- **55개 예제** — 기초부터 실무 패턴까지 전 범위 커버
- **드롭다운 네비게이션** — 8개 카테고리 + 예제 하단 이전/다음 버튼

## 예제 목록

### 핵심 기능

| # | 예제 | 주요 내용 |
|---|------|----------|
| 1 | **라우팅** | 기본/파라미터/Named Route/HTTP 메서드/그룹/리다이렉트 |
| 2 | **컨트롤러** | Request 객체, 폼 처리, 유효성 검사, Response 타입 |
| 3 | **뷰** | 기본 렌더링, 레이아웃 시스템, 파셜, View Cell |
| 4 | **모델 & 데이터베이스** | CRUD, Query Builder, 페이지네이션, Entity 클래스 |
| 5 | **필터** | Before/After 필터, 인증 필터 패턴 (로그인/로그아웃 데모) |
| 34 | **Entity 심화** | `$casts`, `$datamap`, virtual property getter/setter |

### 고급 기능

| # | 예제 | 주요 내용 |
|---|------|----------|
| 6 | **RESTful API** | JSON 응답, HTTP 상태 코드, POST API 라이브 테스트 |
| 39 | **RESTful API v2 (JWT)** | 라이브러리 없는 HS256 JWT 발급/검증, Bearer 인증 |

### 입출력 처리

| # | 예제 | 주요 내용 |
|---|------|----------|
| 8 | **파일 업로드** | 단일/다중 업로드, 유효성 검사, 파일 관리 |
| 47 | **파일 업로드 심화** | Drag & Drop, FileReader 미리보기, XHR 업로드 진행률, AJAX 업로드·삭제 |
| 48 | **TinyMCE 에디터** | 리치 텍스트 편집, 이미지 업로드, HTML 저장·출력, jsDelivr 무료 연동 |
| 9 | **세션 & 쿠키** | 세션 CRUD, 플래시 데이터, 쿠키 설정/삭제 |
| 10 | **유효성 검사** | 기본 규칙, 커스텀 메시지, 인라인 규칙 |
| 11 | **HTTP 클라이언트** | CURLRequest로 외부 API 호출, GET/POST/헤더 |
| 12 | **이메일 발송** | SMTP 설정, HTML 메일, 첨부파일 |

### 아키텍처 패턴

| # | 예제 | 주요 내용 |
|---|------|----------|
| 13 | **서비스 레이어** | Controller → Service → Model 레이어 분리 패턴 |
| 35 | **Repository 패턴** | Interface → Repository → Controller 레이어 분리 |
| 14 | **커스텀 헬퍼** | 헬퍼 함수 정의, 자동 로드 설정 |
| 15 | **캐싱** | 파일/더미 캐시, TTL, 태그 기반 캐시 |
| 16 | **다국어 (i18n)** | 언어 파일, `lang()`, 런타임 언어 전환 |
| 17 | **이벤트 시스템** | `Events::on()`, `Events::trigger()`, 우선순위 |
| 18 | **CLI 커맨드** | `BaseCommand` 상속, `spark` 명령어 등록 |
| 21 | **테스팅** | PHPUnit, Feature 테스트, Mock, CI4 테스트 헬퍼 |

### 실무 패턴

| # | 예제 | 주요 내용 |
|---|------|----------|
| 22 | **DB 트랜잭션** | `transStart/Complete/Rollback`, 중첩 트랜잭션 |
| 23 | **로깅** | `log_message()`, PSR-3 레벨, 로그 파일 뷰어 |
| 24 | **예외 처리** | `PageNotFoundException`, 전역 핸들러, try/catch |
| 25 | **Throttler** | 토큰 버킷 속도 제한, 요청 횟수 제한 데모 |
| 26 | **Model 콜백** | `beforeInsert`, `afterFind`, 비밀번호 해싱 자동화 |
| 27 | **Config 환경 분리** | `BaseConfig` 서브클래스, `.env` 오버라이드 |
| 28 | **유효성 검사 고급** | 커스텀 규칙 클래스, 규칙 그룹, 조건부 검사 |
| 29 | **API 인증** | API Key 발급/검증/취소, Bearer 토큰 필터 |
| 30 | **Security 클래스** | `esc()` 컨텍스트별 이스케이프, `sanitizeFilename()`, CSRF |
| 31 | **Query Builder 고급** | JOIN, 서브쿼리, GROUP BY/HAVING, RawSql |
| 36 | **Pagination 심화** | 기본 pager, AJAX 페이지 전환, 무한 스크롤 |
| 37 | **다중 DB 연결** | 런타임 DB 그룹 설정, 두 SQLite 연결 시연 |
| 32 | **이미지 처리** | CI4 Image 클래스 — resize/fit(crop)/rotate, 썸네일 생성 |
| 33 | **암호화 & 해싱** | `password_hash` bcrypt, `password_verify`, 해싱 vs 암호화 |

### 실전 예제

| # | 예제 | 주요 내용 |
|---|------|----------|
| 7 | **게시판 CRUD** | 완성형 게시판 (목록/작성/상세/수정/소프트 삭제) + 3단계 스팸 감지 (규칙→SFS→AI) |
| 38 | **회원 인증** | 회원가입/로그인/로그아웃/대시보드/비밀번호 변경 |
| 40 | **큐 시스템** | DB 기반 커스텀 큐 — push/process/retry/failed 전체 흐름 |
| 41 | **CSV/Excel** | 순수 PHP `fputcsv`/`fgetcsv` + PhpSpreadsheet XLSX, UTF-8 BOM 처리 |
| 42 | **CI4 공식 Queue** | `codeigniter4/queue` — Database 핸들러, 우선순위, 재시도 |
| 43 | **Task Scheduler** | `codeigniter4/tasks` — closure/command/shell 스케줄, cron 연동 |
| 44 | **PDF 생성** | `dompdf/dompdf` — HTML→PDF, 상품보고서·인보이스·인라인보기·다운로드, 한글(NotoSansCJK KR) 폰트 내장 |
| 55 | **스팸 관리** | AI 학습 키워드 DB 관리, StopForumSpam IP 평판 조회, 실시간 스팸 테스트 패널 |

### AI & 실시간

| # | 예제 | 주요 내용 |
|---|------|----------|
| 45 | **Server-Sent Events** | SSE 실시간 스트림 — EventSource, 커스텀 이벤트, 자동 재연결, 실시간 큐 모니터링 |
| 46 | **알림 시스템** | DB 기반 알림 생성·읽음·전체 읽음 + SSE 실시간 미읽음 배지 |
| 51 | **동기화 에디터** | SSE + AJAX 실시간 공유 에디터 — 다중 클라이언트 동기화, 충돌 감지, `Last-Event-ID` 재연결 |
| 52 | **챗봇** | Groq AI(LLaMA 3.1 8B) 연동 — 키워드 자동 감지 웹 검색(Serper), 대화 컨텍스트 유지, 출처 링크 표시 |
| 53 | **고양이 키우기** | 다마고찌 스타일 AI 게임 — 시간 경과 상태 감소, 레벨/경험치, 성장 단계(아기→성묘→노령묘), 상태 히스토리 차트, Groq AI 반응 |

### 데이터 & 시각화

| # | 예제 | 주요 내용 |
|---|------|----------|
| 49 | **AG Grid** | AG Grid Community + CI4 JSON API — 클라이언트/서버 사이드 정렬·필터·페이지네이션·CSV |
| 50 | **AJAX 페이지네이션** | 검색·정렬·페이지당 건수 + `pushState`/`popstate` URL 상태 유지 |
| 54 | **차트 (Chart.js)** | Chart.js + CI4 JSON API — 꺾은선·막대·도넛·복합 차트, 지연 로드 |

## 빠른 시작

```bash
# 1. 저장소 클론
git clone https://github.com/hoksi/ci4-playground.git
cd ci4-playground

# 2. 의존성 설치
composer install

# 3. 환경 설정 ★ 필수
cp .env.example .env
# 로컬 개발: app.baseURL = 'http://localhost:8080' 확인
# 공개 서버: app.baseURL 을 실제 도메인으로 변경

# 4. 데이터베이스 초기화 및 샘플 데이터 입력
php spark migrate --all
php spark db:seed AllSeeder

# 5. 개발 서버 실행
php spark serve
# → http://localhost:8080 접속
```

> SQLite를 사용하므로 별도 DB 설치 없이 바로 실행됩니다.

## AI 기능 설정 (Groq)

챗봇(#52), 고양이 키우기(#53), 게시판 스팸 감지(#7/#55) 예제는 **Groq API**를 사용합니다.
API 키 없이도 기본 동작하지만, 키를 설정하면 AI 반응이 활성화됩니다.

### 1. API 키 발급

1. [console.groq.com](https://console.groq.com) 접속 후 무료 가입
2. **API Keys** 메뉴 → **Create API Key**
3. 생성된 키 복사 (`gsk_` 로 시작)

### 2. .env 설정

```bash
# .env 파일에 추가
GROQ_API_KEY = gsk_여기에_키_입력
```

### 3. 사용 모델

| 모델 | 용도 |
|------|------|
| `llama-3.1-8b-instant` | 챗봇, 고양이 키우기 (빠른 응답) |

> Groq 무료 티어는 모델별로 RPM(분당 요청)·RPD(일 요청)·TPM(분당 토큰) 제한이 다릅니다.
> 정확한 한도는 **console.groq.com → Settings → Limits** 에서 확인하세요.

---

## 스팸 감지 설정

게시판(#7) 스팸 감지는 **3단계 하이브리드** 방식으로 동작합니다.

```
1단계: 규칙 기반   → 내장 키워드 + AI 학습 키워드 DB (무료, 즉시)
2단계: StopForumSpam → 알려진 스패머 IP 평판 DB 대조 (무료, 30분 캐시)
3단계: Groq AI    → 불확실 구간(점수 31~69)만 호출 (비용 최소화)
```

- AI가 스팸을 확정하면 핵심 키워드를 자동 추출해 DB에 저장 → 시간이 지날수록 1단계 적중률 상승
- `/examples/spam-admin` 에서 학습된 키워드 관리 및 실시간 테스트 가능
- **Groq API 키 필요** (키 미설정 시 규칙+SFS 2단계까지만 동작)

---

## 웹 검색 기능 설정 (Serper)

챗봇(#52) 예제는 질문에 **최신·오늘·뉴스** 등 키워드가 포함되면 자동으로 Google 검색을 수행합니다.

### 1. API 키 발급

1. [serper.dev](https://serper.dev) 접속 후 무료 가입 (2,500회/월 무료)
2. Dashboard → API Key 복사

### 2. .env 설정

```bash
SERPER_API_KEY = 발급받은_키_입력
```

### 3. 검색 트리거 키워드

`최신`, `오늘`, `현재`, `지금`, `뉴스`, `날씨`, `주가`, `환율`, `최근`, `요즘` 및 연도 패턴(2024, 2025...)

> 동일한 검색어는 10분간 캐시되어 API 호출 횟수를 절약합니다.

## 정적 분석 (PHPStan)

PHPStan 레벨 3으로 코드 품질을 검사합니다.

```bash
./vendor/bin/phpstan analyse --memory-limit=512M
```

> 오류 0개 기준을 유지합니다. 새 컨트롤러 추가 시 반드시 실행하세요.

## 요구 사항

- **PHP 8.2** 이상
- **Composer**
- PHP 확장: `intl`, `mbstring`, `sqlite3`, `xml`, `curl`, `gd`

Ubuntu/Debian 설치 예시:

```bash
sudo apt install php8.3 php8.3-cli php8.3-mbstring php8.3-xml \
     php8.3-curl php8.3-intl php8.3-zip php8.3-sqlite3 php8.3-gd
```

## 프로젝트 구조

```
app/
├── Commands/
│   ├── PlaygroundReset.php           ← 전체 리셋 커맨드 (DB·파일·캐시)
│   ├── PlaygroundSeed.php            ← 샘플 데이터 시드 커맨드
│   └── PlaygroundStats.php           ← 통계 커맨드
├── Config/
│   ├── Pager.php                     ← Bootstrap 5 커스텀 페이저 템플릿 등록
│   ├── PlaygroundConfig.php          ← 커스텀 설정 클래스
│   └── Routes.php                    ← 전체 라우트 정의
├── Controllers/
│   ├── Home.php                      ← 메인 목차
│   └── Examples/                     ← 54개 예제 컨트롤러
├── Database/
│   ├── Migrations/                   ← 테이블 마이그레이션
│   └── Seeds/                        ← 샘플 데이터 시더
├── Entities/
│   ├── Post.php                      ← 게시글 Entity
│   └── UserEntity.php                ← Entity 심화 예제
├── Filters/
│   ├── AuthFilter.php                ← 인증 필터
│   └── ApiKeyFilter.php              ← API Key 필터
├── Interfaces/
│   └── PostRepositoryInterface.php   ← Repository 인터페이스
├── Jobs/
│   ├── BaseJob.php                   ← 잡 추상 클래스
│   ├── EmailNotificationJob.php      ← 이메일 잡
│   ├── DataProcessJob.php            ← 데이터 처리 잡
│   └── ReportGenerateJob.php         ← 보고서 생성 잡
├── Libraries/
│   └── QueueManager.php              ← 커스텀 큐 매니저
├── Models/
│   ├── PostModel.php
│   ├── AuthUserModel.php
│   └── UserCallbackModel.php
├── Repositories/
│   └── PostRepository.php            ← Repository 패턴 구현체
├── Services/
│   ├── PostService.php               ← 서비스 레이어 예제
│   └── SpamChecker.php               ← 3단계 스팸 감지 (규칙→SFS→AI)
├── Validation/
│   └── PlaygroundRules.php           ← 커스텀 유효성 검사 규칙
└── Views/
    ├── layouts/main.php              ← 공통 레이아웃 — 드롭다운 네비게이션, 이전/다음 버튼
    ├── pager/bootstrap_full.php      ← Bootstrap 5 커스텀 페이저 템플릿
    ├── home/index.php                ← 목차 페이지
    └── examples/                     ← 54개 예제 뷰
resources/
└── fonts/
    └── NotoSansKR-Regular.ttf       ← 한글 PDF 폰트 (NotoSansCJK KR)
writable/
├── fonts/                            ← DOMPDF UFM 메트릭 캐시 (런타임 생성, .gitignore)
├── uploads/                          ← 업로드 파일 저장소
│   ├── advanced/                     ← 파일 업로드 심화
│   └── tinymce/                      ← TinyMCE 이미지 업로드
└── uploads_seed/                     ← 리셋 시 복원할 초기 샘플 파일
```

## 데이터 리셋

공개 서버 배포 시 라이브 테스트로 오염된 데이터를 초기 상태로 복원할 수 있습니다.

### 수동 리셋

```bash
# DB + 업로드 파일 + 캐시 전체 리셋
php spark playground:reset

# DB 데이터만 리셋
php spark playground:reset --db-only

# 업로드 파일만 리셋
php spark playground:reset --files-only
```

### 자동 리셋 (cron)

#### 1. PHP 실행 경로 확인

```bash
which php
# 예: /usr/bin/php  또는  /usr/bin/php8.3
```

#### 2. 로그 디렉터리 생성 및 권한 설정

```bash
mkdir -p /var/www/playground/writable/logs
chmod 775 /var/www/playground/writable/logs
chown www-data:www-data /var/www/playground/writable/logs
```

#### 3. crontab 등록

```bash
sudo crontab -u www-data -e
```

아래 줄을 추가합니다:

```
# CI4 Playground — 매일 새벽 3시 전체 리셋
0 3 * * * cd /var/www/playground && /usr/bin/php spark playground:reset --quiet >> writable/logs/reset.log 2>&1
```

> `0 */6 * * *` — 6시간마다  /  `0 3 * * 1` — 매주 월요일 새벽 3시

#### 4. 등록 확인 및 즉시 테스트

```bash
sudo crontab -u www-data -l
cd /var/www/playground && /usr/bin/php spark playground:reset
```

#### 5. 로그 로테이션 (선택)

`/etc/logrotate.d/ci4-playground` 파일 생성:

```
/var/www/playground/writable/logs/reset.log {
    weekly
    rotate 4
    compress
    missingok
    notifempty
}
```

---

리셋 동작:
- **DB** — 모든 테이블 truncate 후 초기 샘플 데이터 재입력
- **업로드 파일** — `writable/uploads/` 삭제 후 `writable/uploads_seed/` 에서 복원
- **캐시** — `writable/cache/` 전체 삭제

## 기술 스택

| 구분 | 내용 |
|------|------|
| Framework | CodeIgniter 4.7.3 |
| Database | SQLite3 (설치 불필요) |
| CSS | Bootstrap 5.3 + Bootstrap Icons |
| Syntax Highlight | Highlight.js 11 (github-dark 테마) |
| Excel | PhpSpreadsheet 5.x |
| Queue | codeigniter4/queue 1.x |
| Task Scheduler | codeigniter4/tasks 1.x |
| PDF | dompdf/dompdf 2.x |
| 리치 텍스트 | TinyMCE 7 (jsDelivr, GPL 라이선스) |
| 데이터 그리드 | AG Grid Community 33 (jsDelivr) |
| 차트 | Chart.js 4 (jsDelivr) |
| AI API | Groq (LLaMA 3.1 8B, 무료 티어) |
| 웹 검색 | Serper (Google 검색, 무료 2,500회/월) |
| 스팸 감지 | StopForumSpam (IP 평판 DB, 무료) + Groq AI |
| 정적 분석 | PHPStan 2.x (레벨 3) |
| PHP | 8.2+ |
| 고성능 서버 | FrankenPHP 1.x (선택, `php spark worker:install`) |

## 라이선스

MIT
