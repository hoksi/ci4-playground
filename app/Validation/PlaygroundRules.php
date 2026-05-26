<?php

namespace App\Validation;

class PlaygroundRules
{
    /**
     * 한국 전화번호 형식 검사 (010-XXXX-XXXX)
     */
    public function korean_phone(string $value, string &$error = null): bool
    {
        if (preg_match('/^01[016789]-\d{3,4}-\d{4}$/', $value)) {
            return true;
        }
        $error = '올바른 한국 전화번호 형식이 아닙니다. (예: 010-1234-5678)';
        return false;
    }

    /**
     * 사용자명 금지어 체크
     * 사용법: not_reserved[admin,root,system]
     */
    public function not_reserved(string $value, string $params, array $data, string &$error = null): bool
    {
        $reserved = array_map('trim', explode(',', $params));
        if (in_array(strtolower($value), array_map('strtolower', $reserved))) {
            $error = '"' . $value . '"은(는) 사용할 수 없는 예약어입니다. (' . implode(', ', $reserved) . ')';
            return false;
        }
        return true;
    }
}
