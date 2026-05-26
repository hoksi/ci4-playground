<?php

namespace App\Interfaces;

interface PostRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?object;

    public function create(array $data): int|false;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function findRecent(int $limit = 10): array;
}
