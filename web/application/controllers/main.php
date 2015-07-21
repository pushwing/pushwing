<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		//$this->load->view('welcome_message');
		$this->contact();
	}

	function contact()
	{
		$this->load->helper(array('form', 'url', 'alert'));

		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', '이름', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if($this->input->post('mode') == 'partner')
            $this->form_validation->set_rules('web_url', '웹사이트 주소', 'required');
        else
            $this->form_validation->set_rules('content', '내용', 'required');

		if ($this->form_validation->run() == FALSE)
		{
            if($this->input->post())
                alert('필수 정보가 누락되었거나 이메일이 형식에 맞지 않습니다. 다시 시도해주세요');
            else
    			$this->load->view('contact_v');
		}
		else
		{
			$post = $this->input->post(NULL, true);
			$post['reg_date'] = date("Y-m-d H:i:s");

			//db입력
            unset($post['mode']);
			$this->db->insert('contact', $post);

			$this->load->library('email');

			$config['mailtype'] = 'html';

			$this->email->initialize($config);

			$this->email->from($post['email'], $post['name']);
			$this->email->reply_to($post['email'], $post['name']);

			$this->email->to('master@pushwing.com');

            if($this->input->post('mode') == 'qna')
            {
                $this->email->subject('푸시윙 문의: '.$post['name'].'님');
                $this->email->message('이름 : '.$post['name'].'<br><br> 카테고리 : '.$post['category'].'<br><br> 문의내용 : '.$post['content'].'<br><br> Email : '.$post['email'].'  <br><br> 접수되었습니다.');
            }
            else
            {
                $this->email->subject('푸시윙 파트너 요청: '.$post['name'].'님');
                $this->email->message('이름 : '.$post['name'].'<br><br> 웹사이트 주소 : '.$post['web_url'].'<br><br> Email : '.$post['email'].'  <br><br> 접수되었습니다.');
            }

			$this->email->send();

			alert('접수되었습니다. \n\r빠른 시일내에 연락드리겠습니다. \n\r감사합니다.', 'http://'.$_SERVER['HTTP_HOST']);
			//$this->load->view('end_v');
		}
	}

    function docs()
    {
        $this->load->library('geshilib'); //syntax highlighter 라이브러리
        $this->load->view('docs_v');
    }

    function partners()
    {
        $this->load->model('partners_m');
        $data['partners'] = $this->partners_m->get_all();
        $this->load->view('partners_v',$data);
    }

    function communication()
    {
        $this->load->view('communication_v');
    }

    function download()
    {
        $this->load->view('download_v');
    }

    //블로그 글 목록 조회
    function blog_list()
    {
        $page = 0;
        $table_id = 2;
        $rp = 10;
        $post = array('method' => '', 's_word' => '');

        $this->load->model('board_m');
        $data['blog_list'] = $this->board_m->load_list($page, $rp, $post, $table_id);
        $this->load->view('blog_list_v',$data);

    }

    //블로그 개별 글 조회
    function blog($id)
    {
        $this->load->model('board_m');
        $data['blog'] = $this->board_m->board_view($id,'view');
        $this->load->view('blog_v',$data);
    }
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */