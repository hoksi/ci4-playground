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
    private const EXP_GAIN = ['feed' => 10, 'play' => 15, 'sleep' => 5, 'pet' => 12];
    private const DECAY_PER_MIN = ['hunger' => 0.5, 'happiness' => 0.3, 'energy' => 0.4];

    // 레벨 임계값: Lv1-5 아기, Lv6-15 성묘, Lv16+ 노령묘
    private const STAGE_THRESHOLDS = ['baby' => 5, 'adult' => 15];

    // 성격: Lv.5 도달 시 행동 카운터 비율로 확정
    private const PERSONALITIES = [
        'feed'     => ['desc' => '음식을 무척 사랑하는', 'trait' => '배고프면 짜증을 잘 내고, 먹을 때 가장 행복해합니다.'],
        'play'     => ['desc' => '에너지가 넘치는',      'trait' => '놀자고 조르는 편이며, 쉬는 걸 싫어합니다.'],
        'sleep'    => ['desc' => '잠을 사랑하는',        'trait' => '느긋하고 무기력하며, 귀찮은 걸 싫어합니다.'],
        'pet'      => ['desc' => '스킨십을 좋아하는',    'trait' => '관심을 원하고, 혼자 있으면 외로워합니다.'],
        'balanced' => ['desc' => '균형 잡힌',            'trait' => '무엇이든 잘 적응하고 온화한 성격입니다.'],
    ];

    public function index(): string
    {
        $cat = $this->loadCat();

        return view('examples/cat-game/index', [
            'title'         => '고양이 키우기',
            'cat'           => $cat,
            'mood'          => $this->getMood($cat['hunger'], $cat['happiness'], $cat['energy']),
            'stage'         => $this->getStage($cat['level']),
            'moodEmoji'     => $this->getMoodEmoji($this->getMood($cat['hunger'], $cat['happiness'], $cat['energy']), $this->getStage($cat['level'])),
            'defaultSpeech' => $this->getDefaultSpeech($this->getMood($cat['hunger'], $cat['happiness'], $cat['energy'])),
            'expToNext'     => $this->expToNextLevel($cat['exp']),
            'expProgress'   => $this->expProgress($cat['exp']),
        ]);
    }

    public function status(): \CodeIgniter\HTTP\ResponseInterface
    {
        $cat = $this->loadCat();

        return $this->response->setJSON([
            'hunger'      => $cat['hunger'],
            'happiness'   => $cat['happiness'],
            'energy'      => $cat['energy'],
            'name'        => $cat['name'],
            'level'       => $cat['level'],
            'exp'         => $cat['exp'],
            'expProgress' => $this->expProgress($cat['exp']),
            'expToNext'   => $this->expToNextLevel($cat['exp']),
            'stage'       => $this->getStage($cat['level']),
            'mood'        => $this->getMood($cat['hunger'], $cat['happiness'], $cat['energy']),
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

        // 경험치 & 레벨 계산
        $newExp    = (int) $row['exp'] + self::EXP_GAIN[$act];
        $newLevel  = $this->calculateLevel($newExp);
        $leveledUp = $newLevel > (int) $row['level'];

        // 행동 카운터 증가
        $actKey = 'act_' . $act;
        $newActCount = (int) ($row[$actKey] ?? 0) + 1;

        // Lv.5 도달 시 성격 확정 (아직 성격이 없을 때만)
        $personality = $row['personality'] ?? null;
        if ($leveledUp && $newLevel === 6 && $personality === null) {
            $personality = $this->determinePersonality(array_merge($row, [$actKey => $newActCount]));
        }

        $now = date('Y-m-d H:i:s');
        $db->table('cats')->where('id', $row['id'])->update([
            'hunger'       => $hunger,
            'happiness'    => $happiness,
            'energy'       => $energy,
            'level'        => $newLevel,
            'exp'          => $newExp,
            $actKey        => $newActCount,
            'personality'  => $personality,
            'last_updated' => $now,
        ]);

        // 히스토리 기록
        $db->table('cat_history')->insert([
            'cat_id'      => $row['id'],
            'hunger'      => $hunger,
            'happiness'   => $happiness,
            'energy'      => $energy,
            'level'       => $newLevel,
            'recorded_at' => $now,
        ]);

        $stage    = $this->getStage($newLevel);
        $mood     = $this->getMood($hunger, $happiness, $energy);
        $reaction = $this->getGroqReaction($row['name'], $act, $hunger, $happiness, $energy, $stage, $leveledUp, $personality);

        return $this->response->setJSON([
            'success'     => true,
            'hunger'      => $hunger,
            'happiness'   => $happiness,
            'energy'      => $energy,
            'level'       => $newLevel,
            'exp'         => $newExp,
            'expProgress' => $this->expProgress($newExp),
            'expToNext'   => $this->expToNextLevel($newExp),
            'stage'       => $stage,
            'mood'        => $mood,
            'moodEmoji'   => $this->getMoodEmoji($mood, $stage),
            'leveledUp'   => $leveledUp,
            'reaction'    => $reaction,
        ]);
    }

    /** 상태 히스토리 — 최근 20건 */
    public function history(): \CodeIgniter\HTTP\ResponseInterface
    {
        $cat = db_connect()->table('cats')->limit(1)->get()->getRowArray();

        if (! $cat) {
            return $this->response->setJSON(['labels' => [], 'hunger' => [], 'happiness' => [], 'energy' => []]);
        }

        $rows = db_connect()
            ->table('cat_history')
            ->where('cat_id', $cat['id'])
            ->orderBy('id', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        $rows = array_reverse($rows);

        return $this->response->setJSON([
            'labels'    => array_map(fn($r) => date('H:i', strtotime($r['recorded_at'])), $rows),
            'hunger'    => array_map('intval', array_column($rows, 'hunger')),
            'happiness' => array_map('intval', array_column($rows, 'happiness')),
            'energy'    => array_map('intval', array_column($rows, 'energy')),
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
            'level'        => 1,
            'exp'          => 0,
            'personality'  => null,
            'act_feed'     => 0,
            'act_play'     => 0,
            'act_sleep'    => 0,
            'act_pet'      => 0,
            'last_updated' => $now,
        ]);
        db_connect()->table('cat_history')->truncate();

        return $this->response->setJSON(['success' => true]);
    }

    // ─── private helpers ─────────────────────────────────────────────────────

    private function loadCat(): array
    {
        $db  = db_connect();
        $row = $db->table('cats')->limit(1)->get()->getRowArray();

        if (! $row) {
            return ['name' => '냥이', 'hunger' => 70, 'happiness' => 70, 'energy' => 70, 'level' => 1, 'exp' => 0];
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

    private function applyDecay(array $row): array
    {
        $elapsed   = max(0, (time() - strtotime((string) ($row['last_updated'] ?? 'now'))) / 60);
        $hunger    = max(0, (int) $row['hunger']    - (int) ($elapsed * self::DECAY_PER_MIN['hunger']));
        $happiness = max(0, (int) $row['happiness'] - (int) ($elapsed * self::DECAY_PER_MIN['happiness']));
        $energy    = max(0, (int) $row['energy']    - (int) ($elapsed * self::DECAY_PER_MIN['energy']));

        return [$hunger, $happiness, $energy];
    }

    private function calculateLevel(int $exp): int
    {
        return min(20, (int) ($exp / 50) + 1);
    }

    private function expProgress(int $exp): int
    {
        return ($exp % 50) * 2; // 0~100%
    }

    private function expToNextLevel(int $exp): int
    {
        if ($this->calculateLevel($exp) >= 20) return 0;
        return 50 - ($exp % 50);
    }

    private function getStage(int $level): string
    {
        if ($level <= self::STAGE_THRESHOLDS['baby'])  return 'baby';
        if ($level <= self::STAGE_THRESHOLDS['adult']) return 'adult';
        return 'elder';
    }

    private function getMood(int $hunger, int $happiness, int $energy): string
    {
        $min = min($hunger, $happiness, $energy);
        $avg = (int) (($hunger + $happiness + $energy) / 3);

        if ($min <= 10)       return 'critical';
        if ($hunger <= 25)    return 'hungry';
        if ($energy <= 25)    return 'tired';
        if ($happiness <= 25) return 'sad';
        if ($avg >= 75)       return 'ecstatic';
        if ($avg >= 50)       return 'happy';

        return 'neutral';
    }

    private function getMoodEmoji(string $mood, string $stage): string
    {
        $map = [
            'baby' => [
                'ecstatic' => '🐱', 'happy' => '🐱', 'neutral' => '🐱',
                'hungry' => '😿', 'tired' => '😪', 'sad' => '😾', 'critical' => '🙀',
            ],
            'adult' => [
                'ecstatic' => '😻', 'happy' => '😺', 'neutral' => '😼',
                'hungry' => '😿', 'tired' => '😪', 'sad' => '😾', 'critical' => '🙀',
            ],
            'elder' => [
                'ecstatic' => '🐈', 'happy' => '🐈', 'neutral' => '🐈‍⬛',
                'hungry' => '😿', 'tired' => '😪', 'sad' => '😾', 'critical' => '🙀',
            ],
        ];
        return $map[$stage][$mood] ?? '😺';
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

    private function determinePersonality(array $row): string
    {
        $counts = [
            'feed'  => (int) ($row['act_feed']  ?? 0),
            'play'  => (int) ($row['act_play']  ?? 0),
            'sleep' => (int) ($row['act_sleep'] ?? 0),
            'pet'   => (int) ($row['act_pet']   ?? 0),
        ];

        $max      = max($counts);
        $dominant = array_keys(array_filter($counts, fn($v) => $v === $max));

        return count($dominant) === 1 ? $dominant[0] : 'balanced';
    }

    private function getGroqReaction(
        string $name, string $action, int $hunger, int $happiness, int $energy,
        string $stage, bool $leveledUp, ?string $personality
    ): string {
        $apiKey = trim((string) (env('GROQ_API_KEY') ?? ''));
        if ($apiKey === '') {
            return $leveledUp ? '레벨 업! 냥~! ✨' : '냥~';
        }

        $labels   = ['feed' => '먹이를 받았을 때', 'play' => '같이 놀아줬을 때', 'sleep' => '재워줬을 때', 'pet' => '쓰다듬어줬을 때'];
        $stages   = ['baby' => '아기 고양이', 'adult' => '성묘', 'elder' => '노령묘'];
        $levelMsg = $leveledUp ? "\n특별 상황: 방금 레벨업했습니다! 기뻐하세요." : '';

        // 성격 정보 (확정된 경우만 프롬프트에 주입)
        $personalityLine = '';
        if ($personality !== null && isset(self::PERSONALITIES[$personality])) {
            $p = self::PERSONALITIES[$personality];
            $personalityLine = "\n성격: {$p['desc']} 고양이. {$p['trait']}";
        }

        $prompt = "당신은 '{$name}'이라는 {$stages[$stage]}입니다.{$personalityLine}\n"
            . "현재 상태: 배고픔 {$hunger}/100, 행복도 {$happiness}/100, 에너지 {$energy}/100\n"
            . "상황: {$labels[$action]}{$levelMsg}\n"
            . "현재 상태와 성격에 어울리는 반응을 고양이 말투로 1~2문장 짧게 표현하세요.";

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
            return $leveledUp ? '레벨 업! 냥~! ✨' : '냥~';
        }
    }
}
