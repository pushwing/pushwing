<?php

namespace App\Models;

use CodeIgniter\Model;

class AdReportModel extends Model
{
    /**
     * 광고 노출 리포트 저장
     */
    public function insertViewReport(array $data): void
    {
        $db = \Config\Database::connect();
        $db->table('view_report')->insert($data);
    }

    /**
     * 광고 클릭 리포트 저장
     */
    public function insertClickReport(array $data): void
    {
        $db = \Config\Database::connect();
        $db->table('click_report')->insert($data);
    }
}
