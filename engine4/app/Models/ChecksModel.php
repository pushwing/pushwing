<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecksModel extends Model
{
    protected $table      = 'checks';
    protected $primaryKey = 'id';

    /**
     * 앱 시작 시 필요한 플래그 정보 반환
     *
     * @return array|null ['code_value' => ..., 'and_ver' => ..., 'ios_ver' => ..., 'update' => ...]
     */
    public function getFlags(): ?array
    {
        $db = \Config\Database::connect();

        $adStart = $db->table('checks')
            ->select('code_value')
            ->where('code_name', 'ad_start')
            ->get()->getRowArray();

        $curVer = $db->table('checks')
            ->select('code_value')
            ->where('code_name', 'cur_ver')
            ->get()->getRowArray();

        $update = $db->table('checks')
            ->select('code_value')
            ->where('code_name', 'update')
            ->get()->getRowArray();

        if (! $adStart) {
            return null;
        }

        $ver = explode('|', $curVer['code_value'] ?? '|');

        return [
            'code_value' => $adStart['code_value'],
            'and_ver'    => $ver[0] ?? '',
            'ios_ver'    => $ver[1] ?? '',
            'update'     => $update['code_value'] ?? '0',
        ];
    }
}
