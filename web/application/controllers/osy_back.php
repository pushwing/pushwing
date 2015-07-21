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
        log_message('error','id : '.$this->input->post('rid'));
        log_message('error','msg : '.$this->input->post('msg'));

        $ch = $this->init_curl();
        $this->send_gcm($this->input->post('rid'), $this->input->post('msg'), $ch);
        curl_close($ch);
    }

    public function test_push_form()
    {
        $this->load->view('test_push_form');
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

    function send_gcm($rid, $msg, $ch)
    {
        if(!empty($rid) && !empty($msg)) {
            $data = json_encode(array(
                'registration_ids' => array($rid),
                'data' => array('msg' => $msg)
            ));

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($ch);
            echo $result;
        }
    }

    function get_push_data()
    {
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
    }

    function send_apns()
    {
        $deviceToken = '6cc3cb12040b3f609f7d6224858a2c02df3cb6a7dd30913bd79e02dfec8c8f8f'; // 디바이스토큰ID
        $message = 'Message received from eye'; // 전송할 메시지

        // 개발용
        //$apnsHost = 'gateway.sandbox.push.apple.com';
        //$apnsCert = 'pushwing-APNS-Dev-Cert.pem';

        // 실서비스용
        $apnsHost = 'gateway.push.apple.com';
        $apnsCert = 'apns-pro.pem';

        $apnsPort = 2195;

        $payload = array('aps' => array('alert' => $message, 'badge' => 0, 'sound' => 'default'));
        $payload = json_encode($payload);

        $streamContext = stream_context_create();
        stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

        $apns = stream_socket_client('ssl://'.$apnsHost.':'.$apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);

        if($apns)
        {
            print_r($apns);
            $apnsMessage = chr(0).chr(0).chr(32).pack('H*', str_replace(' ', '', $deviceToken)).chr(0).chr(strlen($payload)).$payload;
            fwrite($apns, $apnsMessage);
            fclose($apns);
        }
    }
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */