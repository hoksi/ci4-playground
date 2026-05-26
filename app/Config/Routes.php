<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ─── 홈 ───────────────────────────────────────────────
$routes->get('/', 'Home::index');

// ─── 예제 섹션 ─────────────────────────────────────────
$routes->group('examples', function ($routes) {

    // 1. 라우팅
    $routes->get('routing',                    'Examples\Routing::index');
    $routes->get('routing/params/(:num)',       'Examples\Routing::params/$1');
    $routes->get('routing/named',              'Examples\Routing::named', ['as' => 'routing.named']);
    $routes->get('routing/redirect',           'Examples\Routing::redirect');
    $routes->get('routing/redirected',         'Examples\Routing::redirected');
    $routes->match(['get','post'], 'routing/method', 'Examples\Routing::method');

    // 2. 컨트롤러
    $routes->get('controllers',                'Examples\Controllers::index');
    $routes->get('controllers/request',        'Examples\Controllers::request');
    $routes->post('controllers/store',         'Examples\Controllers::store');
    $routes->get('controllers/response',       'Examples\Controllers::response');

    // 3. 뷰
    $routes->get('views',                      'Examples\Views::index');
    $routes->get('views/layout',               'Examples\Views::layout');
    $routes->get('views/partial',              'Examples\Views::partial');
    $routes->get('views/cell',                 'Examples\Views::cell');

    // 4. 모델 & 데이터베이스
    $routes->get('models',                     'Examples\Models::index');
    $routes->get('models/querybuilder',        'Examples\Models::queryBuilder');
    $routes->get('models/pagination',          'Examples\Models::pagination');
    $routes->get('models/entity',              'Examples\Models::entity');

    // 5. 필터
    $routes->get('filters',                    'Examples\Filters::index');
    $routes->get('filters/public',             'Examples\Filters::publicPage');
    $routes->get('filters/protected',          'Examples\Filters::protectedPage', ['filter' => 'auth-example']);
    $routes->get('filters/login',              'Examples\Filters::login');
    $routes->get('filters/logout',             'Examples\Filters::logout');

    // 6. API
    $routes->get('api',                        'Examples\Api::index');
    $routes->group('api', ['namespace' => 'App\Controllers\Examples'], function ($routes) {
        $routes->get('users',                  'Api::users');
        $routes->get('users/(:num)',           'Api::user/$1');
        $routes->post('users',                 'Api::createUser');
    });

    // 8. 파일 업로드
    $routes->get('fileupload',                   'Examples\FileUpload::index');
    $routes->post('fileupload/upload',           'Examples\FileUpload::upload');
    $routes->post('fileupload/multi',            'Examples\FileUpload::multi');
    $routes->get('fileupload/delete/(:segment)', 'Examples\FileUpload::delete/$1');

    // 7. 게시판 (실전 CRUD)
    $routes->get('board',                      'Examples\Board::index');
    $routes->get('board/create',               'Examples\Board::create');
    $routes->post('board/store',               'Examples\Board::store');
    $routes->get('board/(:num)',               'Examples\Board::show/$1');
    $routes->get('board/(:num)/edit',          'Examples\Board::edit/$1');
    $routes->post('board/(:num)/update',       'Examples\Board::update/$1');
    $routes->get('board/(:num)/delete',        'Examples\Board::delete/$1');
});
