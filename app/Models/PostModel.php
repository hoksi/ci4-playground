<?php

namespace App\Models;

use App\Entities\Post;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table         = 'posts';
    protected $primaryKey    = 'id';
    protected $returnType    = Post::class;
    protected $useSoftDeletes = true;

    protected $allowedFields = ['title', 'content', 'author', 'views', 'spam_status'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'title'   => 'required|min_length[2]|max_length[200]',
        'content' => 'required|min_length[10]',
        'author'  => 'required|min_length[2]|max_length[100]',
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => '제목을 입력해주세요.',
            'min_length' => '제목은 최소 2자 이상이어야 합니다.',
            'max_length' => '제목은 200자를 초과할 수 없습니다.',
        ],
        'content' => [
            'required'   => '내용을 입력해주세요.',
            'min_length' => '내용은 최소 10자 이상이어야 합니다.',
        ],
        'author' => [
            'required'   => '작성자를 입력해주세요.',
            'min_length' => '작성자는 최소 2자 이상이어야 합니다.',
        ],
    ];

    public function incrementViews(int $id): void
    {
        $this->set('views', 'views + 1', false)->where('id', $id)->update();
    }
}
