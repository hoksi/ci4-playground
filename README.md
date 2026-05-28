# CI4 Playground

CodeIgniter 4의 주요 기능을 **코드와 실행 결과로 함께** 배우는 예제 모음입니다.

## 특징

- **SQLite** 사용 — 별도 DB 설치 없이 바로 실행
- **Bootstrap 5 + Highlight.js** — 깔끔한 UI와 코드 하이라이팅
- **한국어 설명** — 각 예제마다 상세한 한국어 설명
- **실행 가능한 데모** — 설명과 실제 동작 결과를 한 화면에서 확인
- **41개 예제** — 기초부터 실무 패턴까지 전 범위 커버

## 예제 목록

### 핵심 기능

| # | 섹션 | 주요 내용 |
|---|------|----------|
| 1 | **라우팅** | 기본/파라미터/Named Route/HTTP 메서드/그룹/리다이렉트 |
| 2 | **컨트롤러** | Request 객체, 폼 처리, 유효성 검사, Response 타입 |
| 3 | **뷰** | 기본 렌더링, 레이아웃 시스템, 파셜, View Cell |
| 4 | **모델 & 데이터베이스** | CRUD, Query Builder, 페이지네이션, Entity 클래스 |
| 5 | **필터** | Before/After 필터, 인증 필터 패턴 (로그인/로그아웃 데모) |
| 34 | **Entity 심화** | `$casts`, `$datamap`, virtual property getter/setter |

### 고급 기능

| # | 섹션 | 주요 내용 |
|---|------|----------|
| 6 | **RESTful API** | JSON 응답, HTTP 상태 코드, POST API 라이브 테스트 |
| 39 | **RESTful API v2 (JWT)** | 라이브러리 없는 HS256 JWT 발급/검증, Bearer 인증 |

### 입출력 처리

| # | 섹션 | 주요 내용 |
|---|------|----------|
| 8 | **파일 업로드** | 단일/다중 업로드, 유효성 검사, 파일 관리 |
| 9 | **세션 & 쿠키** | 세션 CRUD, 플래시 데이터, 쿠키 설정/삭제 |
| 10 | **유효성 검사** | 기본 규칙, 커스텀 메시지, 인라인 규칙 |
| 11 | **HTTP 클라이언트** | CURLRequest로 외부 API 호출, GET/POST/헤더 |
| 12 | **이메일 발송** | SMTP 설정, HTML 메일, 첨부파일 |

### 아키텍처 패턴

| # | 섹션 | 주요 내용 |
|---|------|----------|
| 13 | **서비스 레이어** | Controller → Service → Model 레이어 분리 패턴 |
| 14 | **커스텀 헬퍼** | 헬퍼 함수 정의, 자동 로드 설정 |
| 15 | **캐싱** | 파일/더미 캐시, TTL, 태그 기반 캐시 |
| 16 | **다국어 (i18n)** | 언어 파일, `lang()`, 런타임 언어 전환 |
| 17 | **이벤트 시스템** | `Events::on()`, `Events::trigger()`, 우선순위 |
| 18 | **CLI 커맨드** | `BaseCommand` 상속, `spark` 명령어 등록 |
| 21 | **테스팅** | PHPUnit, Feature 테스트, Mock, CI4 테스트 헬퍼 |
| 35 | **Repository 패턴** | Interface → Repository → Controller 레이어 분리 |

### 실무 패턴

| # | 섹션 | 주요 내용 |
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
| 32 | **이미지 처리** | CI4 Image 클래스 — resize/fit(crop)/rotate, 썸네일 생성 |
| 33 | **암호화 & 해싱** | `password_hash` bcrypt, `password_verify`, 해싱 vs 암호화 |
| 36 | **Pagination 심화** | 기본 pager, AJAX 페이지 전환, 무한 스크롤 |
| 37 | **다중 DB 연결** | 런타임 DB 그룹 설정, 두 SQLite 연결 시연 |
| 40 | **큐(Queue) 시스템** | DB 기반 커스텀 큐 — push/process/retry/failed 전체 흐름 |
| 41 | **CSV/Excel 내보내기·가져오기** | 순수 PHP `fputcsv`/`fgetcsv` + PhpSpreadsheet XLSX, UTF-8 BOM 처리 |

### 실전 예제

| # | 섹션 | 주요 내용 |
|---|------|----------|
| 7 | **게시판 CRUD** | 완성형 게시판 (목록/작성/상세/수정/소프트 삭제) |
| 38 | **회원 인증 시스템** | 회원가입/로그인/로그아웃/대시보드/비밀번호 변경 |

## 빠른 시작

```bash
# 1. 저장소 클론
git clone https://github.com/hoksi/ci4-playground.git
cd ci4-playground

# 2. 의존성 설치
composer install

# 3. 환경 설정
cp env .env
# .env 파일에서 아래 값 확인/수정
# CI_ENVIRONMENT = development
# app.baseURL = 'http://localhost:8080'
# database.default.DBDriver = SQLite3
# database.default.database = database.db

# 4. 데이터베이스 초기화 및 샘플 데이터 입력
php spark migrate --all
php spark db:seed AllSeeder

# 5. 개발 서버 실행
php spark serve
# → http://localhost:8080 접속
```

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
│   ├── PlaygroundConfig.php          ← 커스텀 설정 클래스
│   └── Routes.php                    ← 전체 라우트 정의
├── Controllers/
│   ├── Home.php                      ← 메인 목차
│   └── Examples/                     ← 41개 예제 컨트롤러
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
│   └── PostService.php               ← 서비스 레이어 예제
├── Validation/
│   └── PlaygroundRules.php           ← 커스텀 유효성 검사 규칙
└── Views/
    ├── layouts/main.php              ← 공통 레이아웃 (Bootstrap 5)
    ├── home/index.php                ← 목차 페이지
    └── examples/                     ← 41개 예제 뷰
writable/
├── uploads/                          ← 업로드 파일 저장소
└── uploads_seed/                     ← 리셋 시 복원할 초기 샘플 파일
```

## 데이터 리셋

공개 서버에 배포 시 라이브 테스트로 오염된 데이터를 초기 상태로 복원할 수 있습니다.

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

```bash
# crontab -e  — 매일 새벽 3시 자동 리셋
0 3 * * * cd /var/www/playground && php spark playground:reset --quiet >> writable/logs/reset.log 2>&1
```

리셋 동작:
- **DB** — 모든 플레이그라운드 테이블 truncate 후 초기 샘플 데이터 재입력
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
| PHP | 8.2+ |

## 라이선스

MIT
