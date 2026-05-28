<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $allowedFields = ['type', 'title', 'message', 'is_read'];
    protected $useTimestamps = true;
    protected $updatedField  = '';   // updated_at 컬럼 없음

    public function countUnread(): int
    {
        return $this->where('is_read', 0)->countAllResults();
    }

    public function markRead(int $id): void
    {
        $this->update($id, ['is_read' => 1]);
    }

    public function markAllRead(): void
    {
        $this->where('is_read', 0)->set(['is_read' => 1])->update();
    }
}
