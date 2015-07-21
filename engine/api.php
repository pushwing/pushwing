<?php
//error_reporting(0);
error_reporting(E_ALL);
/**
 * 꽃살전문 (controller)
 * @author 변종원 <codeigniterk@gmail.com>
 *
 *	usage :
 *	    $object->response(output_data, status_code);
 *		$object->_request	- to get santinized input
 *
 *		output_data : JSON (I am using)
 *		status_code : Send status message for headers
 */

//RESTful class
require_once("./lib/Rest.inc.php");

//Ad Engine class (model)
require_once("./eg.php");

include("./lib/openssl_aes256_imcore_net.php");

class API extends REST {

	public $data = "";

	private $db = NULL;

	public function __construct()
	{
		parent::__construct();

		//엔진
		$this->eg = new Engine();
	}

	/*
	 * Public method for access api.
	 * This method dynmically call the method based on the query string
	 *
	 */
	public function processApi()
	{
		$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
		if((int)method_exists($this,$func) > 0)
			$this->$func();
		else
			$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
	}

    /*
     *  Encode array into JSON
    */
    private function json($data)
    {
        if(is_array($data))
        {
            return json_encode($data);
        }
    }

    /**
     * 변수 유무 체크 처리
     *
     * @param string $vars 변수명
     * @param string $message 에러메시지
     * @param string $code 반환코드
     * @param string $type Y일경우 변수값이 없으면 에러, N일 경우 필수가 아님
	 * @return string $clean_input 보안처리된 변수값 반환
     */
    private function param_proc($vars, $message='', $code, $type='Y')
    {
        if ($type == 'Y')
        {
            if (@$this->_request[$vars] == '')
            {
                $this->response($message, $code);
                exit;
            }
            else
            {
                //변수에 대한 보안처리
                if(get_magic_quotes_gpc())
                {
                    $data = trim(stripslashes($this->_request[$vars]));
                }
                else
                {
                	$data = $this->_request[$vars];
                }

                $data = strip_tags($data);
                $clean_input = trim($data);
                return $clean_input;
            }
        }
        else if($type == 'N')
        {
            //변수에 대한 보안처리
            if(get_magic_quotes_gpc())
            {
                $data = trim(stripslashes(@$this->_request[$vars]));
            }
            else
            {
                $data = @$this->_request[$vars];
            }

            $data = strip_tags($data);
            $clean_input = trim($data);
            return $clean_input;
        }
    }

/*********************************************************************************************************************
 *
 *
 * 위는 수정하면 안됨. 아래 부터 사용자 영역
 *
 *
 ********************************************************************************************************************/

	/**
     * 광고 on, off 플래그, 번호에 대응하는 키가 있는지 체크
     *
     * @author 변종원 <codeigniterk@gmail.com>
     * @param string methods html, json (html일 경우 인식가능한 형태로 출력, json은 암호화하여 출력됨)
     * @return json 1 on, 0 off
     */
    private function go()
    {
        $methods = $this->param_proc('methods', '', '406');

		//140223 device_id 체크 중복이라 제거
		//$hp = $this->param_proc('hp', '', '406');

		//플래그값 가져오기
		$result = $this->eg->get_flag();

        if($result)
        {
            $this->response($this->json($result[0]), 200);
        }
        else
        {
            //데이터가 없는 경우나 에러
            $this->response('', 418);
        }
    }

	/**
     * push id 전송
     *
     * @author 변종원 <codeigniterk@gmail.com>
     * @param string methods html, json (html일 경우 인식가능한 형태로 출력, json은 암호화하여 출력됨)
	 * @param string hp 휴대폰번호
	 * @param string cd device_id
	 * @param string os os종류 1 ios, 2 android
     * @return NONE 없음
     */
    private function sd()
    {
        $methods = $this->param_proc('methods', '', '406');
		$hp = $this->param_proc('hp', '', '406');
		$cd = $this->param_proc('cd', '', '406');
		$os = $this->param_proc('os', '', '406');

		$view_arr = array(
            'hp' => $hp,
            'device_id' => $cd,
            'type' => $os
        );

        $returns = $this->eg->insert_act($view_arr, 'push_db');
        $return = array('message'=>$returns);
		$this->response($this->json($return), 200);
    }


