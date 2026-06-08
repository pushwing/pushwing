<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migrate extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        // 관리자(최고 권한)만 실행 가능
        is_admin_login('9');
    }

	public function index()
	{
        $this->load->library('migration');

        if ( ! $this->migration->current())
        {
            show_error($this->migration->error_string());
        }
	}
}

/* End of file migrate.php */
/* Location: ./application/controllers/migrate.php */