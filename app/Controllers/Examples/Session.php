<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Session extends BaseController
{
    public function index(): string
    {
        return view('examples/session/index', [
            'title'       => '세션 & 쿠키',
            'sessionData' => session()->get(),
            'cookieVal'   => $this->request->getCookie('playground_cookie') ?? '',
        ]);
    }

    public function setSession()
    {
        $key   = $this->request->getPost('key');
        $value = $this->request->getPost('value');

        if ($key !== '' && $key !== null) {
            session()->set(esc($key), esc($value));
            session()->setFlashdata('success', "세션 [{$key}] 저장 완료");
        }
        return redirect()->to(base_url('examples/session'))->with('tab', 'session');
    }

    public function removeSession()
    {
        $key = $this->request->getPost('key');
        if ($key) {
            session()->remove($key);
            session()->setFlashdata('success', "세션 [{$key}] 삭제 완료");
        }
        return redirect()->to(base_url('examples/session'))->with('tab', 'session');
    }

    public function destroySession()
    {
        session()->destroy();
        return redirect()->to(base_url('examples/session'))->with('tab', 'session');
    }

    public function setFlash()
    {
        session()->setFlashdata('flash_demo', $this->request->getPost('msg') ?? '플래시 메시지 예제입니다.');
        return redirect()->to(base_url('examples/session'))->with('tab', 'flash');
    }

    public function setCookie()
    {
        $value = $this->request->getPost('value') ?? 'hello';
        $res   = $this->response->setCookie('playground_cookie', $value, 3600);
        session()->setFlashdata('success', "쿠키 [playground_cookie] 저장 완료 (1시간)");
        return redirect()->to(base_url('examples/session'))->with('tab', 'cookie');
    }

    public function deleteCookie()
    {
        $this->response->deleteCookie('playground_cookie');
        session()->setFlashdata('success', "쿠키 [playground_cookie] 삭제 완료");
        return redirect()->to(base_url('examples/session'))->with('tab', 'cookie');
    }
}
