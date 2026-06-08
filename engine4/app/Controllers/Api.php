<?php

namespace App\Controllers;

use App\Models\ChecksModel;
use App\Models\PushModel;
use App\Models\AdReportModel;
use App\Models\NoticeModel;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    private ChecksModel  $checksModel;
    private PushModel    $pushModel;
    private AdReportModel $adReportModel;
    private NoticeModel  $noticeModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);

        $this->checksModel   = new ChecksModel();
        $this->pushModel     = new PushModel();
        $this->adReportModel = new AdReportModel();
        $this->noticeModel   = new NoticeModel();
    }

    /**
     * 앱 시작 시 광고 on/off 플래그, 앱 버전, 업데이트 여부 반환
     * 결과는 5분간 캐싱 (거의 변경되지 않는 설정값)
     *
     * GET /api/go
     */
    public function go(): ResponseInterface
    {
        $cache  = cache();
        $result = $cache->get('api_go_flags');

        if ($result === null) {
            $result = $this->checksModel->getFlags();

            if (! $result) {
                return $this->response->setStatusCode(418)->setJSON(['message' => 'no data']);
            }

            $cache->save('api_go_flags', $result, 300); // 5분
        }

        return $this->response->setJSON($result);
    }

    /**
     * 디바이스 등록 (신규) 또는 token 갱신 (기존)
     *
     * POST /api/sd
     * Body: hp(휴대폰번호), cd(device token), os(1=iOS, 2=Android)
     */
    public function sd(): ResponseInterface
    {
        $rules = [
            'hp' => ['label' => '휴대폰번호', 'rules' => 'required|regex_match[/^01[016789][0-9]{7,8}$/]'],
            'cd' => ['label' => 'device token', 'rules' => 'required|max_length[255]'],
            'os' => ['label' => 'OS', 'rules' => 'required|in_list[1,2]'],
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(406)
                ->setJSON(['message' => $this->validator->getErrors()]);
        }

        $message = $this->pushModel->registerDevice(
            $this->request->getPost('hp'),
            $this->request->getPost('cd'),
            $this->request->getPost('os')
        );

        return $this->response->setJSON(['message' => $message]);
    }

    /**
     * push_end id에 해당하는 푸시 내용 반환
     * device token 소유권 검증 후 반환 (IDOR 방어)
     *
     * GET /api/fd/{id}?cd={device_token}
     */
    public function fd(int $id): ResponseInterface
    {
        $cd = $this->request->getGet('cd');

        if (empty($cd)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'device token required']);
        }

        $result = $this->pushModel->getPushEnd($id, $cd);

        if (! $result) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'not found']);
        }

        return $this->response->setJSON($result);
    }

    /**
     * 광고 노출 리포트
     *
     * POST /api/vr
     */
    public function vr(): ResponseInterface
    {
        $rules = [
            'sid' => 'required|integer',
            'cd'  => 'required|max_length[255]',
            'ty'  => 'required|in_list[1,2]',
            'av'  => 'required|max_length[20]',
            'hp'  => 'required|regex_match[/^01[016789][0-9]{7,8}$/]',
            'sq'  => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(406)
                ->setJSON(['message' => $this->validator->getErrors()]);
        }

        $this->adReportModel->insertViewReport([
            'ad_seq'      => $this->request->getPost('sid'),
            'client_code' => $this->request->getPost('cd'),
            'type'        => $this->request->getPost('ty'),
            'app_ver'     => $this->request->getPost('av'),
            'hp'          => $this->request->getPost('hp'),
            'push_seq'    => $this->request->getPost('sq'),
            'view_date'   => time(),
            'ymd'         => date('ymd'),
            'time'        => date('G'),
        ]);

        return $this->response->setJSON(['message' => 'success']);
    }

    /**
     * 광고 클릭 리포트
     *
     * POST /api/cr
     */
    public function cr(): ResponseInterface
    {
        $rules = [
            'sid' => 'required|integer',
            'cd'  => 'required|max_length[255]',
            'ty'  => 'required|in_list[1,2]',
            'av'  => 'required|max_length[20]',
            'hp'  => 'required|regex_match[/^01[016789][0-9]{7,8}$/]',
            'sq'  => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(406)
                ->setJSON(['message' => $this->validator->getErrors()]);
        }

        $this->adReportModel->insertClickReport([
            'ad_seq'      => $this->request->getPost('sid'),
            'client_code' => $this->request->getPost('cd'),
            'type'        => $this->request->getPost('ty'),
            'app_ver'     => $this->request->getPost('av'),
            'hp'          => $this->request->getPost('hp'),
            'push_seq'    => $this->request->getPost('sq'),
            'click_date'  => time(),
            'ymd'         => date('ymd'),
            'time'        => date('G'),
        ]);

        return $this->response->setJSON(['message' => 'success']);
    }

    /**
     * 공지사항 목록 (최근 10건)
     * 결과는 10분간 캐싱
     *
     * GET /api/nl
     */
    public function nl(): ResponseInterface
    {
        $cache  = cache();
        $result = $cache->get('api_notice_list');

        if ($result === null) {
            $result = $this->noticeModel->getList();
            $cache->save('api_notice_list', $result, 600); // 10분
        }

        return $this->response->setJSON($result);
    }

    /**
     * 공지사항 상세
     *
     * GET /api/nv/{id}
     */
    public function nv(int $id): ResponseInterface
    {
        $result = $this->noticeModel->getView($id);

        if (! $result) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'not found']);
        }

        return $this->response->setJSON($result);
    }
}
