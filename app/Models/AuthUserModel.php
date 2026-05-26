<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthUserModel extends Model
{
    protected $table         = 'auth_users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $allowedFields = ['username', 'email', 'password', 'remember_token'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findByEmail(string $email): ?object
    {
        return $this->where('email', strtolower(trim($email)))->first();
    }
}
