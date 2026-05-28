# 배포 업데이트 마이그레이션 가이드

이미 배포된 CI4 Playground 서버를 최신 버전으로 업데이트하는 절차를 설명합니다.

---

## 변경 사항 요약

| 버전 기준 | 변경 내용 |
|-----------|-----------|
| #41 CSV/Excel | `playground_products` 테이블 추가, `phpoffice/phpspreadsheet ^5.7` 의존성 추가 |
| #42 CI4 공식 Queue | `codeigniter4/queue ^1.0` 의존성 추가, `custom_queue_jobs` / `custom_queue_failed_jobs` / `queue_jobs`(공식) / `queue_jobs_failed`(공식) 테이블 추가 |
| 환경 파일 | `env` → `.env.example` 이름 변경 (기존 `.env`는 영향 없음) |

---

## 1. 현재 배포 상태 확인

업데이트 전에 마이그레이션 상태를 확인합니다.

```bash
cd /var/www/playground
php spark migrate:status
```

출력에서 `CodeIgniter\Queue` 네임스페이스 항목이 있는지 확인하세요.

**정상 상태 (업데이트 완료된 서버)**
```
| Namespace         | Filename                       | ...
| App               | CreateQueueTables              | ...
| App               | RenameCustomQueueTables        | ...
| App               | CreateOfficialQueueJobsTable   | ...
| CodeIgniter\Queue | AddQueueTables                 | ...  ← 이 줄이 있어야 함
| CodeIgniter\Queue | AddPriorityField               | ...  ← 이 줄이 있어야 함
```

결과에 따라 아래 **시나리오**를 선택하세요.

---

## 2. 공통 업데이트 절차

시나리오와 무관하게 모든 배포에서 공통으로 실행합니다.

### 2-1. DB 백업 (필수)

```bash
# SQLite 사용 시
cp writable/database.db writable/database.db.bak.$(date +%Y%m%d%H%M%S)
```

### 2-2. 코드 업데이트

```bash
git pull origin main
```

### 2-3. 의존성 설치

```bash
composer install --no-dev --optimize-autoloader
# 신규 패키지: codeigniter4/queue ^1.0, phpoffice/phpspreadsheet ^5.7
```

### 2-4. 환경 파일 확인

```bash
# 기존 .env가 있으면 영향 없음 — 확인만
ls -la .env .env.example
```

> `env` 파일로 구동 중이었다면 `.env`로 복사합니다:
> ```bash
> cp env .env   # (이미 .env가 있으면 건너뜀)
> ```

---

## 3. 시나리오별 DB 마이그레이션

### 시나리오 A — 최초 배포 (기존 DB 없음)

```bash
php spark migrate --all
php spark db:seed AllSeeder
```

