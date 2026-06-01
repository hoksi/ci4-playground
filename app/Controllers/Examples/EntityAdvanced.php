<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Entities\UserEntity;

class EntityAdvanced extends BaseController
{
    public function index(): string
    {
        return view('examples/entityadvanced/index', [
            'title' => 'Entity 심화',
        ]);
    }

    public function demo()
    {
        $firstName = trim((string) $this->request->getPost('first_name'));
        $lastName  = trim((string) $this->request->getPost('last_name'));
        $email     = trim((string) $this->request->getPost('email'));
        $isActive  = (string) $this->request->getPost('is_active');
        $score     = (string) $this->request->getPost('score');
        $tagsCsv   = (string) $this->request->getPost('tags');
        $metaJson  = (string) $this->request->getPost('metadata');

        // ─── Entity 인스턴스 생성 (캐스팅 전 raw 입력) ───────
        $raw = [
            'id'         => '42',                            // string → integer
            'first_name' => $firstName,                       // setter: ucfirst()
            'last_name'  => $lastName,                        // setter: ucfirst()
            'email'      => $email,                           // setter: strtolower()+trim()
            'is_active'  => $isActive === '1' ? 1 : 0,        // → boolean
            'score'      => $score !== '' ? $score : '0',     // string → float
            'tags'       => $tagsCsv,                         // CSV → array
            'metadata'   => $metaJson !== '' ? $metaJson : '{}', // JSON → array
            'created_at' => '2026-05-26 10:00:00',            // string → Time
        ];

        $user = new UserEntity($raw);

        // 캐스팅 결과 추출
        $casted = [
            'id'         => ['value' => $user->id,         'type' => get_debug_type($user->id)],
            'first_name' => ['value' => $user->first_name, 'type' => get_debug_type($user->first_name)],
            'last_name'  => ['value' => $user->last_name,  'type' => get_debug_type($user->last_name)],
            'email'      => ['value' => $user->email,      'type' => get_debug_type($user->email)],
            'is_active'  => ['value' => $user->is_active,  'type' => get_debug_type($user->is_active)],
            'score'      => ['value' => $user->score,      'type' => get_debug_type($user->score)],
            'tags'       => ['value' => $user->tags,       'type' => get_debug_type($user->tags)],
            'metadata'   => ['value' => $user->metadata,   'type' => get_debug_type($user->metadata)],
            'created_at' => [
                'value' => ($user->created_at instanceof \CodeIgniter\I18n\Time) ? $user->created_at->format('Y-m-d H:i:s') : null,
                'type'  => get_debug_type($user->created_at),
            ],
        ];

        // Virtual properties
        $virtual = [
            'full_name'    => $user->full_name,     // getFullName()
            'display_name' => $user->display_name,  // getDisplayName()
        ];

        // Datamap 시연 (email_address 로 접근)
        $datamap = [
            'access_by_email_address' => $user->email_address,
        ];

        return $this->response->setJSON([
            'success' => true,
            'raw'     => $raw,
            'casted'  => $casted,
            'virtual' => $virtual,
            'datamap' => $datamap,
        ]);
    }
}
