<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Pushwing push library
 * 스마트폰으로 push를 날려주는 라이브러리
 *
 * @package pushwing
 * @author 한대승 <hoksi2k@hanmail.net>
 * @site http://www.pushwing.com
 */
class Pushwing {
    private $ci;
    private $db;
    private $client_id;
    
    public function __construct($config)
    {
        $dbcfg = array(
            'hostname' => isset($config['pushwing_server']) ? $config['pushwing_server'] : NULL,
            'username' => isset($config['pushwing_id']) ? $config['pushwing_id'] : NULL,
            'password' => isset($config['pushwing_password']) ? $config['pushwing_password'] : NULL,
            'database' => 'pushwing',
            'dbdriver' => 'mysqli',
            'pconnect' => FALSE,
            'db_debug' => FALSE,
            'cache_on' => FALSE,
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
        );
        
        $this->ci = &get_instance();
        $this->client_id = isset($config['client_id']) ? $config['client_id'] : NULL;
        $this->init_db($dbcfg);
    }
    
    public function init_db($config)
    {
        if(!empty($config) && $config['hostname'] && $config['username'] && $config['password']) {
            $this->db = $this->ci->load->database($config, TRUE);
        } else {
            $this->db = NULL;
        }
    }
    
    public function send_push($hp, $subject, $contents, $url)
    {
        $ret = FALSE;
        
        if(isset($this->db->conn_id) && $this->db->conn_id) {
            $this->db->set('hp', $hp);
            $this->db->set('client_id', $this->client_id);
            $this->db->set('subject', $this->cut_str($subject, 20));
            $this->db->set('contents', $this->cut_str($contents, 200));
            $this->db->set('url', $url);
            $this->db->set('ymd','CURDATE() + 0', FALSE);
            $this->db->set('time','CURTIME() + 0', FALSE);
            $this->db->set('timestamp', 'UNIX_TIMESTAMP()', FALSE);
            $ret = $this->db->insert('push_wait');
        }
        
        return $ret;
    }
    
    public function cut_str($string, $cut_size = 0, $tail = '...')
    {
        return $cut_size > 1 && $string ? mb_strimwidth($string, 0, $cut_size + 4, $tail, 'utf-8') : $string;
    }
}