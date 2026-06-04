# 챗봇 웹 검색 기능 구현 계획

## 개요

Tavily AI 검색 API와 Groq LLM을 결합해 최신 정보를 반영한 AI 챗봇을 구현합니다.
질문에 특정 키워드("최신", "오늘", "뉴스" 등)가 포함된 경우 자동으로 웹 검색을 수행하고,
검색 결과를 컨텍스트로 Groq에 전달해 정확하고 최신화된 답변을 생성합니다.

## 검색 기준 — 키워드 자동 감지

AI는 학습 시점 이후의 정보를 모르므로, 아래 키워드가 포함된 질문에 한해 자동으로 검색합니다.

```php
// 검색 트리거 키워드
$keywords = [
    '최신', '오늘', '현재', '지금', '어제',
    '이번 주', '이번 달', '뉴스', '날씨',
    '주가', '환율', '최근', '지금 몇 시',
];

// 연도 패턴 (2024, 2025, 2026 등)
$yearPattern = '/20\d{2}년?/';
```

**예시:**

| 질문 | 검색 여부 | 이유 |
|------|----------|------|
| "파이썬이 뭐야?" | ❌ | 키워드 없음 — AI가 알고 있음 |
| "오늘 환율 알려줘" | ✅ | "오늘" 감지 |
| "CI4 최신 버전은?" | ✅ | "최신" 감지 |
| "뉴스 알려줘" | ✅ | "뉴스" 감지 |
| "2026년 트렌드는?" | ✅ | 연도 패턴 감지 |

---

## 아키텍처 (RAG 패턴)

```
사용자 질문
    │
    ▼
[검색 필요 여부 판단]
  · 자동 감지: "최신", "오늘", "현재", 연도(2025, 2026) 등 키워드
  · 수동 토글: 검색 ON/OFF 스위치
    │
    ├─ 검색 필요 ──▶ [Tavily API 호출] ──▶ 검색 결과 3~5건
    │                                          │
    └─ 검색 불필요                             │
              │                               │
              ▼                               ▼
         [Groq API] ◀── 시스템 프롬프트 + 검색 컨텍스트
              │
              ▼
         AI 답변 + 출처 URL 반환
```

---

## 기술 스택

| 구분 | 선택 | 이유 |
|------|------|------|
| 검색 API | **Serper** | Google 검색 결과, 무료 2,500회/월 |
| LLM | Groq (llama-3.1-8b-instant) | 기존 챗봇과 동일 |
| 캐싱 | DB 캐시 10분 | API 호출 절약 |
| 백엔드 | CodeIgniter 4 (CI4) | 기존 프레임워크 |

---

## Serper API 설정

