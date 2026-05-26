<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Transaction extends BaseController
{
    private function getAccounts(): array
    {
        return db_connect()->table('accounts')->orderBy('id')->get()->getResultObject();
    }

    public function index(): string
    {
        return view('examples/transaction/index', [
            'title'    => 'DB 트랜잭션',
            'accounts' => $this->getAccounts(),
        ]);
    }

    public function transfer()
    {
        $fromId = (int) $this->request->getPost('from_id');
        $toId   = (int) $this->request->getPost('to_id');
        $amount = (int) $this->request->getPost('amount');
        $scene  = $this->request->getPost('scene'); // 'success' | 'rollback' | 'error'

        $db = db_connect();

        $db->transStart();

        try {
            $from = $db->table('accounts')->where('id', $fromId)->get()->getRowObject();
            $to   = $db->table('accounts')->where('id', $toId)->get()->getRowObject();

            if (! $from || ! $to || $fromId === $toId) {
                throw new \RuntimeException('유효하지 않은 계좌입니다.');
            }

            // 잔액 부족 시나리오
            if ($scene === 'rollback' || $from->balance < $amount) {
                $db->transRollback();
                return redirect()->back()
                    ->with('result', [
                        'success' => false,
                        'scene'   => 'rollback',
                        'message' => "잔액 부족! {$from->name} 보유액: " . number_format($from->balance) . "원 < 이체액: " . number_format($amount) . "원 → 트랜잭션 롤백",
                    ]);
            }

            // 강제 오류 시나리오: 출금 후 의도적으로 예외 발생
            $db->table('accounts')->where('id', $fromId)->update(['balance' => $from->balance - $amount]);

            if ($scene === 'error') {
                throw new \RuntimeException('입금 처리 중 예외 발생 (강제 오류 시나리오)');
            }

            $db->table('accounts')->where('id', $toId)->update(['balance' => $to->balance + $amount]);

            $db->transComplete();

            $msg = "{$from->name} → {$to->name} " . number_format($amount) . "원 이체 성공. 트랜잭션 커밋 완료.";
            return redirect()->back()->with('result', ['success' => true, 'scene' => 'success', 'message' => $msg]);

        } catch (\RuntimeException $e) {
            $db->transRollback();
            return redirect()->back()
                ->with('result', [
                    'success' => false,
                    'scene'   => 'error',
                    'message' => "예외 발생 → 자동 롤백: " . $e->getMessage(),
                ]);
        }
    }

    public function reset()
    {
        $db = db_connect();
        $db->table('accounts')->truncate();
        $db->table('accounts')->insertBatch([
            ['name' => '홍길동', 'balance' => 100000],
            ['name' => '김철수', 'balance' => 50000],
            ['name' => '이영희', 'balance' => 200000],
        ]);
        return redirect()->to(base_url('examples/transaction'))->with('success', '계좌 잔액이 초기화되었습니다.');
    }
}