완료 후 [확인 절차](#4-마이그레이션-완료-확인)로 이동하세요.

---

### 시나리오 B — `CodeIgniter\Queue` 마이그레이션이 없는 기존 배포 ⚠️

`migrate:status`에 `CodeIgniter\Queue` 네임스페이스 항목이 **없는** 경우입니다.

**원인**: `composer install` 전에 `php spark migrate --all`을 실행했거나, `codeigniter4/queue` 패키지가 없는 상태에서 먼저 마이그레이션이 실행된 경우입니다. 이 상태에서 `migrate --all`을 추가로 실행하면 패키지 마이그레이션(`AddQueueTables` 등)이 기존 `queue_jobs` 테이블과 충돌해 오류가 발생합니다.

```
SYSTEMPATH/Database/BaseConnection.php at line 863
DatabaseException: ...
```

**해결 방법 — 전체 롤백 후 재실행**

```bash
# 1. 기존 마이그레이션 전체 롤백
php spark migrate:rollback --all

# 2. composer install로 패키지 최신화 확인
composer install --no-dev --optimize-autoloader

# 3. 올바른 순서로 전체 마이그레이션 재실행
#    (codeigniter4/queue 패키지 마이그레이션이 먼저 실행됨)
php spark migrate --all

# 4. 샘플 데이터 재입력
php spark db:seed AllSeeder
```

완료 후 `migrate:status`에 `CodeIgniter\Queue` 항목이 포함되어 있는지 확인하세요.

---

### 시나리오 C — `CodeIgniter\Queue` 마이그레이션은 있지만 `queue_jobs` 스키마 오류 ⚠️

`migrate:status`에 `CodeIgniter\Queue` 항목이 있음에도 공식 Queue 예제에서 오류가 발생하는 경우입니다.

> 해당 여부 확인:
> ```bash
> sqlite3 writable/database.db "PRAGMA table_info(queue_jobs);"
> ```
> 출력에 `job_class` 컬럼이 보이면 이 시나리오입니다.

**해결 방법 — 충돌 테이블만 수동 처리**

```bash
# 1. queue_jobs 테이블 교체
sqlite3 writable/database.db "DROP TABLE IF EXISTS queue_jobs;"

# 2. migrations 테이블에서 관련 레코드 삭제
sqlite3 writable/database.db "DELETE FROM migrations WHERE class LIKE '%OfficialQueueJobs%' OR class LIKE '%AddQueueTables%' OR class LIKE '%AddPriorityField%';"

# 3. 마이그레이션 재실행
php spark migrate --all
```

---

## 4. 마이그레이션 완료 확인

```bash
# 마이그레이션 상태 확인
php spark migrate:status

# 테이블 목록 확인 (아래 6개가 모두 있어야 함)
sqlite3 writable/database.db ".tables"
```

정상 상태의 테이블 목록:

| 테이블 | 용도 |
|--------|------|
| `queue_jobs` | 공식 codeigniter4/queue — 대기/처리 중 잡 |
| `queue_jobs_failed` | 공식 codeigniter4/queue — 실패 잡 |
| `custom_queue_jobs` | #40 커스텀 큐 — 대기/처리 중 잡 |
| `custom_queue_failed_jobs` | #40 커스텀 큐 — 실패 잡 |
| `playground_products` | #41 CSV/Excel 예제용 상품 데이터 |
| 기존 테이블들 | posts, accounts, api_keys 등 |

---

## 5. 캐시 클리어

```bash
php spark cache:clear
```

---

## 6. 동작 확인

```bash
# 개발 서버 기동 후 주요 예제 URL 접속
php spark serve

# 확인 포인트
# - http://localhost:8080/                       홈 목차 (42개 예제 표시)
# - http://localhost:8080/examples/csv-excel     CSV/Excel 예제 (#41)
# - http://localhost:8080/examples/official-queue  공식 Queue 예제 (#42)
# - http://localhost:8080/examples/queue         커스텀 Queue 예제 (#40)
```

---

## 7. 문제 해결

### `queue_jobs` 관련 NOT NULL 오류

```
NOT NULL constraint failed: queue_jobs.job_class
```

→ `queue_jobs` 테이블이 공식 스키마가 아닙니다. [시나리오 B](#시나리오-b--40-커스텀-큐가-있는-기존-배포-)의 방법 2를 실행하세요.

### `composer install` 실패 — 패키지 없음

```bash
composer require codeigniter4/queue:^1.0
composer require phpoffice/phpspreadsheet:^5.7
```

### 마이그레이션이 이미 실행됨(Already up to date) 인데 테이블이 없음

migration 기록 테이블(`migrations`)에 완료로 찍혀 있지만 실제 테이블이 없는 경우:

```bash
# 해당 마이그레이션 레코드 삭제 후 재실행
sqlite3 writable/database.db "DELETE FROM migrations WHERE class LIKE '%QueueJobs%' OR class LIKE '%RenameCustomQueue%';"
php spark migrate --all
```

---

## 8. 자동화 스크립트

cron 등으로 무중단 업데이트를 자동화하려면 아래 스크립트를 참고하세요.

```bash
#!/bin/bash
# update.sh — CI4 Playground 무중단 업데이트
set -e

APP_DIR="/var/www/playground"
cd "$APP_DIR"

echo "[$(date)] 업데이트 시작"
cp writable/database.db "writable/database.db.bak.$(date +%Y%m%d%H%M%S)"
git pull origin main
composer install --no-dev --optimize-autoloader --quiet
php spark migrate --all
php spark cache:clear
echo "[$(date)] 업데이트 완료"
```
