<?php

namespace App\Models;

use CodeIgniter\Model;

class PushModel extends Model
{
    protected $table      = 'push_db';
    protected $primaryKey = 'id';

    protected $allowedFields = ['hp', 'device_id', 'type'];

    /**
     * 디바이스 등록 또는 token 갱신
     *
     * SELECT → INSERT/UPDATE 패턴의 TOCTOU Race condition을 피하기 위해
     * INSERT ... ON DUPLICATE KEY UPDATE 를 사용한다.
     * (push_db.hp 에 UNIQUE 제약이 있어야 함)
     */
    public function registerDevice(string $hp, string $deviceId, string $os): string
    {
        $db = \Config\Database::connect();

        $isNew = ! $db->table('push_db')->where('hp', $hp)->countAllResults();

        $db->query(
            'INSERT INTO push_db (hp, device_id, type)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE device_id = VALUES(device_id), type = VALUES(type)',
            [$hp, $deviceId, $os]
        );

        if ($isNew) {
            $this->queueWelcomePush($hp);
        }

        return 'success';
    }

    /**
     * 최초 설치 시 환영 푸시를 push_wait 에 등록
     */
    private function queueWelcomePush(string $hp): void
    {
        $welcomeUrl = env('pushwing.welcomeUrl', 'http://www.pushwing.com/main/partners');

        \Config\Database::connect()->table('push_wait')->insert([
            'hp'        => $hp,
            'client_id' => 2,
            'subject'   => '푸시윙을 설치해주셔서 감사합니다!',
            'contents'  => '푸시윙은 앱이 없는 웹사이트를 위한 통합 푸시 알림 수신 서비스입니다.',
            'ymd'       => date('ymd'),
            'time'      => date('G'),
            'timestamp' => time(),
            'url'       => $welcomeUrl,
        ]);
    }

    /**
     * push_end 조회 — device token으로 소유권 검증 후 반환 (IDOR 방어)
     *
     * @param int    $id       push_end.id
     * @param string $deviceId 요청 기기의 FCM/GCM token
     */
    public function getPushEnd(int $id, string $deviceId): ?array
    {
        $db = \Config\Database::connect();

        $push = $db->table('push_end')->where('id', $id)->get()->getRowArray();

        if (! $push) {
            return null;
        }

        // device token → hp 역조회 후 push_end.hp 와 대조
        $device = $db->table('push_db')->where('device_id', $deviceId)->get()->getRowArray();

        if (! $device || $device['hp'] !== $push['hp']) {
            return null;
        }

        return $push;
    }
}