    /**
     * push_end id에 해당하는 내용 전송
     *
     * @author 변종원 <codeigniterk@gmail.com>
     * @param string methods html, json (html일 경우 인식가능한 형태로 출력, json은 암호화하여 출력됨)
     * @param string id push_end id
     * @return array
     */
    private function fd()
    {
        $methods = $this->param_proc('methods', '', '406');
        $id = $this->param_proc('id', '', '406');

        $returns = $this->eg->get_push_end($id);
        $this->response($this->json($returns), 200);
    }


    /**
     * View Report, 앱에서 노출된 광고에 대한 리포트 전송
     *
     * @author BJ <codeigniterk@gmail.com>
     * @param string methods html, json (html일 경우 인식가능한 형태로 출력, json은 암호화하여 출력됨)
     * @param string cd 발송한 커뮤니터코드
     * @param string ty 광고위치. 초기화면 1, 푸시내용 2
     * @param string av 앱버전
	 * @param string hp 휴대폰번호
     * @param string sq 푸시번호
     * @param string sid 광고번호
     *
     * @return NONE 없음
     */
    private function vr()
    {
        $ymd = date("ymd");
        $time = date("G");

        $methods = $this->param_proc('methods', '', '406');

        $sid = $this->param_proc('sid', '', '406');
        $cd = $this->param_proc('cd', '', '406');
        $ty = $this->param_proc('ty', '', '406');
        $av = $this->param_proc('av', '', '406');
		$hp = $this->param_proc('hp', '', '406');
        $sq = $this->param_proc('sq', '', '406');

        $view_arr = array(
            'ad_seq' => $sid,
            'client_code' => $cd,
            'type' => $ty,
            'app_ver' => $av,
            'hp' => $hp,
            'push_seq' => $sq,
            'view_date' => time(),
            'ymd' => $ymd,
            'time' => $time
        );
        $returns = $this->eg->insert_act($view_arr, 'view_report');

        $this->response($this->json($returns), 200);
    }

    /**
     * Click Report, 앱에서 클릭한 광고에 대한 리포트 전송
     *
     * @author 변종원 <codeigniterk@gmail.com>
     * @param string methods html, json (html일 경우 인식가능한 형태로 출력, json은 암호화하여 출력됨)
     * @param string cd 발송한 커뮤니터코드
     * @param string ty 광고위치. 초기화면 1, 푸시내용 2
     * @param string av 앱버전
	 * @param string hp 휴대폰번호
     * @param string sq 푸시번호
     * @param string sid 광고번호
     *
     * @return NONE 없음
     */
    private function cr()
    {
        $ymd = date("ymd");
        $time = date("G");

        $methods = $this->param_proc('methods', '', '406');

        $sid = $this->param_proc('sid', '', '406');
        $cd = $this->param_proc('cd', '', '406');
        $ty = $this->param_proc('ty', '', '406');
        $av = $this->param_proc('av', '', '406');
		$hp = $this->param_proc('hp', '', '406');
        $sq = $this->param_proc('sq', '', '406');

        $view_arr = array(
            'ad_seq' => $sid,
            'client_code' => $cd,
            'type' => $ty,
            'app_ver' => $av,
            'hp' => $hp,
            'push_seq' => $sq,
            'click_date' => time(),
            'ymd' => $ymd,
            'time' => $time
        );
        $returns = $this->eg->insert_act($view_arr, 'click_report');

        $this->response($this->json($returns), 200);
    }

    /**
     * 공지사항 리스트 전송. Notice List
     *
     * @author 변종원 <codeigniterk@gmail.com>
     * @param string methods html, json (html일 경우 인식가능한 형태로 출력, json은 암호화하여 출력됨)
     * @return array
     */
    private function nl()
    {
        $methods = $this->param_proc('methods', '', '406');

        $returns = $this->eg->get_notice_list();
        $this->response($this->json($returns), 200);
    }

    /**
     * 공지사항 내용 전송. Notice View
     *
     * @author 변종원 <codeigniterk@gmail.com>
     * @param string methods html, json (html일 경우 인식가능한 형태로 출력, json은 암호화하여 출력됨)
     * @param string board id 게시물번호
     * @return array
     */
    private function nv()
    {
        $methods = $this->param_proc('methods', '', '406');
        $board_id = $this->param_proc('bid', '', '406');

        $returns = $this->eg->get_notice_view($board_id);
        $this->response($this->json($returns), 200);
    }



}


/********************아래는 수정하면 안됨. 윗부분까지 사용자 영역********************/
// Initiiate Library
$api = new API;
$api->processApi();
?>