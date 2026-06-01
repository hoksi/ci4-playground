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

    public function clear(): \CodeIgniter\HTTP\ResponseInterface
    {
        db_connect()->table('chat_messages')->truncate();

        return $this->response->setJSON(['success' => true]);
    }
}
