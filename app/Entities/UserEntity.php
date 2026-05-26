<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    /**
     * 외부 키 ↔ 내부 속성 매핑.
     * email_address 로도 $entity->email 에 접근 가능.
     */
    protected $datamap = [
        'email_address' => 'email',
    ];

    /**
     * Time 객체로 자동 변환되는 컬럼.
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 속성별 캐스팅 타입.
     * 입력은 그대로, 접근 시 지정된 타입으로 변환.
     */
    protected $casts = [
        'id'        => 'integer',
        'is_active' => 'boolean',
        'metadata'  => 'json-array',
        'tags'      => 'csv',
        'score'     => 'float',
    ];

    // ─── Virtual Properties (getter) ──────────────────────
    public function getFullName(): string
    {
        $first = $this->attributes['first_name'] ?? '';
        $last  = $this->attributes['last_name'] ?? '';

        return trim($first . ' ' . $last);
    }

    public function getDisplayName(): string
    {
        $name = $this->getFullName();

        return $name !== '' ? $name : ($this->attributes['email'] ?? '익명');
    }

    // ─── Setter (입력 정규화) ─────────────────────────────
    public function setEmail(string $email): static
    {
        $this->attributes['email'] = strtolower(trim($email));

        return $this;
    }

    public function setFirstName(string $name): static
    {
        $this->attributes['first_name'] = ucfirst(trim($name));

        return $this;
    }

    public function setLastName(string $name): static
    {
        $this->attributes['last_name'] = ucfirst(trim($name));

        return $this;
    }
}
