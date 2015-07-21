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
     */
    public function send_all()
    {
        $this->load->model('push_m');
        $this->push_m->send_all();
		//redirect('/osy/test_push_form');
    }
}

/* End of file push.php */
/* Location: ./application/controllers/push.php */