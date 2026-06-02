<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Chat extends BaseController
{
    private const BOT_NICK   = 'AI봇';
    private const BOT_MODELS = [
        'llama-3.3-70b-versatile',
        'llama-3.1-8b-instant',
        'llama3-70b-8192',
        'mixtral-8x7b-32768',
        'gemma2-9b-it',
    ];

    public function index(): string
    {
        $messages = db_connect()
            ->table('chat_messages')
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        return view('examples/chat/index', [
            'title'    => '챗봇',
            'messages' => array_reverse($messages),
        ]);
    }

    /** 유저 메시지 저장 + Groq 봇 응답 저장 → 둘 다 반환 */
    public function send(): \CodeIgniter\HTTP\ResponseInterface
    {
        $content = trim((string) ($this->request->getPost('content') ?? ''));
        $apiKey  = trim((string) ($this->request->getPost('api_key') ?? ''));
        $model   = (string) ($this->request->getPost('model') ?? self::BOT_MODELS[0]);

        if ($content === '') {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => '메시지를 입력해주세요.']);
        }

        if (mb_strlen($content) > 500) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => '메시지는 500자 이내로 입력해주세요.']);
        }

        if (! in_array($model, self::BOT_MODELS, true)) {
            $model = self::BOT_MODELS[0];
        }

        if ($apiKey === '') {
            $apiKey = trim((string) (env('GROQ_API_KEY') ?? ''));
        }

        if ($apiKey === '') {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Groq API 키가 필요합니다.']);
        }

        $db  = db_connect();
        $now = date('Y-m-d H:i:s');

        // 유저 메시지 저장
        $userId = $db->table('chat_messages')->insert([
            'nickname'   => '나',
            'content'    => $content,
            'created_at' => $now,
        ], true);

        $userMsg = ['id' => $userId, 'nickname' => '나', 'content' => $content, 'created_at' => $now];

        // 최근 20개 대화 컨텍스트
        $history = $db->table('chat_messages')
            ->orderBy('id', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        $groqMessages = [
            ['role' => 'system', 'content' => '당신은 친근하고 유쾌한 AI 어시스턴트입니다. 한국어로 자연스럽게 대화하세요. 2~3문장 이내로 답변하세요.'],
        ];
        foreach (array_reverse($history) as $msg) {
            $groqMessages[] = [
                'role'    => $msg['nickname'] === self::BOT_NICK ? 'assistant' : 'user',
                'content' => $msg['content'],
            ];
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
                        'model'       => $model,
                        'messages'    => $groqMessages,
                        'max_tokens'  => 300,
                        'temperature' => 0.8,
                    ]),
                    'timeout' => 15,
                ]
            );

            $body       = json_decode($response->getBody(), true);
            $botContent = trim($body['choices'][0]['message']['content'] ?? '');

            if ($botContent === '') {
                return $this->response->setStatusCode(500)
                    ->setJSON(['error' => 'AI 응답이 비어 있습니다.', 'user' => $userMsg]);
            }

            $botNow = date('Y-m-d H:i:s');
            $botId  = $db->table('chat_messages')->insert([
                'nickname'   => self::BOT_NICK,
                'content'    => $botContent,
                'created_at' => $botNow,
            ], true);

            return $this->response->setJSON([
                'success' => true,
                'user'    => $userMsg,
                'bot'     => ['id' => $botId, 'nickname' => self::BOT_NICK, 'content' => $botContent, 'created_at' => $botNow],
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Groq API 오류: ' . $e->getMessage(), 'user' => $userMsg]);
        }
    }

    public function clear(): \CodeIgniter\HTTP\ResponseInterface
    {
        db_connect()->table('chat_messages')->truncate();

        return $this->response->setJSON(['success' => true]);
    }
}
