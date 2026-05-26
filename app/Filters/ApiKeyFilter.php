<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        // Authorization: Bearer {api_key} 헤더 검증
        if (empty($authHeader) || ! str_starts_with($authHeader, 'Bearer ')) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'error'   => 'Unauthorized',
                    'message' => 'Authorization: Bearer {api_key} 헤더가 필요합니다.',
                ]);
        }

        $apiKey = trim(substr($authHeader, 7));

        $db  = \Config\Database::connect();
        $row = $db->table('api_keys')->where('api_key', $apiKey)->get()->getRowArray();

        if (! $row) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'error'   => 'Invalid API Key',
                    'message' => '유효하지 않은 API 키입니다.',
                ]);
        }

        if (! $row['is_active']) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON([
                    'success' => false,
                    'error'   => 'Forbidden',
                    'message' => '비활성화된 API 키입니다.',
                ]);
        }

        // 통과 시 last_used_at 업데이트
        $db->table('api_keys')
            ->where('id', $row['id'])
            ->update(['last_used_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
