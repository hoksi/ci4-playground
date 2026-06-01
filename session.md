# CI4 Playground — 세션 인계 문서

> 작성일: 2026-05-28  
> 현재 브랜치: `develop`

---

## 브랜치 전략 (중요)

- **모든 작업은 `develop` 브랜치에서 직접 커밋**한다. feature 브랜치 별도 생성 금지.
- 작업 완료 후 `develop → main` PR 작성.
- `gh pr merge` 시 `--delete-branch` 옵션 절대 사용 금지 (develop 브랜치 유지).
- 신규 작업 시작 전 GitHub 이슈 생성, 완료 시 closed 처리.

---

## 이번 세션 완료 작업

### 1. CLI 실행 결과 자동 스크롤 (PR #98 ✅ merged)
- **파일**: `app/Views/examples/cli/index.php`
- **문제**: 명령어 실행 버튼 클릭 시 폼 POST 후 페이지 상단으로 이동해 결과 확인 불편
- **수정**: 결과 카드에 `id="cli-result"` 추가 + 페이지 로드 시 자동 스크롤 JS 적용
- **오프셋**: 고정 네비게이션 바 56px + 여백 16px 적용

### 2. 이메일 디버그 정보 미표시 수정 (PR #100 ✅ merged)
- **파일**: `app/Controllers/Examples/Email.php`, `app/Views/examples/email/index.php`
- **문제**: SMTP 미설정으로 발송 실패 시 디버그 정보(헤더/제목/본문)가 표시되지 않음
- **원인**: `printDebugger()`를 `send()` 이전에 호출 → CI4 Email 클래스는 `send()` 중에 `headerStr`, `finalBody` 빌드
- **수정**: `printDebugger()` 호출을 `send()` 이후로 이동, 예외 발생 시에도 디버그 출력
- **추가**: 결과 카드 자동 스크롤 적용

### 3. 모바일 네비게이션 메뉴 미표시 수정 (PR #102 ✅ merged)
- **파일**: `app/Views/layouts/main.php`
- **문제**: 모바일에서 햄버거 버튼 클릭 시 메뉴가 보이지 않음
- **원인**: `.app-navbar { height: 56px }` 고정으로 Bootstrap collapse 확장 시 내용 잘림
- **수정**: `height` → `min-height` 변경
- **추가**: 모바일 expanded 메뉴 다크 테마 배경·텍스트·hover 색상 스타일 추가

### 4. 모바일 이전/다음 버튼 텍스트 오버플로우 수정 (PR #104 ✅ merged)
- **파일**: `app/Views/layouts/main.php`
- **문제**: 모바일에서 이전/다음 버튼 레이블이 긴 경우 버튼 영역을 벗어남
- **원인**: 버튼 내부 `<div>`에 `min-width: 0` 없어 flexbox에서 `text-overflow: ellipsis` 미동작
- **수정**: `.page-nav-btn > div { min-width: 0; overflow: hidden }` 추가
- **추가**: 576px 이하 소형 화면 패딩·폰트·아이콘 크기 축소

---

## 현재 미완료 작업

없음 — 이번 세션 모든 작업 완료.

---

## 주요 파일 현황

| 파일 | 최근 수정 내용 |
|------|--------------|
| `app/Views/layouts/main.php` | 모바일 navbar min-height, 이전/다음 버튼 overflow 수정 |
| `app/Controllers/Examples/Email.php` | printDebugger() send() 이후 호출로 이동 |
| `app/Views/examples/email/index.php` | 디버그 카드 자동 스크롤 |
| `app/Views/examples/cli/index.php` | 결과 카드 자동 스크롤 |

---

## 레이아웃 핵심 정보 (main.php)

- **네비게이션**: Bootstrap 5 `navbar-expand-lg`, 6개 카테고리 드롭다운
- **헤더 높이**: `--header-height: 56px` (CSS 변수), `body { padding-top: 56px }`
- **예제 순서**: `$allExamples` 배열 43개 항목 (이전/다음 네비게이션 기준)
- **자동 스크롤 패턴**:
  ```js
  const el = document.getElementById('result-id');
  if (el) {
      const offset = parseInt(getComputedStyle(document.documentElement)
          .getPropertyValue('--header-height')) || 56;
      window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - offset - 16, behavior: 'smooth' });
  }
  ```

---

## 이전 세션 주요 완료 작업 (참고)

- PDF 한글 폰트(NotoSansCJK KR) 정상 출력 — `resources/fonts/NotoSansKR-Regular.ttf`, DOMPDF `file://` URI + 4개 variant 등록
- SSE 연결중 멈춤 수정 — `while (ob_get_level() > 0) ob_end_clean()` + `ob_implicit_flush(true)`
- 상단 드롭다운 네비게이션 전환 (좌측 사이드바 → 상단 Bootstrap navbar)
- `getMethod() === 'post'` → `$this->request->is('post')` 전체 통일
- PHPUnit `--no-coverage --do-not-cache-result` 플래그 추가 (배포 서버 권한 오류 방지)
