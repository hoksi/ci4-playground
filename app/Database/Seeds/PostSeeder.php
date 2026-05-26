<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title'   => 'CodeIgniter 4 시작하기',
                'content' => "CodeIgniter 4는 PHP 프레임워크 중 빠르고 가벼운 것으로 유명합니다.\n\nMVC 패턴을 기반으로 하며, 풍부한 내장 라이브러리와 간결한 문법이 특징입니다.\n\n이 예제 프로젝트를 통해 CI4의 주요 기능을 직접 체험해보세요.",
                'author'  => '김철수',
                'views'   => 142,
            ],
            [
                'title'   => '라우팅의 기초',
                'content' => "CI4의 라우팅 시스템은 URL과 컨트롤러를 유연하게 연결해줍니다.\n\n그룹 라우팅, Named Route, 와일드카드 등 다양한 기능을 제공합니다.\n\nRoutes.php 파일에서 모든 라우팅 규칙을 중앙 관리할 수 있습니다.",
                'author'  => '이영희',
                'views'   => 98,
            ],
            [
                'title'   => 'Model과 데이터베이스 활용',
                'content' => "CI4의 Model 클래스는 데이터베이스 테이블과 매핑되어 CRUD 작업을 간편하게 처리합니다.\n\nQuery Builder를 통해 복잡한 쿼리도 PHP 코드로 안전하게 작성할 수 있습니다.\n\n마이그레이션과 시더를 활용하면 팀 협업 시 DB 스키마를 손쉽게 공유할 수 있습니다.",
                'author'  => '박민준',
                'views'   => 215,
            ],
            [
                'title'   => '필터로 미들웨어 구현하기',
                'content' => "CI4의 필터는 요청 전/후에 공통 로직을 처리하는 미들웨어 역할을 합니다.\n\n인증 체크, CORS 설정, 로깅, 속도 제한 등을 필터로 구현할 수 있습니다.\n\n라우트 그룹에 필터를 적용하면 여러 페이지에 일괄 적용됩니다.",
                'author'  => '최지수',
                'views'   => 67,
            ],
            [
                'title'   => 'RESTful API 개발',
                'content' => "CI4로 JSON 기반의 RESTful API를 쉽게 개발할 수 있습니다.\n\nResourceController를 사용하면 CRUD API를 빠르게 구성할 수 있고, HTTP 상태 코드와 에러 처리도 간편합니다.\n\n이 예제에서는 게시글 API를 통해 실제 API 개발 패턴을 알아봅니다.",
                'author'  => '정현우',
                'views'   => 183,
            ],
            [
                'title'   => 'View Cell로 위젯 만들기',
                'content' => "View Cell은 독립적인 로직과 뷰를 가진 재사용 가능한 컴포넌트입니다.\n\n최근 게시글 목록, 카테고리 메뉴, 쇼핑 카트 등을 View Cell로 구현하면 코드 재사용성이 높아집니다.\n\nCI4 4.3부터 더욱 강력해진 Cell 기능을 활용해보세요.",
                'author'  => '윤서연',
                'views'   => 54,
            ],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($posts as &$post) {
            $post['created_at'] = $now;
            $post['updated_at'] = $now;
        }

        $this->db->table('posts')->insertBatch($posts);
    }
}
