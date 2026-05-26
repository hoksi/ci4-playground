# CI4 Playground

CodeIgniter 4의 주요 기능을 **코드와 실행 결과로 함께** 배우는 예제 모음입니다.

## 특징

- **SQLite** 사용 — 별도 DB 설치 없이 바로 실행
- **Bootstrap 5 + Highlight.js** — 깔끔한 UI와 코드 하이라이팅
- **한국어 설명** — 각 예제마다 상세한 한국어 설명
- **실행 가능한 데모** — 설명과 실제 동작 결과를 한 화면에서 확인

## 예제 목록

| # | 섹션 | 주요 내용 |
|---|------|----------|
| 1 | **라우팅** | 기본/파라미터/Named Route/HTTP 메서드/그룹/리다이렉트 |
| 2 | **컨트롤러** | Request 객체, 폼 처리, 유효성 검사, Response 타입 |
| 3 | **뷰** | 기본 렌더링, 레이아웃 시스템, 파셜, View Cell |
| 4 | **모델 & 데이터베이스** | CRUD, Query Builder, 페이지네이션, Entity 클래스 |
| 5 | **필터** | Before/After 필터, 인증 필터 패턴 (로그인/로그아웃 데모) |
| 6 | **RESTful API** | JSON 응답, HTTP 상태 코드, POST API 라이브 테스트 |
| 7 | **게시판 CRUD** | 완성형 게시판 (목록/작성/상세/수정/소프트 삭제) |

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

- **PHP 8.1** 이상
- **Composer**
- PHP 확장: `intl`, `mbstring`, `sqlite3`, `xml`, `curl`

Ubuntu/Debian 설치 예시:
```bash
sudo apt install php8.3 php8.3-cli php8.3-mbstring php8.3-xml \
     php8.3-curl php8.3-intl php8.3-zip php8.3-sqlite3
```

## 프로젝트 구조

```
app/
├── Controllers/
│   ├── Home.php                      ← 메인 목차
│   └── Examples/
│       ├── Routing.php               ← 라우팅 예제
│       ├── Controllers.php           ← 컨트롤러 예제
│       ├── Views.php                 ← 뷰 예제
│       ├── Models.php                ← 모델 예제
│       ├── Filters.php               ← 필터 예제
│       ├── Api.php                   ← RESTful API 예제
│       └── Board.php                 ← 게시판 CRUD
├── Models/PostModel.php
├── Entities/Post.php
├── Filters/AuthFilter.php
├── Database/
│   ├── Migrations/..._CreatePostsTable.php
│   └── Seeds/
│       ├── PostSeeder.php
│       └── AllSeeder.php
└── Views/
    ├── layouts/main.php              ← 공통 레이아웃 (Bootstrap 5)
    ├── home/index.php                ← 목차 페이지
    └── examples/
        ├── routing/
        ├── controllers/
        ├── views/
        ├── models/
        ├── filters/
        ├── api/
        └── board/
```

## 기술 스택

| 구분 | 내용 |
|------|------|
| Framework | CodeIgniter 4.7.3 |
| Database | SQLite3 (설치 불필요) |
| CSS | Bootstrap 5.3 + Bootstrap Icons |
| Syntax Highlight | Highlight.js 11 |
| PHP | 8.1+ |

## 라이선스

MIT
