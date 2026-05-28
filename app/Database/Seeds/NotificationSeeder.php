<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $notifications = [
            ['type' => 'success', 'title' => '회원 가입',        'message' => '새 회원(user01@example.com)이 가입했습니다.',                 'is_read' => 1, 'created_at' => date('Y-m-d H:i:s', strtotime('-10 minutes'))],
            ['type' => 'info',    'title' => '새 게시글',        'message' => '김철수님이 새 게시글 "CI4 팁 모음"을 등록했습니다.',           'is_read' => 1, 'created_at' => date('Y-m-d H:i:s', strtotime('-8 minutes'))],
            ['type' => 'warning', 'title' => '시스템 점검 예정', 'message' => '내일 새벽 2~4시 정기 점검이 예정되어 있습니다.',               'is_read' => 0, 'created_at' => date('Y-m-d H:i:s', strtotime('-5 minutes'))],
            ['type' => 'error',   'title' => '오류 발생',        'message' => 'API 서버 응답 시간이 임계값(2s)을 초과했습니다.',              'is_read' => 0, 'created_at' => date('Y-m-d H:i:s', strtotime('-3 minutes'))],
            ['type' => 'info',    'title' => '캐시 갱신',        'message' => '상품 목록 캐시가 자동으로 갱신되었습니다.',                    'is_read' => 0, 'created_at' => $now],
        ];

        $this->db->table('notifications')->insertBatch($notifications);
    }
}