### 1. API 키 발급
1. [serper.dev](https://serper.dev) 접속 후 무료 가입
2. Dashboard → API Key 복사

### 2. .env 설정
```env
SERPER_API_KEY = 여기에_키_입력
```

### 3. Serper 검색 요청 예시
```
POST https://google.serper.dev/search
X-API-KEY: {api_key}
Content-Type: application/json

{
  "q": "검색 쿼리",
  "num": 5,
  "gl": "kr",
  "hl": "ko"
}
```

### 4. 응답 구조
```json
{
  "organic": [
    {
      "title": "페이지 제목",
      "link": "https://...",
      "snippet": "페이지 내용 요약"
    }
  ],
  "knowledgeGraph": {
    "title": "...",
    "description": "..."
  }
}
```

---

## 구현 상세

### DB 변경 없음
기존 `chat_messages` 테이블 그대로 사용. 검색 결과는 컨텍스트로만 활용.

### 컨트롤러 변경 (`Chat.php`)

#### 검색 필요 여부 자동 감지
```php
private function needsSearch(string $content): bool
{
    $keywords = ['최신', '오늘', '현재', '지금', '어제', '이번 주', '이번 달',
                 '뉴스', '날씨', '주가', '환율', '최근'];
    $yearPattern = '/20\d{2}년?/';

    foreach ($keywords as $kw) {
        if (str_contains($content, $kw)) return true;
    }
    return (bool) preg_match($yearPattern, $content);
}
```

#### Serper 검색 메서드 (캐시 포함)
```php
private function searchWeb(string $query): array
{
    $apiKey = trim(env('SERPER_API_KEY') ?? '');
    if ($apiKey === '') return [];

    // 10분 캐시 — 동일 쿼리는 API 재호출 없음
    $cacheKey = 'search_' . md5($query);
    $cached   = cache($cacheKey);
    if ($cached !== null) return $cached;

    try {
        $res = \Config\Services::curlrequest()->post(
            'https://google.serper.dev/search',
            [
                'headers' => [
                    'X-API-KEY'    => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'body'    => json_encode([
                    'q'   => $query,
                    'num' => 5,
                    'gl'  => 'kr',
                    'hl'  => 'ko',
                ]),
                'timeout' => 10,
            ]
        );
        $result = json_decode($res->getBody(), true) ?? [];
        cache()->save($cacheKey, $result, 600); // 10분 캐시
        return $result;
    } catch (\Throwable) {
        return [];
    }
}
```

#### Groq 프롬프트에 검색 컨텍스트 주입
```php
// Serper 응답에서 organic 결과 추출 후 컨텍스트 구성
$organic = $searchResults['organic'] ?? [];
if (! empty($organic)) {
    $context = "다음은 최신 구글 검색 결과입니다. 답변 시 참고하세요:\n\n";
    foreach (array_slice($organic, 0, 3) as $r) {
        $context .= "- [{$r['title']}] {$r['snippet']}\n";
    }
    $groqMessages[0]['content'] .= "\n\n" . $context;
}

// knowledgeGraph가 있으면 추가
$kg = $searchResults['knowledgeGraph'] ?? [];
if (! empty($kg['description'])) {
    $groqMessages[0]['content'] .= "\n지식 패널: {$kg['description']}";
}
```

#### send() 메서드 흐름
```php
public function send(): ResponseInterface
{
    // 1. 키워드 감지로 검색 여부 자동 판단
    $doSearch = $this->needsSearch($content);

    // 2. 검색 실행
    $searchResults = $doSearch ? $this->searchWeb($content) : [];
    $sources       = array_column($searchResults['organic'] ?? [], 'link');

    // 3. Groq 호출 (검색 컨텍스트 포함)
    // ... (기존 로직에 컨텍스트 주입)

    // 4. 응답에 출처 포함
    return $this->response->setJSON([
        'success'  => true,
        'user'     => $userMsg,
        'bot'      => $botMsg,
        'searched' => $doSearch,
        'sources'  => array_slice($sources, 0, 3),
    ]);
}
```

---

## UI 변경 (`chat/index.php`)

### 추가 UI 요소

1. **자동 검색 인디케이터** (키워드 감지 시 표시)
```js
const SEARCH_KEYWORDS = ['최신', '오늘', '현재', '지금', '뉴스', '날씨', '주가', '환율'];
const willSearch = SEARCH_KEYWORDS.some(kw => content.includes(kw));
if (willSearch) showTypingIndicator('🔍 웹 검색 중...');
```

2. **출처 표시** (검색된 경우 봇 메시지 하단)
```html
<div class="sources mt-1">
    <small class="text-muted"><i class="bi bi-search me-1"></i>검색 출처:</small>
    <a href="..." target="_blank" class="badge bg-light text-dark border">링크</a>
</div>
```

---

## 구현 순서

| 단계 | 작업 | 예상 시간 |
|------|------|----------|
| 1 | Tavily API 키 발급 및 `.env` 설정 | 5분 |
| 2 | `Chat.php` — `searchWeb()`, `needsSearch()` 추가 | 30분 |
| 3 | `send()` 메서드 — 검색 결합 로직 수정 | 20분 |
| 4 | 뷰 — 검색 토글, 자동 감지 표시, 출처 표시 | 30분 |
| 5 | 테스트 및 PHPStan | 10분 |

---

## 테스트 시나리오

| 질문 | 예상 동작 |
|------|----------|
| "오늘 날씨 어때?" | 자동 감지 → 검색 → 날씨 정보 포함 답변 |
| "CodeIgniter 최신 버전은?" | 자동 감지 → 검색 → 공식 사이트 기반 답변 |
| "안녕!" | 검색 없음 → 일반 대화 |
| 검색 토글 ON + "PHP란?" | 강제 검색 → 최신 문서 기반 답변 |

---

## 주의사항

- Tavily 무료 티어: 1,000회/월 → 불필요한 검색 방지를 위해 자동 감지 로직 정교화 필요
- 검색 결과가 없을 경우 Groq 단독 응답으로 fallback
- API 키는 `.env`에만 저장, 클라이언트 미노출
- 검색 타임아웃: 10초 (Groq보다 우선 실행)
