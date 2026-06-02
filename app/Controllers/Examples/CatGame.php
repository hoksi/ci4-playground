<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class CatGame extends BaseController
{
    private const MODEL   = 'llama-3.1-8b-instant';
    private const ACTIONS = ['feed', 'play', 'sleep', 'pet'];
    private const EFFECTS = [
        'feed'  => ['hunger' =>  35, 'happiness' =>  5, 'energy' =>  -5],
        'play'  => ['hunger' => -10, 'happiness' => 25, 'energy' => -20],
        'sleep' => ['hunger' =>  -5, 'happiness' =>  5, 'energy' =>  40],
        'pet'   => ['hunger' =>  -2, 'happiness' => 20, 'energy' =>  -5],
    ];
    private const DECAY_PER_MIN = ['hunger' => 0.5, 'happiness' => 0.3, 'energy' => 0.4];

    public function index(): string
    {
        $cat = $this->loadCat();

        $mood = $this->getMood($cat['hunger'], $cat['happiness'], $cat['energy']);

        return view('examples/cat-game/index', [
            'title'        => '고양이 키우기',
            'cat'          => $cat,
            'mood'         => $mood,
            'moodEmoji'    => $this->getMoodEmoji($mood),
            'defaultSpeech'=> $this->getDefaultSpeech($mood),
        ]);
    }

    public function status(): \CodeIgniter\HTTP\ResponseInterface
    {
        $cat = $this->loadCat();

        return $this->response->setJSON([
            'hunger'    => $cat['hunger'],
            'happiness' => $cat['happiness'],
            'energy'    => $cat['energy'],
            'name'      => $cat['name'],
            'mood'      => $this->getMood($cat['hunger'], $cat['happiness'], $cat['energy']),
        ]);
    }

    public function action(): \CodeIgniter\HTTP\ResponseInterface
    {
        $act = (string) ($this->request->getPost('action') ?? '');

        if (! in_array($act, self::ACTIONS, true)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => '잘못된 행동입니다.']);
        }

        $db  = db_connect();
        $row = $db->table('cats')->limit(1)->get()->getRowArray();

        if (! $row) {
            return $this->response->setStatusCode(404)->setJSON(['error' => '고양이를 찾을 수 없습니다.']);
        }

        [$hunger, $happiness, $energy] = $this->applyDecay($row);

        $fx        = self::EFFECTS[$act];
        $hunger    = max(0, min(100, $hunger    + $fx['hunger']));
        $happiness = max(0, min(100, $happiness + $fx['happiness']));
        $energy    = max(0, min(100, $energy    + $fx['energy']));

        $now = date('Y-m-d H:i:s');
        $db->table('cats')->where('id', $row['id'])->update([
            'hunger'       => $hunger,
            'happiness'    => $happiness,
            'energy'       => $energy,
            'last_updated' => $now,
        ]);

        $mood     = $this->getMood($hunger, $happiness, $energy);
        $reaction = $this->getGroqReaction($row['name'], $act, $hunger, $happiness, $energy);

        return $this->response->setJSON([
            'success'   => true,
            'hunger'    => $hunger,
            'happiness' => $happiness,
            'energy'    => $energy,
            'mood'      => $mood,
            'reaction'  => $reaction,
        ]);
    }

    public function rename(): \CodeIgniter\HTTP\ResponseInterface
    {
        $name = mb_substr(trim((string) ($this->request->getPost('name') ?? '')), 0, 32);

        if ($name === '') {
            return $this->response->setStatusCode(400)->setJSON(['error' => '이름을 입력해주세요.']);
        }

        db_connect()->table('cats')->where('id >', 0)->update(['name' => $name]);

        return $this->response->setJSON(['success' => true, 'name' => $name]);
    }

    public function reset(): \CodeIgniter\HTTP\ResponseInterface
    {
        $now = date('Y-m-d H:i:s');
        db_connect()->table('cats')->where('id >', 0)->update([
            'hunger'       => 70,
            'happiness'    => 70,
            'energy'       => 70,
            'last_updated' => $now,
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    // ─── private helpers ─────────────────────────────────────────────────────

    private function loadCat(): array
    {
        $db  = db_connect();
        $row = $db->table('cats')->limit(1)->get()->getRowArray();

        if (! $row) {
            return ['name' => '냥이', 'hunger' => 70, 'happiness' => 70, 'energy' => 70];
        }

        [$hunger, $happiness, $energy] = $this->applyDecay($row);

        $db->table('cats')->where('id', $row['id'])->update([
            'hunger'       => $hunger,
            'happiness'    => $happiness,
            'energy'       => $energy,
            'last_updated' => date('Y-m-d H:i:s'),
        ]);

        return array_merge($row, ['hunger' => $hunger, 'happiness' => $happiness, 'energy' => $energy]);
    }

    /** 경과 시간 기반 상태 감소 */
    private function applyDecay(array $row): array
    {
        $elapsed   = max(0, (time() - strtotime((string) ($row['last_updated'] ?? 'now'))) / 60);
        $hunger    = max(0, (int) $row['hunger']    - (int) ($elapsed * self::DECAY_PER_MIN['hunger']));
        $happiness = max(0, (int) $row['happiness'] - (int) ($elapsed * self::DECAY_PER_MIN['happiness']));
        $energy    = max(0, (int) $row['energy']    - (int) ($elapsed * self::DECAY_PER_MIN['energy']));

        return [$hunger, $happiness, $energy];
    }

    private function getMoodEmoji(string $mood): string
    {
        $map = [
            'ecstatic' => '😻', 'happy'  => '😺', 'neutral' => '😼',
            'hungry'   => '😿', 'tired'  => '😪', 'sad'     => '😾', 'critical' => '🙀',
        ];
        return $map[$mood] ?? '😺';
    }

    private function getDefaultSpeech(string $mood): string
    {
        $map = [
            'ecstatic' => '그르릉~ 너무 행복해요 냥! 🎉',
            'happy'    => '냥~ 기분이 좋아요!',
            'neutral'  => '..냥.',
            'hungry'   => '배고파요... 밥 주세요 냥!',
            'tired'    => '졸려요... 재워주세요 냥...',
            'sad'      => '심심해요. 놀아주세요 냥!',
            'critical' => '도와주세요!! 냥!!',
        ];
        return $map[$mood] ?? '냥~';
    }

    private function getMood(int $hunger, int $happiness, int $energy): string
    {
        $min = min($hunger, $happiness, $energy);
        $avg = (int) (($hunger + $happiness + $energy) / 3);

        if ($min <= 10)         return 'critical';
        if ($hunger <= 25)      return 'hungry';
        if ($energy <= 25)      return 'tired';
        if ($happiness <= 25)   return 'sad';
        if ($avg >= 75)         return 'ecstatic';
        if ($avg >= 50)         return 'happy';

        return 'neutral';
    }

    private function getGroqReaction(string $name, string $action, int $hunger, int $happiness, int $energy): string
    {
        $apiKey = trim((string) (env('GROQ_API_KEY') ?? ''));
        if ($apiKey === '') {
            return '냥~';
        }

        $labels = ['feed' => '먹이를 받았을 때', 'play' => '같이 놀아줬을 때', 'sleep' => '재워줬을 때', 'pet' => '쓰다듬어줬을 때'];

        $prompt = "당신은 '{$name}'이라는 고양이입니다.\n"
            . "현재 상태: 배고픔 {$hunger}/100, 행복도 {$happiness}/100, 에너지 {$energy}/100\n"
            . "상황: {$labels[$action]}\n"
            . "현재 상태에 어울리는 반응을 고양이 말투로 1~2문장 짧게 표현하세요. 야옹, 냥, 그르릉 등을 자연스럽게 섞어주세요.";

        try {
            $res  = \Config\Services::curlrequest()->post(
                'https://api.groq.com/openai/v1/chat/completions',
                [
                    'headers' => ['Authorization' => 'Bearer ' . $apiKey, 'Content-Type' => 'application/json'],
                    'body'    => json_encode([
                        'model'       => self::MODEL,
                        'messages'    => [['role' => 'user', 'content' => $prompt]],
                        'max_tokens'  => 80,
                        'temperature' => 0.9,
                    ]),
                    'timeout' => 10,
                ]
            );
            $body = json_decode($res->getBody(), true);
            return trim($body['choices'][0]['message']['content'] ?? '냥~');
        } catch (\Throwable) {
            return '냥~';
        }
    }
}
