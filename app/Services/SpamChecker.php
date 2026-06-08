<?php

namespace App\Services;

/**
 * 3단계 스팸 감지 서비스
 *
 * 1단계: 규칙 기반 (무료, 즉시)
 * 2단계: Groq AI (점수 30~70 구간만)
 * 결과: approved / review / spam
 */
class SpamChecker
{
    private const SCORE_SPAM    = 70;
    private const SCORE_REVIEW  = 30;
    private const GROQ_MODEL    = 'llama-3.1-8b-instant';
    private const GROQ_API_URL  = 'https://api.groq.com/openai/v1/chat/completions';
    private const IP_LIMIT      = 5;   // 10분 내 최대 게시글 수
    private const IP_WINDOW     = 600; // 초

    private const SPAM_KEYWORDS = [
        '비아그라', '시알리스', '카지노', '도박', '복권', '대출', '빚탕감',
        'casino', 'viagra', 'cialis', 'porn', 'sex', 'loan', 'click here',
        'free money', 'winner', 'congratulations', '무료수익', '부업',
        '당첨', '클릭', '지금바로', '100%보장',
    ];

    public function check(string $title, string $content, string $ip): array
    {
        $text  = $title . ' ' . $content;
        $score = $this->ruleScore($text, $ip);

        if ($score >= self::SCORE_SPAM) {
            return ['status' => 'spam', 'score' => $score, 'reason' => '규칙 기반 스팸 감지'];
        }

        if ($score <= self::SCORE_REVIEW) {
            return ['status' => 'approved', 'score' => $score, 'reason' => '정상'];
        }

        // 불확실 구간(31~69)만 AI 호출
        return $this->aiCheck($title, $content, $score);
    }

    private function ruleScore(string $text, string $ip): int
    {
        $score = 0;

        // 금지 키워드
        $lowerText = mb_strtolower($text);
        foreach (self::SPAM_KEYWORDS as $kw) {
            if (str_contains($lowerText, mb_strtolower($kw))) {
                $score += 30;
                break;
            }
        }

        // URL 과다 (3개 이상)
        $urlCount = preg_match_all('/https?:\/\/\S+/i', $text);
        if ($urlCount >= 3) {
            $score += 40;
        } elseif ($urlCount >= 2) {
            $score += 20;
        }

        // 반복 문자 (같은 문자 5회 이상 연속)
        if (preg_match('/(.)\1{4,}/', $text)) {
            $score += 20;
        }

        // 특수문자 비율 (30% 이상)
        $specialCount = preg_match_all('/[!@#$%^&*(){}\[\]<>\/\\\\|~`]/', $text);
        $totalLen      = mb_strlen($text) ?: 1;
        if ($specialCount / $totalLen > 0.3) {
            $score += 25;
        }

        // IP 빈도 제한
        if ($this->isIpOverLimit($ip)) {
            $score += 35;
        }

        return min($score, 100);
    }

    private function isIpOverLimit(string $ip): bool
    {
        $cacheKey = 'spam_ip_' . md5($ip);
        $count    = cache($cacheKey) ?? 0;

        cache()->save($cacheKey, $count + 1, self::IP_WINDOW);

        return $count >= self::IP_LIMIT;
    }

    private function aiCheck(string $title, string $content, int $ruleScore): array
    {
        $apiKey = env('GROQ_API_KEY');
        if (empty($apiKey)) {
            return ['status' => 'approved', 'score' => $ruleScore, 'reason' => 'AI 키 미설정'];
        }

        $prompt = <<<PROMPT
다음 게시글이 스팸인지 판단해줘. JSON으로만 응답해.

제목: {$title}
내용: {$content}

응답 형식: {"is_spam": true|false, "reason": "한 줄 이유"}
PROMPT;

        $payload = json_encode([
            'model'       => self::GROQ_MODEL,
            'messages'    => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0,
            'max_tokens'  => 80,
        ]);

        $ch = curl_init(self::GROQ_API_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
        ]);

        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $raw === false) {
            return ['status' => 'review', 'score' => $ruleScore, 'reason' => 'AI 판정 실패'];
        }

        try {
            $response = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            $aiText   = $response['choices'][0]['message']['content'] ?? '{}';

            // JSON 블록 추출
            if (preg_match('/\{.*\}/s', $aiText, $m)) {
                $aiText = $m[0];
            }

            $aiResult = json_decode($aiText, true, 512, JSON_THROW_ON_ERROR);
            $isSpam   = (bool) ($aiResult['is_spam'] ?? false);
            $reason   = $aiResult['reason'] ?? 'AI 판정';

            return [
                'status' => $isSpam ? 'spam' : 'approved',
                'score'  => $ruleScore,
                'reason' => $reason,
            ];
        } catch (\JsonException $e) {
            return ['status' => 'review', 'score' => $ruleScore, 'reason' => 'AI 응답 파싱 실패'];
        }
    }
}
