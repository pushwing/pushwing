<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pushwing_test extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
        $this->load->library('pushwing');

        echo 'pushwing이 설치된 스마트폰으로 push를 전송 합니다.';
        $this->pushwing->send_push('0101234567', 'Test push', '가나다라마바사', 'http://www.cikorea.net');
	}
}

/* End of file pushwing_test.php */
/* Location: ./application/controllers/pushwing_test.php */