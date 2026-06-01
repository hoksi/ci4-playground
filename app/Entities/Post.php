<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Post extends Entity
{
    protected $casts = [
        'id'    => 'integer',
        'views' => 'integer',
    ];

    public function getExcerpt(int $length = 100): string
    {
        return mb_strlen($this->content) > $length
            ? mb_substr($this->content, 0, $length) . '...'
            : $this->content;
    }

    public function getFormattedDate(): string
    {
        return ($this->created_at instanceof \CodeIgniter\I18n\Time)
            ? $this->created_at->format('Y-m-d H:i')
            : '';
    }
}
