<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 푸시 컨트롤러
 */
class Push extends CI_Controller {

    function __construct()
    {
        parent::__construct();
    }

	public function index()
	{

	}

    /*
     * push_wait 테이블에 있는 모든 푸시들을 전송한다.
     * CLI 크론 또는 관리자(auth_code=9)만 실행 가능.
     */
    public function send_all()
    {
        if ( ! $this->input->is_cli_request()) {
            is_admin_login('9');
        }

        $this->load->model('push_m');
        $this->push_m->send_all();
    }
}

/* End of file push.php */
/* Location: ./application/controllers/push.php */
