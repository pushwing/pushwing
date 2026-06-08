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
     * 기존 번호가 있으면 device_id와 type을 업데이트,
     * 없으면 신규 INSERT 후 최초 푸시 발송 대기 등록
     */
    public function registerDevice(string $hp, string $deviceId, string $os): string
    {
        $exists = $this->where('hp', $hp)->first();

        if ($exists) {
            $this->where('hp', $hp)->set([
                'device_id' => $deviceId,
                'type'      => $os,
            ])->update();
        } else {
            $this->insert([
                'hp'        => $hp,
                'device_id' => $deviceId,
                'type'      => $os,
            ]);

            $this->queueWelcomePush($hp);
        }

        return 'success';
    }

    /**
     * 최초 설치 시 환영 푸시를 push_wait 에 등록
     */
    private function queueWelcomePush(string $hp): void
    {
        $db = \Config\Database::connect();
        $db->table('push_wait')->insert([
            'hp'        => $hp,
            'client_id' => 2,
            'subject'   => '푸시윙을 설치해주셔서 감사합니다!',
            'contents'  => '푸시윙은 앱이 없는 웹사이트를 위한 통합 푸시 알림 수신 서비스입니다.',
            'ymd'       => date('ymd'),
            'time'      => date('G'),
            'timestamp' => time(),
            'url'       => 'http://www.pushwing.com/main/partners',
        ]);
    }

    /**
     * push_end 테이블에서 id로 푸시 내용 조회
     */
    public function getPushEnd(int $id): ?array
    {
        $db = \Config\Database::connect();
        return $db->table('push_end')->where('id', $id)->get()->getRowArray();
    }
}
