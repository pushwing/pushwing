<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Partners_m extends CI_Model
{
	public function __construct()
    {
        parent::__construct();
    }

    /*
     * 모든 파트너(클라이언트)를 조회한다.
     */
    public function get_all()
    {
        return $this->db->where('visible',1)->get('client')->result_array();
    }
}