<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Email extends BaseController
{
    public function index(): string
    {
        return view('examples/email/index', ['title' => '이메일 발송']);
    }

    public function send()
    {
        $rules = [
            'to'      => ['label' => '받는 사람', 'rules' => 'required|valid_email'],
            'subject' => ['label' => '제목',      'rules' => 'required|max_length[200]'],
            'message' => ['label' => '본문',       'rules' => 'required'],
        ];

        if (! $this->validate($rules)) {
            return view('examples/email/index', [
                'title'  => '이메일 발송',
                'tab'    => 'send',
                'errors' => $this->validator->getErrors(),
                'old'    => $this->request->getPost(),
            ]);
        }

        $to      = $this->request->getPost('to');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');
        $isHtml  = (bool) $this->request->getPost('is_html');

        $emailService = \Config\Services::email();
        $emailService->setTo($to);
        $emailService->setFrom('noreply@ci4playground.local', 'CI4 Playground');
        $emailService->setSubject($subject);
        $emailService->setMessage($isHtml ? $message : nl2br(esc($message)));
        $emailService->setMailType($isHtml ? 'html' : 'text');

        $sent   = false;
        $errMsg = '';

        try {
            $sent = $emailService->send(false);
        } catch (\Throwable $e) {
            $errMsg = $e->getMessage();
        }

        // send() 이후 호출해야 헤더·본문이 준비된 상태로 출력됨
        $preview = $emailService->printDebugger(['headers', 'subject', 'body']);

        if (! $sent && $errMsg === '') {
            $errMsg = $emailService->printDebugger();
        }

        return view('examples/email/index', [
            'title'   => '이메일 발송',
            'tab'     => 'send',
            'sent'    => $sent,
            'errMsg'  => $errMsg,
            'preview' => $preview,
            'old'     => $this->request->getPost(),
        ]);
    }
}
