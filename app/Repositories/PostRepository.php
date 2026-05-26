<?php

namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\PostModel;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(private PostModel $model) {}

    public function findAll(): array
    {
        return $this->model->orderBy('id', 'DESC')->findAll();
    }

    public function findById(int $id): ?object
    {
        return $this->model->find($id);
    }

    public function create(array $data): int|false
    {
        if (! $this->model->insert($data)) {
            return false;
        }

        return (int) $this->model->getInsertID();
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->delete($id);
    }

    public function findRecent(int $limit = 10): array
    {
        return $this->model->orderBy('id', 'DESC')->findAll($limit);
    }
}
