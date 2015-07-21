<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Client extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('admin/client_m');

		$this->load->helper('alert');
		$this->load->helper('common');

		$this->seg_exp = segment_explode($this->uri->uri_string());

		$this->output->enable_profiler(false);

		is_admin_login('9', $this->uri->segment(2));
	}

    /**
     * 사이트 헤더, 푸터를 자동으로 추가해준다.
     *
     */
    public function _remap($method)
    {
        //헤더 include
        if (!strpos($method, '_ajax'))
        {
            $this->load->view('top_v');
        }


        if( method_exists($this, $method) )
        {
            $this->{"{$method}"}();
        }

        //푸터 include
        if (!strpos($method, '_ajax'))
        {
            $this->load->view('bottom_v');
        }
    }

   	public function index()
	{
		$this->lists();
	}

	public function lists()
	{
		if(url_explode($this->seg_exp, 'page'))
        {
            $page = url_explode($this->seg_exp, 'page');
        }
        else
        {
            $page = 1;
        }

		$condition = array(
			'page'				=> $page,
			'scope'			=> $this->input->get('scope'),
			'keyword'		=> $this->input->get('keyword')
		);

		$data['lists'] = $this->client_m->get_lists($condition, $page, 20, '3');
        //vdd($data['lists']);

		$data['list_count'] = $this->client_m->get_lists_count($condition, '3');
		$data['condition'] = $condition;

		$this->_getPaginationLinks($data, $this->seg_exp, $data['list_count'], $page);

		$this->load->view('/admin/client/list_v', $data);
	}

	public function detail()
	{
		$data['id'] = $id = $this->uri->segment(5);

		$this->load->library('form_validation');
        $this->form_validation->set_rules('name', '회사명', 'required|xss_clean');
        $this->form_validation->set_rules('charge_person_name', '담당자', 'required|xss_clean');
        $this->form_validation->set_rules('charge_phone', '휴대전화', 'required|xss_clean');
        $this->form_validation->set_rules('charge_email', 'E-mail', 'required|xss_clean|valid_email');

        $this->form_validation->set_rules('user_password', '비밀번호', 'required|xss_clean|matches[user_password1]');
        $this->form_validation->set_rules('user_password1', '비밀번호 확인', 'required|xss_clean');
        $this->form_validation->set_rules('site_url', '사이트 주소', 'required|xss_clean');
        $this->form_validation->set_rules('ip_address', '서버 IP address', 'required|xss_clean');
        //$this->form_validation->set_rules('mysql_id', 'MySQL ID', 'required|xss_clean');
        //$this->form_validation->set_rules('mysql_pass', 'MySQL password', 'required|xss_clean');

        if ($this->form_validation->run() == FALSE)
        {
            $data['user_detail'] = $this->client_m->my_info($id);

            $this->load->view('/admin/client/info_v',$data);
        }
        else
        {
            $post = $this->input->post(NULL, TRUE);

            $user_data = $this->client_m->my_info($id);

            //비밀번호 비교
            if ($user_data->password == $post['user_password'])
            {
                $user = array();
            }
            else
            {
                //전송된 패스워드 암호화
                require_once('application/libraries/phpass-0.1/PasswordHash.php');
                $this->load->config('tank_auth', TRUE);

                $hasher = new PasswordHash(
                $this->config->item('phpass_hash_strength', 'tank_auth'),
                $this->config->item('phpass_hash_portable', 'tank_auth'));

                $user['password'] = $hasher->HashPassword($post['user_password']);
            }

            if ($user_data->business_file != $post['business_images_url'])
            {
                $adv['business_file'] = $post['business_images_url'];
            }

            $user['nickname'] = $adv['name'] = $post['name'];
            $user['email'] = $post['charge_email'];

            $adv['business_no'] = $post['business_no'];
            $adv['zip_code'] = $post['zip_code'];
            $adv['address1'] = $post['address1'];
            $adv['address2'] = $post['address2'];
            $adv['charge_person_name'] = $post['charge_person_name'];
            $adv['telephone'] = $post['telephone'];
            $adv['charge_phone'] = $post['charge_phone'];
            $adv['charge_email'] = $post['charge_email'];
            $adv['site_url'] = $post['site_url'];
            $adv['ip_address'] = $post['ip_address'];
            //$adv['mysql_id'] = $post['mysql_id'];
            //$adv['mysql_pass'] = $post['mysql_pass'];

            //db 업데이트후 창닫기
            $this->client_m->update_user($id, $user, $adv);

            alert('수정되었습니다.', '/admin/client/detail/0/'.$id);
        }
	}

	public function register()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', '회사명', 'required|xss_clean');
        $this->form_validation->set_rules('charge_person_name', '담당자', 'required|xss_clean');
        $this->form_validation->set_rules('charge_phone', '휴대전화', 'xss_clean');
        $this->form_validation->set_rules('charge_email', 'E-mail', 'required|xss_clean|valid_email');
        $this->form_validation->set_rules('username', '클라이언트ID', 'required|xss_clean|alpha_dash|is_unique[users
        .username]');
        $this->form_validation->set_rules('user_password', '비밀번호', 'xss_clean|matches[user_password1]');
        $this->form_validation->set_rules('user_password1', '비밀번호 확인', 'xss_clean');
		$this->form_validation->set_rules('site_url', '사이트 주소', 'required|xss_clean');
        $this->form_validation->set_rules('ip_address', '서버 IP address', 'required|xss_clean');
        $this->form_validation->set_rules('mysql_id', 'MySQL ID', 'required|xss_clean');
        $this->form_validation->set_rules('mysql_pass', 'MySQL password', 'required|xss_clean');

        if ($this->form_validation->run() == FALSE)
        {
            $data['contact_id'] = $this->uri->segment(5, 0);

            if($data['contact_id'] != 0)
            {
                $data['view'] = $this->client_m->get_contact_view($data['contact_id']);

                preg_match('@^(?:http://)?([^/]+)@i', $data['view']['web_url'], $web_url);
                $data['view']['ip_address'] = gethostbyname($web_url[1]);

                $data['view']['web_url'] = prep_url($web_url[0]);

                preg_match('@^(?:http://)?([^/]+)@i', $data['view']['web_url'], $client_name_preg);
                $client_name = $client_name_preg[1];
                $client_name = str_replace('www.','',$client_name);
                $client_name = str_replace('.','',$client_name);
                $data['view']['username'] = $client_name;
                $data['view']['mysql_id'] = $client_name;

                $data['view']['mysql_pass'] = sha1(rand(10000,99999));
            }
            else
            {
                $data['view'] = array();
            }

            $this->load->view('/admin/client/register_v', $data);
        }
        else
        {
           $post = $this->input->post(NULL, TRUE);

            require_once('application/libraries/phpass-0.1/PasswordHash.php');
            $this->load->config('tank_auth', TRUE);

            $hasher = new PasswordHash(
            $this->config->item('phpass_hash_strength', 'tank_auth'),
            $this->config->item('phpass_hash_portable', 'tank_auth'));

            $user['username'] = $post['username'];
            $user['password'] = $hasher->HashPassword($post['user_password']);
            $user['email'] = $post['charge_email'];
            $user['nickname'] = $post['name'];
            $user['created'] = date('Y-m-d H:i:s');
            
            $adv['business_file'] = $post['business_images_url'];
            $adv['name'] = $post['name'];
            $adv['business_no'] = $post['business_no'];
            $adv['zip_code'] = $post['zip_code'];
            $adv['address1'] = $post['address1'];
            $adv['address2'] = $post['address2'];
            $adv['charge_person_name'] = $post['charge_person_name'];
            $adv['telephone'] = $post['telephone'];
            $adv['charge_phone'] = $post['charge_phone'];
            $adv['charge_email'] = $post['charge_email'];
			$adv['site_url'] = $post['site_url'];
            $adv['ip_address'] = $post['ip_address'];
            $adv['mysql_id'] = $post['mysql_id'];
            $adv['mysql_pass'] = $post['mysql_pass'];
            $adv['reg_date'] = date('Ymd');

            $post['client_id'] = $user_id = $this->client_m->set_user($user, $adv);
            //echo $user_id;

            if ($user_id)
            {
                //mysql 유저 생성 및 테이블 권한 주기
                $sql = "GRANT INSERT ON  pushwing.push_wait TO `".$post['mysql_id']."`@`".$post['ip_address']."`
                IDENTIFIED BY '".$post['mysql_pass']."' WITH GRANT OPTION";
                $this->db->query($sql);

                $sql1 = "FLUSH PRIVILEGES";
                $this->db->query($sql1);

                //contact_id와 연동처리
                if($post['contact_id'] != 0)
                {
                    $u_arr = array(
                        'check_date'=>date("Y-m-d H:i:s"),
                        'email_date'=>date("Y-m-d H:i:s"),
                        'check_id'=>$this->session->userdata['user_id'],
                        'client_id'=>$user_id
                    );
                    $this->db->where('id', $post['contact_id']);
                    $this->db->update('contact', $u_arr);
                }

                //email send
                $this->send_email($post);

                alert('등록되었습니다.', '/admin/client/lists/0/');

            }
            else
            {
                alert_back('등록 실패하였습니다.');
            }
        }
    }

    function test_mail()
    {
        $post = array(
            'charge_email' => 'blumine@naver.com',
            'client_id' => '47',
            'mysql_id' => 'jb',
            'mysql_pass' => 'jb!',
            'ip_address' => '127.0.0.1'
        );

        $this->send_email($post);
    }
    private function send_email($post)
    {
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $config['priority'] = '1';
        $this->email->initialize($config);

        $this->email->from('master@pushwing.com', '푸시윙');
        //$this->email->cc('leehs@pushwing.com');
        $this->email->to($post['charge_email']);

        $this->email->subject("푸시윙 파트너 등록 완료");
        $e_content =
            "감사합니다. 푸시윙 파트너 등록이 완료되었습니다.<br>
<br>
푸시윙에서 사용하실 정보는 다음과 같습니다.<br>
<br>
Client_id : ".$post['client_id']."<br>
MySQL ID : ".$post['mysql_id']."<br>
MySQL PW : ".$post['mysql_pass']."<br>
<br>
웹사이트에 적용시 푸시윙 푸시 알림 가이드 <a href='http://pushwing.com/main/docs' target='_blank'>http://pushwing.com/main/docs</a> 를 참고해주세요.<br>
<br>
구현이나 테스트 중 문제가 발생하거나 문의사항이 있으면 언제든 편하게 연락주세요.<br>
<br>
서버 아이피를 ".$post['ip_address']." 로 등록하였습니다. 서버 아이피가 틀리면 작동을 하지 않으니 혹시 실제와 다르면 메일로 회신 부탁드립니다. 특히, 클라우드 서비스를 사용하시거나, 웹호스팅을 사용하시는 사이트의 경우 nslookup 도메인 해서 나온 아이피와 실제 전송테스트 시에 나온 아이피가 다를 경우 전송시의 아이피를 이메일로 알려주세요.<br>
<br>
감사합니다.<br>
<br>
푸시윙팀";
        $this->email->message($e_content);

        $this->email->send();
    }

    public function contact()
    {
        if(url_explode($this->seg_exp, 'page'))
        {
            $page = url_explode($this->seg_exp, 'page');
        }
        else
        {
            $page = 1;
        }

        $condition = array(
            'page'				=> $page,
            'scope'			=> $this->input->get('scope'),
            'keyword'		=> $this->input->get('keyword')
        );

        $data['lists'] = $this->client_m->get_contact_lists($condition, $page, 20, '3');
        //vdd($data['lists']);

        $data['list_count'] = $this->client_m->get_contact_lists_count($condition, '3');
        $data['condition'] = $condition;

        $this->_getPaginationLinks($data, $this->seg_exp, $data['list_count'], $page);

        $this->load->view('/admin/client/contact_list_v', $data);
    }

    public function contact_view()
    {
        $data['id'] = $id = $this->uri->segment(5);

        $data['view'] = $this->client_m->get_contact_view($id);

        $this->load->view('/admin/client/contact_view_v',$data);
    }

	/**
     * 페이지네이션 추출 함수
     *
     * @access      public
     * @since       2011. 10. 18
     */
    private function _getPaginationLinks(&$data, &$segments, $total, $page = 1, $rp = 20, $limit = 9)
    {
        //페이징
        if(in_array("page", $segments)) {
            $arr_key = array_keys($segments, "page");
            $arr_val = $arr_key[0] + 1;
            if(@$segments[$arr_val])            {
                $data['page_account']=$page = $segments[$arr_val];
            } else{
                $data['page_account']=$page = 1;
            }
        } else {
            $data['page_account']=$page = 1;
        }

        $data['list_total'] = $total;

        //$rp = 20; //리스트 갯수
        //$limit = 9; //보여줄 페이지수

        if(!is_numeric($page)) {
            $page = 1;
        }

        $start = (($page-1) * $rp);

        //검색후 페이징처리위한..
        //print_r($this->seg_exp);
        $segmentsUrl = $segments;
        $arr_s = array_search('page', $segmentsUrl);
        if ( $arr_s )
        {
            array_splice($segmentsUrl, $arr_s, 2);
        }

        $querystring = '';

        foreach ($data['condition'] as $key => $val) {
            $querystring .= '&' . $key . '=' . urlencode($val);
        }


        $querystring = ltrim($querystring, '&');

        $urls = implode('/', $segmentsUrl);
        $data['querystring'] = $querystring;
        $data['pagination_links'] = pagination($urls."/page", paging($page,$rp,$total,$limit));

        //if (!empty($querystring)) $data['pagination_links'] = str_replace('/page/1', '/?' . $querystring, $data['pagination_links']);
        $data['pagination_links'] = preg_replace('/\/page\/(\d+)/', '/page/$1/?' . $querystring, $data['pagination_links']);
    }

    public function nostart()
    {
        $data['no_start_clients'] = $this->client_m->get_no_start_client();
        $data['no_push_in_week_clients'] = $this->client_m->get_no_push_in_week();
        $this->load->view('/admin/client/nostart', $data);
    }


}