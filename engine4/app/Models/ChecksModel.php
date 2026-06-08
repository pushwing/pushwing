<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecksModel extends Model
{
    protected $table      = 'checks';
    protected $primaryKey = 'id';

    /**
     * 앱 시작 시 필요한 플래그 정보를 쿼리 1회로 반환
     *
     * @return array|null ['code_value' => ..., 'and_ver' => ..., 'ios_ver' => ..., 'update' => ...]
     */
    public function getFlags(): ?array
    {
        $rows = $this->select('code_name, code_value')
            ->whereIn('code_name', ['ad_start', 'cur_ver', 'update'])
            ->findAll();

        if (empty($rows)) {
            return null;
        }

        $map = array_column($rows, 'code_value', 'code_name');

        if (! isset($map['ad_start'])) {
            return null;
        }

        $ver = explode('|', $map['cur_ver'] ?? '|');

        return [
            'code_value' => $map['ad_start'],
            'and_ver'    => $ver[0] ?? '',
            'ios_ver'    => $ver[1] ?? '',
            'update'     => $map['update'] ?? '0',
        ];
    }
}
