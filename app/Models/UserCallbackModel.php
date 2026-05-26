<?php

namespace App\Models;

use CodeIgniter\Model;

class UserCallbackModel extends Model
{
    protected $table            = 'users_demo';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['username', 'email', 'password', 'role', 'status'];
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = true;
    protected $allowCallbacks   = true;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    protected $afterFind    = ['maskPassword'];

    protected function hashPassword(array $data): array
    {
        if (! empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    protected function maskPassword(array $data): array
    {
        if (isset($data['data'])) {
            if (is_array($data['data']) && isset($data['data']['id'])) {
                // 단일 레코드
                $data['data']['password'] = '••••••••';
            } elseif (is_array($data['data'])) {
                // 목록
                foreach ($data['data'] as &$row) {
                    if (is_array($row)) {
                        $row['password'] = '••••••••';
                    }
                }
                unset($row);
            }
        }
        return $data;
    }
}
