<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class ApiAuth extends BaseController
{
    public function index(): string
    {
        $db   = \Config\Database::connect();
        $keys = $db->table('api_keys')->orderBy('created_at', 'DESC')->get()->getResultArray();

        return view('examples/apiauth/index', [
            'title' => 'API 인증 (API Key)',
            'keys'  => $keys,
        ]);
    }

    public function generate()
    {
        $name = $this->request->getPost('name');
        if (empty($name)) {
            return redirect()->back()->with('error', '앱 이름을 입력하세요.');
        }

        $apiKey = bin2hex(random_bytes(32));
        $db = \Config\Database::connect();
        $db->table('api_keys')->insert([
            'name'       => $name,
            'api_key'    => $apiKey,
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('examples/apiauth'))
            ->with('success', '새 API 키가 발급됐습니다: ' . $apiKey);
    }

    public function revoke()
    {
        $id = $this->request->getPost('id');
        if (empty($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID가 필요합니다.']);
        }

        $db = \Config\Database::connect();
        $db->table('api_keys')->where('id', $id)->update([
            'is_active'  => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'API 키가 비활성화됐습니다.']);
    }

    public function testApi()
    {
        $apiKey = $this->request->getPost('api_key');
        if (empty($apiKey)) {
            return $this->response->setJSON(['success' => false, 'message' => 'API 키를 입력하세요.']);
        }

        // 보호된 엔드포인트를 내부적으로 HTTP 요청으로 테스트
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->get(base_url('examples/apiauth/protected'), [
                'headers' => [
                    'Authorization'    => 'Bearer ' . $apiKey,
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
                'http_errors' => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body       = json_decode($response->getBody(), true);

            return $this->response->setJSON([
                'status_code' => $statusCode,
                'body'        => $body,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function protected()
    {
        // ApiKeyFilter에 의해 보호되는 엔드포인트
        return $this->response->setJSON([
            'success'   => true,
            'message'   => '인증 성공! 보호된 리소스에 접근했습니다.',
            'data'      => [
                'server_time' => date('Y-m-d H:i:s'),
                'resource'    => 'protected_data',
                'items'       => ['item_1', 'item_2', 'item_3'],
            ],
        ]);
    }
}
