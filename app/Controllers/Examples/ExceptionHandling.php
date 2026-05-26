<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class ExceptionHandling extends BaseController
{
    public function index(): string
    {
        return view('examples/exception/index', ['title' => '예외 처리']);
    }

    public function demo()
    {
        $type = $this->request->getPost('type') ?? 'trycatch';

        switch ($type) {
            case '404':
                throw PageNotFoundException::forPageNotFound('데모용 404: 존재하지 않는 리소스입니다.');

            case 'custom':
                try {
                    $this->riskyOperation(true);
                } catch (\InvalidArgumentException $e) {
                    return redirect()->back()->with('result', [
                        'type'    => 'custom',
                        'caught'  => true,
                        'class'   => get_class($e),
                        'message' => $e->getMessage(),
                        'code'    => $e->getCode(),
                    ]);
                }
                break;

            case 'trycatch':
            default:
                try {
                    $this->riskyOperation(false);
                    return redirect()->back()->with('result', [
                        'type'   => 'trycatch',
                        'caught' => false,
                        'message' => '예외 없이 정상 처리되었습니다.',
                    ]);
                } catch (\RuntimeException $e) {
                    return redirect()->back()->with('result', [
                        'type'    => 'trycatch',
                        'caught'  => true,
                        'class'   => get_class($e),
                        'message' => $e->getMessage(),
                        'code'    => $e->getCode(),
                    ]);
                }
        }

        return redirect()->back();
    }

    private function riskyOperation(bool $throwCustom): void
    {
        if ($throwCustom) {
            throw new \InvalidArgumentException('유효하지 않은 인자입니다.', 422);
        }
        throw new \RuntimeException('외부 서비스 연결 실패 (시뮬레이션)', 503);
    }
}
