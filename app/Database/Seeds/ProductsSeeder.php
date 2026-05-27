<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $now  = date('Y-m-d H:i:s');
        $data = [
            ['name' => '애플 아이폰 15',       'category' => '스마트폰', 'price' => 1250000, 'stock' => 45, 'created_at' => $now],
            ['name' => '삼성 갤럭시 S24',       'category' => '스마트폰', 'price' => 1190000, 'stock' => 62, 'created_at' => $now],
            ['name' => 'LG 그램 노트북',        'category' => '노트북',   'price' => 1890000, 'stock' => 23, 'created_at' => $now],
            ['name' => '애플 맥북 에어 M3',     'category' => '노트북',   'price' => 1690000, 'stock' => 17, 'created_at' => $now],
            ['name' => 'Sony WH-1000XM5',       'category' => '헤드폰',   'price' =>  449000, 'stock' => 88, 'created_at' => $now],
            ['name' => '삼성 QLED TV 55인치',   'category' => 'TV',       'price' => 1350000, 'stock' => 12, 'created_at' => $now],
            ['name' => '다이슨 에어랩',          'category' => '미용가전', 'price' =>  699000, 'stock' => 34, 'created_at' => $now],
            ['name' => '닌텐도 스위치 OLED',    'category' => '게임기',   'price' =>  399000, 'stock' => 56, 'created_at' => $now],
            ['name' => '아이패드 프로 12.9',    'category' => '태블릿',   'price' => 1590000, 'stock' => 29, 'created_at' => $now],
            ['name' => '로지텍 MX Master 3',    'category' => '주변기기', 'price' =>  139000, 'stock' => 102,'created_at' => $now],
        ];

        $this->db->table('playground_products')->insertBatch($data);
    }
}
