<?php

if (! function_exists('format_filesize')) {
    /**
     * 바이트 수를 사람이 읽기 쉬운 파일 크기 문자열로 변환
     */
    function format_filesize(int $bytes, int $decimals = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $decimals) . ' ' . $units[$i];
    }
}

if (! function_exists('time_ago')) {
    /**
     * 날짜/시간을 "N분 전", "N시간 전" 형식으로 반환
     */
    function time_ago(int|string $datetime): string
    {
        $timestamp = is_int($datetime) ? $datetime : strtotime((string) $datetime);
        $diff      = time() - $timestamp;

        if ($diff < 60)         return $diff . '초 전';
        if ($diff < 3600)       return floor($diff / 60) . '분 전';
        if ($diff < 86400)      return floor($diff / 3600) . '시간 전';
        if ($diff < 2592000)    return floor($diff / 86400) . '일 전';
        if ($diff < 31536000)   return floor($diff / 2592000) . '개월 전';
        return floor($diff / 31536000) . '년 전';
    }
}

if (! function_exists('truncate_text')) {
    /**
     * 텍스트를 지정 길이로 자르고 말줄임표를 붙임
     */
    function truncate_text(string $text, int $limit = 100, string $suffix = '...'): string
    {
        $text = strip_tags($text);
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        return mb_substr($text, 0, $limit) . $suffix;
    }
}

if (! function_exists('highlight_keyword')) {
    /**
     * 텍스트 내 키워드를 <mark>로 강조
     */
    function highlight_keyword(string $text, string $keyword): string
    {
        if (empty($keyword)) {
            return esc($text);
        }
        $escaped = esc($text);
        $pattern = '/' . preg_quote(esc($keyword), '/') . '/ui';
        return preg_replace($pattern, '<mark>$0</mark>', $escaped);
    }
}

if (! function_exists('korean_number')) {
    /**
     * 숫자를 한국어 단위(만, 억)로 포맷
     */
    function korean_number(int $num): string
    {
        if ($num < 10000) {
            return number_format($num);
        }
        if ($num < 100000000) {
            $man = floor($num / 10000);
            $rem = $num % 10000;
            return number_format($man) . '만' . ($rem > 0 ? ' ' . number_format($rem) : '');
        }
        $eok = floor($num / 100000000);
        $rem = $num % 100000000;
        return number_format($eok) . '억' . ($rem > 0 ? ' ' . korean_number($rem) : '');
    }
}
