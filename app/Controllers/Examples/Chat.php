<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Chat extends BaseController
{
    public function index(): string
    {
        $messages = db_connect()
            ->table('chat_messages')
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        return view('examples/chat/index', [
            'title'    => '실시간 채팅',
            'messages' => array_reverse($messages),
            'lastId'   => $messages ? (int) $messages[0]['id'] : 0,
        ]);
    }

    public function send(): \CodeIgniter\HTTP\ResponseInterface
    {
        $nickname = trim((string) ($this->request->getPost('nickname') ?? ''));
        $content  = trim((string) ($this->request->getPost('content') ?? ''));

        if ($nickname === '' || $content === '') {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => '닉네임과 메시지를 입력해주세요.']);
        }

        if (mb_strlen($content) > 500) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => '메시지는 500자 이내로 입력해주세요.']);
        }

        $now = date('Y-m-d H:i:s');
        $id  = db_connect()->table('chat_messages')->insert([
            'nickname'   => mb_substr($nickname, 0, 32),
            'content'    => $content,
            'created_at' => $now,
        ], true);

        return $this->response->setJSON([
            'success'    => true,
            'id'         => $id,
            'nickname'   => $nickname,
            'content'    => $content,
            'created_at' => $now,
        ]);
    }

    public function stream(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        @set_time_limit(0);
        ob_implicit_flush(true);

        header('Content-Type: text/event-stream; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Connection: keep-alive');

        // Last-Event-ID = 마지막으로 받은 메시지 ID
        $lastId  = (int) ($this->request->getServer('HTTP_LAST_EVENT_ID') ?? 0);
        $maxTicks = 150; // 최대 5분

        echo ": connected\n\n";
        flush();

        for ($i = 0; $i < $maxTicks; $i++) {
            if (connection_aborted()) {
                break;
            }

            $rows = db_connect()
                ->table('chat_messages')
                ->where('id >', $lastId)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            if (! empty($rows)) {
                $lastId = (int) end($rows)['id'];
                echo "id: {$lastId}\n";
                echo "event: message\n";
                echo 'data: ' . json_encode($rows) . "\n\n";
                flush();
            }

            sleep(2);
        }

        echo "event: reconnect\ndata: {}\n\n";
        flush();
    }

    public function botReply(): \CodeIgniter\HTTP\ResponseInterface
    {
        $apiKey = trim((string) ($this->request->getPost('api_key') ?? ''));

        // 클라이언트 키가 없으면 서버 환경변수 사용
        if ($apiKey === '') {
            $apiKey = trim((string) (env('GROQ_API_KEY') ?? ''));
        }

        if ($apiKey === '') {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Groq API 키가 필요합니다.']);
        }

        // 최근 20개 메시지를 대화 컨텍스트로 사용
        $rows = db_connect()
            ->table('chat_messages')
            ->orderBy('id', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        $groqMessages = [
            ['role' => 'system', 'content' => '당신은 친근하고 유쾌한 채팅 친구입니다. 한국어로 짧고 자연스럽게 대화하세요. 2~3문장 이내로 답변하세요.'],
        ];

        foreach (array_reverse($rows) as $msg) {
            $role           = $msg['nickname'] === 'AI봇' ? 'assistant' : 'user';
            $groqMessages[] = ['role' => $role, 'content' => $msg['content']];
        }

        try {
            $response = \Config\Services::curlrequest()->post(
                'https://api.groq.com/openai/v1/chat/completions',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type'  => 'application/json',
                    ],
                    'body'    => json_encode([
                        'model'       => 'llama-3.3-70b-versatile',
                        'messages'    => $groqMessages,
                        'max_tokens'  => 300,
                        'temperature' => 0.8,
                    ]),
                    'timeout' => 15,
                ]
            );

            $body    = json_decode($response->getBody(), true);
            $content = trim($body['choices'][0]['message']['content'] ?? '');

            if ($content === '') {
                return $this->response->setStatusCode(500)
                    ->setJSON(['error' => 'AI 응답이 비어 있습니다.']);
            }

            $now = date('Y-m-d H:i:s');
            $id  = db_connect()->table('chat_messages')->insert([
                'nickname'   => 'AI봇',
                'content'    => $content,
                'created_at' => $now,
            ], true);

            return $this->response->setJSON([
                'success'    => true,
                'id'         => $id,
                'nickname'   => 'AI봇',
                'content'    => $content,
                'created_at' => $now,
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Groq API 오류: ' . $e->getMessage()]);
        }
    }

    public function clear(): \CodeIgniter\HTTP\ResponseInterface
    {
        db_connect()->table('chat_messages')->truncate();

        return $this->response->setJSON(['success' => true]);
    }
}
