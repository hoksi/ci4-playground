<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notification extends BaseController
{
    private NotificationModel $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    public function index(): string
    {
        return view('examples/notification/index', [
            'title'         => '알림 시스템',
            'notifications' => $this->model->orderBy('created_at', 'DESC')->findAll(),
            'unreadCount'   => $this->model->countUnread(),
        ]);
    }

    public function create(): \CodeIgniter\HTTP\ResponseInterface
    {
        $samples = [
            ['type' => 'info',    'title' => '새 게시글',        'message' => '김철수님이 새 게시글 "CI4 팁 모음"을 등록했습니다.'],
            ['type' => 'success', 'title' => '회원 가입',        'message' => '새 회원(user@example.com)이 가입했습니다.'],
            ['type' => 'warning', 'title' => '시스템 점검 예정', 'message' => '내일 새벽 2~4시 정기 점검이 예정되어 있습니다.'],
            ['type' => 'error',   'title' => '오류 발생',        'message' => 'API 서버 응답 시간이 임계값(2s)을 초과했습니다.'],
            ['type' => 'success', 'title' => '큐 잡 완료',       'message' => '보고서 생성 잡이 정상적으로 처리되었습니다.'],
            ['type' => 'info',    'title' => '캐시 갱신',        'message' => '상품 목록 캐시가 자동으로 갱신되었습니다.'],
        ];

        $data = $samples[array_rand($samples)];
        $id   = $this->model->insert($data);

        return $this->response->setJSON([
            'success'      => true,
            'notification' => $this->model->find($id),
            'unreadCount'  => $this->model->countUnread(),
        ]);
    }

    public function read(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $this->model->markRead($id);

        return $this->response->setJSON([
            'success'    => true,
            'unreadCount' => $this->model->countUnread(),
        ]);
    }

    public function readAll(): \CodeIgniter\HTTP\ResponseInterface
    {
        $this->model->markAllRead();

        return $this->response->setJSON([
            'success'    => true,
            'unreadCount' => $this->model->countUnread(),
        ]);
    }

    public function clear(): \CodeIgniter\HTTP\ResponseInterface
    {
        db_connect()->table('notifications')->truncate();

        return $this->response->setJSON([
            'success'    => true,
            'unreadCount' => 0,
        ]);
    }

    public function count(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON(['unread' => $this->model->countUnread()]);
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
        header('Access-Control-Allow-Origin: *');

        echo ": ping\n\n";
        flush();

        $maxTicks = 60;

        for ($i = 0; $i < $maxTicks; $i++) {
            if (connection_aborted()) {
                break;
            }

            $unread = $this->model->countUnread();
            echo "event: notification\n";
            echo 'data: ' . json_encode(['unread' => $unread]) . "\n\n";
            flush();

            sleep(3);
        }

        echo "event: reconnect\n";
        echo "data: {}\n\n";
        flush();
    }
}
