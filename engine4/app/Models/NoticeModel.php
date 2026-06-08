<?php

namespace App\Models;

use CodeIgniter\Model;

class NoticeModel extends Model
{
    protected $table      = 'board';
    protected $primaryKey = 'id';

    /**
     * 공지사항 목록 (최근 10건)
     */
    public function getList(): array
    {
        return $this->select('id, subject, hit, reg_date')
            ->where('table_id', 2)
            ->where('is_delete', 'N')
            ->where('is_list', 'Y')
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->findAll();
    }

    /**
     * 공지사항 상세
     */
    public function getView(int $id): ?array
    {
        return $this->select('id, subject, contents, hit, reg_date')
            ->where('id', $id)
            ->where('is_delete', 'N')
            ->where('is_list', 'Y')
            ->first();
    }
}
