<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Exceptions\HTTPException;

class HttpClient extends BaseController
{
    private string $baseApi = 'https://jsonplaceholder.typicode.com';

    public function index(): string
    {
        return view('examples/httpclient/index', ['title' => 'HTTP 클라이언트']);
    }

    public function getRequest()
    {
        try {
            $client   = \Config\Services::curlrequest();
            $response = $client->get("{$this->baseApi}/posts/1");

            $data = json_decode($response->getBody(), true);

            return view('examples/httpclient/index', [
                'title'      => 'HTTP 클라이언트',
                'tab'        => 'get',
                'result'     => $data,
                'statusCode' => $response->getStatusCode(),
                'method'     => 'GET',
                'url'        => "{$this->baseApi}/posts/1",
            ]);
        } catch (HTTPException $e) {
            return view('examples/httpclient/index', [
                'title' => 'HTTP 클라이언트',
                'tab'   => 'get',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function postRequest()
    {
        try {
            $client   = \Config\Services::curlrequest();
            $response = $client->post("{$this->baseApi}/posts", [
                'json' => [
                    'title'  => $this->request->getPost('title') ?? 'CI4 테스트',
                    'body'   => $this->request->getPost('body') ?? '본문 내용',
                    'userId' => 1,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return view('examples/httpclient/index', [
                'title'      => 'HTTP 클라이언트',
                'tab'        => 'post',
                'result'     => $data,
                'statusCode' => $response->getStatusCode(),
                'method'     => 'POST',
                'url'        => "{$this->baseApi}/posts",
                'old'        => $this->request->getPost(),
            ]);
        } catch (HTTPException $e) {
            return view('examples/httpclient/index', [
                'title' => 'HTTP 클라이언트',
                'tab'   => 'post',
                'error' => $e->getMessage(),
                'old'   => $this->request->getPost(),
            ]);
        }
    }

    public function getList()
    {
        try {
            $client   = \Config\Services::curlrequest();
            $response = $client->get("{$this->baseApi}/posts", [
                'query' => ['_limit' => 5],
            ]);

            $data = json_decode($response->getBody(), true);

            return view('examples/httpclient/index', [
                'title'      => 'HTTP 클라이언트',
                'tab'        => 'query',
                'result'     => $data,
                'statusCode' => $response->getStatusCode(),
                'method'     => 'GET',
                'url'        => "{$this->baseApi}/posts?_limit=5",
            ]);
        } catch (HTTPException $e) {
            return view('examples/httpclient/index', [
                'title' => 'HTTP 클라이언트',
                'tab'   => 'query',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
