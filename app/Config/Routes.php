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
    $routes->match(['get', 'post'], 'filters/login',  'Examples\Filters::login');
    $routes->get('filters/logout',                    'Examples\Filters::logout');

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

    // 9. 세션 & 쿠키
    $routes->get('session',                      'Examples\Session::index');
    $routes->post('session/set',                 'Examples\Session::setSession');
    $routes->post('session/remove',              'Examples\Session::removeSession');
    $routes->post('session/destroy',             'Examples\Session::destroySession');
    $routes->post('session/flash',               'Examples\Session::setFlash');
    $routes->post('session/cookie/set',          'Examples\Session::setCookie');
    $routes->post('session/cookie/delete',       'Examples\Session::deleteCookie');

    // 10. 유효성 검사
    $routes->get('validation',                   'Examples\Validation::index');
    $routes->post('validation/basic',            'Examples\Validation::basic');
    $routes->post('validation/custom',           'Examples\Validation::custom');

    // 11. HTTP 클라이언트
    $routes->get('httpclient',                   'Examples\HttpClient::index');
    $routes->post('httpclient/get',              'Examples\HttpClient::getRequest');
    $routes->post('httpclient/post',             'Examples\HttpClient::postRequest');
    $routes->post('httpclient/list',             'Examples\HttpClient::getList');

    // 12. 이메일 발송
    $routes->get('email',                        'Examples\Email::index');
    $routes->post('email/send',                  'Examples\Email::send');

    // 13. 서비스 레이어
    $routes->get('servicelayer',                 'Examples\ServiceLayer::index');
    $routes->get('servicelayer/search',          'Examples\ServiceLayer::search');
    $routes->post('servicelayer/create',         'Examples\ServiceLayer::create');

    // 14. 커스텀 헬퍼
    $routes->get('helper',                       'Examples\Helper::index');

    // 15. 캐싱
    $routes->get('cache',                        'Examples\Cache::index');
    $routes->get('cache/clear',                  'Examples\Cache::clear');
    $routes->get('cache/clear-all',              'Examples\Cache::clearAll');

    // 16. 다국어
    $routes->get('lang',                         'Examples\Lang::index');
    $routes->get('lang/switch/(:segment)',       'Examples\Lang::switchLang/$1');

    // 17. 이벤트 시스템
    $routes->get('events',                       'Examples\EventSystem::index');
    $routes->post('events/trigger',              'Examples\EventSystem::trigger');

    // 18. CLI 커맨드
    $routes->get('cli',                          'Examples\CliCommand::index');
    $routes->post('cli/run',                     'Examples\CliCommand::run');

    // 21. 테스팅
    $routes->match(['get','post'], 'testing',    'Examples\Testing::index');

    // 22. DB 트랜잭션
    $routes->get('transaction',          'Examples\Transaction::index');
    $routes->post('transaction/transfer','Examples\Transaction::transfer');
    $routes->get('transaction/reset',    'Examples\Transaction::reset');

    // 23. 로깅
    $routes->get('logging',              'Examples\Logging::index');
    $routes->post('logging/write',       'Examples\Logging::write');

    // 24. 예외 처리
    $routes->get('exception',            'Examples\ExceptionHandling::index');
    $routes->post('exception/demo',      'Examples\ExceptionHandling::demo');

    // 25. Throttler
    $routes->get('throttler',            'Examples\Throttler::index');
    $routes->post('throttler/hit',       'Examples\Throttler::hit');
    $routes->get('throttler/reset',      'Examples\Throttler::reset');

    // 26. Model 콜백
    $routes->get('modelcallback',        'Examples\ModelCallback::index');
    $routes->post('modelcallback/store', 'Examples\ModelCallback::store');
    $routes->get('modelcallback/reset',  'Examples\ModelCallback::reset');

    // 27. Config 환경 분리
    $routes->get('configenv', 'Examples\ConfigEnv::index');

    // 28. 유효성 검사 고급
    $routes->get('advancedvalidation',              'Examples\AdvancedValidation::index');
    $routes->post('advancedvalidation/basic',       'Examples\AdvancedValidation::basic');
    $routes->post('advancedvalidation/group',       'Examples\AdvancedValidation::group');
    $routes->post('advancedvalidation/conditional', 'Examples\AdvancedValidation::conditional');

    // 29. API 인증 (API Key)
    $routes->get('apiauth',                   'Examples\ApiAuth::index');
    $routes->post('apiauth/generate',         'Examples\ApiAuth::generate');
    $routes->post('apiauth/revoke',           'Examples\ApiAuth::revoke');
    $routes->post('apiauth/test',             'Examples\ApiAuth::testApi');
    $routes->get('apiauth/protected',         'Examples\ApiAuth::protected', ['filter' => 'api-key']);

    // 30. Security 클래스
    $routes->get('securitydemo',              'Examples\SecurityDemo::index');
    $routes->post('securitydemo/sanitize',    'Examples\SecurityDemo::sanitize');
    $routes->post('securitydemo/xss',         'Examples\SecurityDemo::xss');

    // 31. Query Builder 고급
    $routes->get('querybuilderadvanced',            'Examples\QueryBuilderAdvanced::index');
    $routes->get('querybuilderadvanced/joins',      'Examples\QueryBuilderAdvanced::joins');
    $routes->get('querybuilderadvanced/subquery',   'Examples\QueryBuilderAdvanced::subquery');
    $routes->get('querybuilderadvanced/aggregate',  'Examples\QueryBuilderAdvanced::aggregate');
    $routes->get('querybuilderadvanced/raw',        'Examples\QueryBuilderAdvanced::raw');

    // 7. 게시판 (실전 CRUD)
    $routes->get('board',                      'Examples\Board::index');
    $routes->get('board/create',               'Examples\Board::create');
    $routes->post('board/store',               'Examples\Board::store');
    $routes->get('board/(:num)',               'Examples\Board::show/$1');
    $routes->get('board/(:num)/edit',          'Examples\Board::edit/$1');
    $routes->post('board/(:num)/update',       'Examples\Board::update/$1');
    $routes->get('board/(:num)/delete',        'Examples\Board::delete/$1');

    // 32. 이미지 처리
    $routes->get('imageprocess',               'Examples\ImageProcess::index');
    $routes->post('imageprocess/upload',       'Examples\ImageProcess::upload');

    // 33. 암호화 & 해싱
    $routes->get('encryption',                 'Examples\EncryptionDemo::index');
    $routes->post('encryption/hash',           'Examples\EncryptionDemo::hash');
    $routes->post('encryption/verify',         'Examples\EncryptionDemo::verify');

    // 34. Entity 심화
    $routes->get('entityadvanced',             'Examples\EntityAdvanced::index');
    $routes->post('entityadvanced/demo',       'Examples\EntityAdvanced::demo');

    // 35. Repository 패턴
    $routes->get('repository',                 'Examples\Repository::index');
    $routes->get('repository/list',            'Examples\Repository::list');
    $routes->post('repository/store',          'Examples\Repository::store');

    // 36. Pagination 심화
    $routes->get('paginationadvanced',         'Examples\PaginationAdvanced::index');
    $routes->get('paginationadvanced/data',    'Examples\PaginationAdvanced::data');

    // 37. 다중 DB 연결
    $routes->get('multidb',                    'Examples\MultiDb::index');
    $routes->post('multidb/query',             'Examples\MultiDb::query');

    // 38. 회원 인증
    $routes->get('auth',                       'Examples\Auth::index');
    $routes->match(['get', 'post'], 'auth/register', 'Examples\Auth::register');
    $routes->match(['get', 'post'], 'auth/login',    'Examples\Auth::login');
    $routes->get('auth/logout',                'Examples\Auth::logout');
    $routes->get('auth/dashboard',             'Examples\Auth::dashboard');
    $routes->post('auth/profile',              'Examples\Auth::profile');

    // 39. RESTful API v2 (JWT)
    $routes->get('apiv2',                      'Examples\ApiV2::index');
    $routes->post('apiv2/auth/token',          'Examples\ApiV2::token');
    $routes->get('apiv2/users',                'Examples\ApiV2::users');
    $routes->get('apiv2/users/(:num)',         'Examples\ApiV2::user/$1');
    $routes->post('apiv2/users',               'Examples\ApiV2::createUser');

    // 40. 큐 시스템
    $routes->get('queue',                      'Examples\Queue::index');
    $routes->post('queue/push',                'Examples\Queue::push');
    $routes->post('queue/process',             'Examples\Queue::process');
    $routes->post('queue/retry',               'Examples\Queue::retry');
    $routes->get('queue/clear',                'Examples\Queue::clear');
    $routes->get('queue/stats',                'Examples\Queue::stats');

    // 41. CSV/Excel 내보내기·가져오기
    $routes->get('csv-excel',                  'Examples\CsvExcel::index');
    $routes->get('csv-excel/export-csv',       'Examples\CsvExcel::exportCsv');
    $routes->post('csv-excel/import-csv',      'Examples\CsvExcel::importCsv');
    $routes->get('csv-excel/export-excel',     'Examples\CsvExcel::exportExcel');
    $routes->post('csv-excel/import-excel',    'Examples\CsvExcel::importExcel');
    $routes->get('csv-excel/reset',            'Examples\CsvExcel::reset');

    // 42. CI4 공식 Queue
    $routes->get('official-queue',             'Examples\OfficialQueue::index');
    $routes->post('official-queue/push',       'Examples\OfficialQueue::push');
    $routes->post('official-queue/process',    'Examples\OfficialQueue::processNext');
    $routes->post('official-queue/retry',      'Examples\OfficialQueue::retryFailed');
    $routes->post('official-queue/clear',      'Examples\OfficialQueue::clear');
    $routes->get('official-queue/stats',       'Examples\OfficialQueue::statsJson');

    // 43. Task Scheduler
    $routes->get('taskscheduler',              'Examples\TaskScheduler::index');
    $routes->post('taskscheduler/run',         'Examples\TaskScheduler::run');

    // 44. PDF 생성
    $routes->get('pdfgeneration',              'Examples\PdfGeneration::index');
    $routes->get('pdfgeneration/products',     'Examples\PdfGeneration::products');
    $routes->get('pdfgeneration/posts',        'Examples\PdfGeneration::posts');
    $routes->get('pdfgeneration/invoice',      'Examples\PdfGeneration::invoice');
});
