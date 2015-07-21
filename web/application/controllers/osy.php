<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Osy extends CI_Controller {

	public function index()
	{
        if($_POST)
        {
            echo 'rid:'.$this->input->post('rid');
            echo ' ';
            echo 'msg:'.$this->input->post('msg');
        }
        else
        {
            echo "return message from pushwing";
        }
	}

    public function test_push()
    {
        $ch = $this->init_curl();
        $data = array('subject' => $this->input->post('subject'), 'item_id' => $this->input->post('item_id'),'client_name' => 'test_client', 'timestamp' => now());
        $this->send_gcm($this->input->post('device_id'), $data, $ch);
        curl_close($ch);
//        $this->load->model('push_m');
//        $data = array(
//            'subject' => $this->input->post('subject'),
//            'item_id' => $this->input->post('item_id'),
//            'client_name' => 'test_client',
//            'timestamp' => now()
//        );
//        $this->push_m->send_push($data,'');
    }

    public function test_send_push()
    {
        $data = array(
            'hp' => $this->input->post('hp'),
            'client_id' => $this->input->post('client_id'),
            'subject' => $this->input->post('subject'),
            'contents' => $this->input->post('contents'),
            'url' => $this->input->post('url'),
            'ymd' => date('ymd'),
            'time' => date('H'),
            'timestamp' => now()
        );
        $this->db->insert('push_wait',$data);
        redirect('/osy/test_push_form');
    }

    public function test_push_form()
    {
        $data['push_wait'] = $this->db->get('push_wait')->result_array();
        $this->load->view('test_push_form',$data);
    }

    function init_curl() {
        $headers[] = 'Content-Type:application/json';
        $headers[] = 'Authorization:key=AIzaSyBUR_3Oain9Q8JgpEaovqF1mSq_G3cn3es';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POST, true);

        return $curl;
    }

    function send_gcm($rid, $data, $ch)
    {
        if(!empty($rid) && !empty($data)) {
            $data = json_encode(array(
                'registration_ids' => array($rid),
                'data' => array('subject' => $data['subject'], 'item_id' => $data['item_id'], 'client_name' => $data['client_name'], 'timestamp' => now())
            ));

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($ch);
            echo $result;
        }
    }

    function get_push_data()
    {
        header("Content-Encoding: utf-8");

        $data = array(
            'id' => 'id입니다',
            'hp' => 'hp입니다',
            'client_id' => 'client_id 입니다',
            'subject' => 'subject 입니다',
            'content' => 'content 입니다',
            'ymd' => 'ymd 입니다',
            'time' =>'time 입니다',
            'timestamp'=>'timestamp 입니다',
            'send_ymd' => 'send_ymd 입니다',
            'send_time' => 'send_time입니다.',
            'send_timestamp' =>'send_timestamp입니다.'
        );

        $this->output
            ->set_content_type('application/json');

        echo json_encode($data);
        //print_r(json_decode(json_encode($data)));
    }

    function send_apns()
    {
        $string = "대상 스트링";
        echo mb_detect_encoding($string);

        $this->load->library('ios');
        $this->ios->to('f8eaecdbc3d871cbda52918c5b6cae25f92426e05dd8e3dfb8c611b84e03294c');
        $this->ios->message('푸쉬가 간다');
        $this->ios->send();
    }

    function detectEncoding($str, $encodingSet)
    {
        foreach ($encodingSet as $v) {
            $tmp = iconv($v, $v, $str);
            if (md5($tmp) == md5($str)) return $v;
        }
        return false;
    }

}

/* End of file main.php */
/* Location: ./application/controllers/main.php */