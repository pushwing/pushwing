<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Push_m extends CI_Model
{
	public function __construct()
    {
        parent::__construct();
    }

    /*
     * push_wait 테이블에 있는 모든 푸시들을 전송한다.
     */
    public function send_all()
    {
        $all_push = $this->get_all_waiting_pushes();

        if($all_push)
        {
            $sent_pushes = array(); // 전송된 푸시의 id값(push_wait 테이블의 id값)

            foreach($all_push as $push)
            {
                $push_end_id = $this->store_push_data($push);
                if($push_end_id)
                {
                    $ret = $this->send_push($push, $push_end_id);
                    if($ret)
                    {
                        $sent_pushes[] = $push['id'];
                    }
                }
            }

            if(!empty($sent_pushes))
            {
                //$this->empty_waiting_push($sent_pushes);
                $this->empty_waiting_push_by_id($push['id']);
            }
        }
    }

    public function get_all_waiting_pushes()
    {
		$this->output->enable_profiler(true);
        $this->db->select('distinct(pw.hp), pw.client_id, c.name client_name, pw.subject, pw.contents, pw.ymd, pw.time, pw.timestamp, pw.id, pd.type, pd.device_id, pw.url');
        $this->db->join('push_db pd', 'pw.hp = pd.hp');
        $this->db->join('client c', 'pw.client_id = c.user_id');
        $this->db->order_by('pw.id');
        return $this->db->get('push_wait pw')->result_array();
    }

    /*
     * 푸시를 전송하고 아이디값을 반환한다.
     * push_wait 에 id가 없기 때문에 hp_timestamp 를 반환
     */
    public function send_push($push, $push_end_id)
    {
        $ret = '';

        if($push['type'] == 1) //ios
        {
            if($this->send_ios($push, $push_end_id))
                $ret = array('hp' => $push['hp'], 'timestamp' => $push['timestamp']);
        }
        else                  //android
        {
            if($this->send_android($push, $push_end_id))
                $ret = array('hp' => $push['hp'], 'timestamp' => $push['timestamp']);
        }

        return $ret;
    }

    public function store_push_data($push)
    {
        $data = array(
            'hp' => $push['hp'],
            'client_id' => $push['client_id'],
            'subject' => $push['subject'],
            'contents' => $push['contents'],
            'ymd' => $push['ymd'],
            'time' => $push['time'],
            'timestamp' => $push['timestamp'],
            'send_ymd' => date('Ymd'),
            'send_time' => date('His'),
            'send_timestamp' => time(),
            'url' => $push['url']
        );
        $this->db->insert('push_end',$data);
        $ret = $this->db->insert_id();

        $this->update_last_push_time($data);

        return $ret;
    }

    public function empty_waiting_push($sent_pushes)
    {
        $this->db->where_in('id',$sent_pushes);
        $this->db->delete('push_wait');
    }

    private function empty_waiting_push_by_id($id)
    {
        $this->db->where('id <=', $id);
        $this->db->delete('push_wait');
    }

    public function send_ios($push, $push_end_id)
    {
        $this->load->library('ios');
        $this->ios->to($push['device_id']);
        $this->ios->message("[{$push['client_name']}] ".$push['subject']);
        $this->ios->item_id($push_end_id);
        $this->ios->client_name($push['client_name']);
        $this->ios->timestamp($push['timestamp']);
        $this->ios->send();
    }

    public function send_android($push, $push_end_id)
    {
        $ret = false;

        $data = array(
            'subject' => "[{$push['client_name']}] ".$push['subject'],
            'item_id' => $push_end_id,
            'client_name' => $push['client_name'],
            'timestamp' => $push['timestamp']
        );

        $ch = $this->init_curl();

        if($this->send_gcm($push['device_id'], $data, $ch))
            $ret = true;

        curl_close($ch);

        return $ret;
    }

    function init_curl() {
        $headers[] = 'Content-Type:application/json';
        $headers[] = 'Authorization:key=GCM_key'; //gcm key 입력

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

    function send_gcm($device_id, $data, $ch)
    {
        $result =false;

        if(!empty($device_id) && !empty($data))
        {
            $data = json_encode(array(
                    'registration_ids' => array($device_id),
                    'data' => array('subject' => $data['subject'], 'item_id' => $data['item_id'], 'client_name' => $data['client_name'], 'timestamp' => $data['timestamp'])
                ));

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($ch);
        }

        return $result;
    }

    /*
     * 푸시를 마지막으로 보낸 시간을 저장한다.
     */
    function update_last_push_time($data)
    {
        $update_data['last_push_time'] = $data['send_timestamp'];
        $this->db->where('user_id',$data['client_id']);
        $this->db->update('client',$update_data);
    }
}