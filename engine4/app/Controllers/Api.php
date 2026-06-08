<?php

namespace App\Controllers;

use App\Models\ChecksModel;
use App\Models\PushModel;
use App\Models\AdReportModel;
use App\Models\NoticeModel;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    /**
     * 앱 시작 시 광고 on/off 플래그, 앱 버전, 업데이트 여부 반환
     *
     * GET /api/go
     *
     * @return ResponseInterface
     */
    public function go(): ResponseInterface
    {
        $model = new ChecksModel();
        $result = $model->getFlags();

        if (! $result) {
            return $this->response->setStatusCode(418)->setJSON(['message' => 'no data']);
        }

        return $this->response->setJSON($result);
    }

    /**
     * 디바이스 등록 (신규) 또는 token 갱신 (기존)
     *
     * POST /api/sd
     * Body: hp(휴대폰번호), cd(device token), os(1=iOS, 2=Android)
     *
     * @return ResponseInterface
     */
    public function sd(): ResponseInterface
    {
        $rules = [
            'hp' => 'required|max_length[20]',
            'cd' => 'required|max_length[255]',
            'os' => 'required|in_list[1,2]',
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(406)
                ->setJSON(['message' => $this->validator->getErrors()]);
        }

        $hp = $this->request->getPost('hp');
        $cd = $this->request->getPost('cd');
        $os = $this->request->getPost('os');

        $model = new PushModel();
        $message = $model->registerDevice($hp, $cd, $os);

        return $this->response->setJSON(['message' => $message]);
    }

    /**
     * push_end id에 해당하는 푸시 내용 반환
     *
     * GET /api/fd/{id}
     *
     * @param int $id
     * @return ResponseInterface
     */
    public function fd(int $id): ResponseInterface
    {
        $model = new PushModel();
        $result = $model->getPushEnd($id);

        if (! $result) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'not found']);
        }

        return $this->response->setJSON($result);
    }

    /**
     * 광고 노출 리포트
     *
     * POST /api/vr
     * Body: sid(광고번호), cd(클라이언트코드), ty(위치 1=초기화면 2=푸시내용),
     *       av(앱버전), hp(휴대폰번호), sq(푸시번호)
     *
     * @return ResponseInterface
     */
    public function vr(): ResponseInterface
    {
        $rules = [
            'sid' => 'required|integer',
            'cd'  => 'required|max_length[50]',
            'ty'  => 'required|in_list[1,2]',
            'av'  => 'required|max_length[20]',
            'hp'  => 'required|max_length[20]',
            'sq'  => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(406)
                ->setJSON(['message' => $this->validator->getErrors()]);
        }

        $data = [
            'ad_seq'      => $this->request->getPost('sid'),
            'client_code' => $this->request->getPost('cd'),
            'type'        => $this->request->getPost('ty'),
            'app_ver'     => $this->request->getPost('av'),
            'hp'          => $this->request->getPost('hp'),
            'push_seq'    => $this->request->getPost('sq'),
            'view_date'   => time(),
            'ymd'         => date('ymd'),
            'time'        => date('G'),
        ];

        $model = new AdReportModel();
        $model->insertViewReport($data);

        return $this->response->setJSON(['message' => 'success']);
    }

    /**
     * 광고 클릭 리포트
     *
     * POST /api/cr
     * Body: sid(광고번호), cd(클라이언트코드), ty(위치), av(앱버전), hp(휴대폰번호), sq(푸시번호)
     *
     * @return ResponseInterface
     */
    public function cr(): ResponseInterface
    {
        $rules = [
            'sid' => 'required|integer',
            'cd'  => 'required|max_length[50]',
            'ty'  => 'required|in_list[1,2]',
            'av'  => 'required|max_length[20]',
            'hp'  => 'required|max_length[20]',
            'sq'  => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(406)
                ->setJSON(['message' => $this->validator->getErrors()]);
        }

        $data = [
            'ad_seq'      => $this->request->getPost('sid'),
            'client_code' => $this->request->getPost('cd'),
            'type'        => $this->request->getPost('ty'),
            'app_ver'     => $this->request->getPost('av'),
            'hp'          => $this->request->getPost('hp'),
            'push_seq'    => $this->request->getPost('sq'),
            'click_date'  => time(),
            'ymd'         => date('ymd'),
            'time'        => date('G'),
        ];

        $model = new AdReportModel();
        $model->insertClickReport($data);

        return $this->response->setJSON(['message' => 'success']);
    }

    /**
     * 공지사항 목록 (최근 10건)
     *
     * GET /api/nl
     *
     * @return ResponseInterface
     */
    public function nl(): ResponseInterface
    {
        $model = new NoticeModel();
        $result = $model->getList();

        return $this->response->setJSON($result);
    }

    /**
     * 공지사항 상세
     *
     * GET /api/nv/{id}
     *
     * @param int $id
     * @return ResponseInterface
     */
    public function nv(int $id): ResponseInterface
    {
        $model = new NoticeModel();
        $result = $model->getView($id);

        if (! $result) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'not found']);
        }

        return $this->response->setJSON($result);
    }
}
