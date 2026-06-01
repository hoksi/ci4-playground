<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class SyncEditor extends BaseController
{
    public function index(): string
    {
        $doc = $this->getDoc();

        return view('examples/sync-editor/index', [
            'title'   => '동기화 에디터',
            'content' => $doc['content'] ?? '',
            'version' => $doc['version'] ?? 1,
        ]);
    }

    public function doc(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON($this->getDoc());
    }

    public function save(): \CodeIgniter\HTTP\ResponseInterface
    {
        $content  = $this->request->getPost('content') ?? '';
        $clientId = $this->request->getPost('client_id') ?? '';

        $db  = db_connect();
        $doc = $db->table('sync_docs')->limit(1)->get()->getRowArray();

        if (! $doc) {
            return $this->response->setStatusCode(404)->setJSON(['error' => '문서를 찾을 수 없습니다.']);
        }

        $newVersion = (int) $doc['version'] + 1;
        $now        = date('Y-m-d H:i:s');

        $db->table('sync_docs')->where('id', $doc['id'])->update([
            'content'    => $content,
            'version'    => $newVersion,
            'client_id'  => $clientId,
            'updated_at' => $now,
        ]);

        return $this->response->setJSON([
            'success'    => true,
            'version'    => $newVersion,
            'client_id'  => $clientId,
            'updated_at' => $now,
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

        $lastVersion = (int) ($this->request->getServer('HTTP_LAST_EVENT_ID') ?? 0);
        $maxTicks    = 120; // 최대 4분 (2s × 120)

        echo ": connected\n\n";
        flush();

        for ($i = 0; $i < $maxTicks; $i++) {
            if (connection_aborted()) {
                break;
            }

            $doc = $this->getDoc();
            $ver = (int) ($doc['version'] ?? 0);

            if ($ver > $lastVersion) {
                $lastVersion = $ver;
                echo "id: {$ver}\n";
                echo "event: update\n";
                echo 'data: ' . json_encode([
                    'version'    => $ver,
                    'content'    => $doc['content'],
                    'client_id'  => $doc['client_id'],
                    'updated_at' => $doc['updated_at'],
                ]) . "\n\n";
                flush();
            }

            sleep(2);
        }

        echo "event: reconnect\ndata: {}\n\n";
        flush();
    }

    private function getDoc(): array
    {
        $row = db_connect()->table('sync_docs')->limit(1)->get()->getRowArray();
        return $row ?: [];
    }
}
