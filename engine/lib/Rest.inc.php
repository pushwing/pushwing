<?php
	/* File : Rest.inc.php
	 * Author : Arun Kumar Sekar
	*/

	class REST {

		public $_allow = array();
		public $_content_type = "application/json";
		public $_request = array();

		private $_method = "";
		private $_code = 200;

		public function __construct()
		{
			$this->inputs();
		}

		public function get_referer()
		{
			return $_SERVER['HTTP_REFERER'];
		}

		public function response($data,$status)
		{
			$this->_code = ($status)?$status:200;
			$this->set_headers();

            if (@$this->_request['methods'] == 'html')
            {
                echo $data;
				//echo "----------"; var_dump(json_decode($data));
            }
            else
            {
				echo AES_Encode($data);
				//echo $data;
            }

            //echo '<br><br>메모리 사용량 : '.memory_get_usage().'Byte ('.(memory_get_usage() / 1000).'KB)';

			exit;
		}

		private function get_status_message()
		{
			$status = array(
						100 => 'Continue',
						101 => 'Switching Protocols',
						200 => 'OK',
						201 => 'Created',
						202 => 'Accepted',
						203 => 'Non-Authoritative Information',
						204 => 'No Content',
						205 => 'Reset Content',
						206 => 'Partial Content',
						300 => 'Multiple Choices',
						301 => 'Moved Permanently',
						302 => 'Found',
						303 => 'See Other',
						304 => 'Not Modified',
						305 => 'Use Proxy',
						306 => '(Unused)',
						307 => 'Temporary Redirect',
						400 => 'Bad Request',
						401 => 'Unauthorized',
						402 => 'Payment Required',
						403 => 'Forbidden',
						404 => 'Not Found',
						405 => 'Method Not Allowed',
						406 => 'Not Acceptable',
						407 => 'Proxy Authentication Required',
						408 => 'Request Timeout',
						409 => 'Conflict',
						410 => 'Gone',
						411 => 'Length Required',
						412 => 'Precondition Failed',
						413 => 'Request Entity Too Large',
						414 => 'Request-URI Too Long',
						415 => 'Unsupported Media Type',
						416 => 'Requested Range Not Satisfiable',
						417 => 'Expectation Failed',
						418 => 'No Data',
						500 => 'Internal Server Error',
						501 => 'Not Implemented',
						502 => 'Bad Gateway',
						503 => 'Service Unavailable',
						504 => 'Gateway Timeout',
						505 => 'HTTP Version Not Supported');
			return ($status[$this->_code])?$status[$this->_code]:$status[500];
		}

		public function get_request_method()
		{
			return $_SERVER['REQUEST_METHOD'];
		}

		private function inputs()
		{
		    $aa = '0';
			switch($this->get_request_method())
			{

				case "POST":
                    //암호화 해제
                    if(!in_array('html',$_POST))
                    {
						$aa = 1;
						$post = json_decode(AES_Decode($_POST['data']), TRUE);
						//$post = json_decode($_POST['data'], TRUE);
                    }
					else
					{
						$post = $_POST;
					}
					$this->_request = $this->cleanInputs($post, $aa);
					break;
				case "GET":
				case "DELETE":
                    //암호화 해제
                    if(!in_array('html',$_GET))
                    {
                        $aa = 1;
                    }
					$this->_request = $this->cleanInputs($_GET, $aa);
					break;
				case "PUT":
					parse_str(file_get_contents("php://input"),$this->_request);
					$this->_request = $this->cleanInputs($this->_request, $aa);
					break;
				default:
					$this->response('',406);
					break;
			}
			//var_dump($this->_request);
		}

		function AES_Encode($plain_text)
		{
			return base64_encode(openssl_encrypt($plain_text, "aes-256-cbc", CIPHER_KEY, true, str_repeat(chr(0), 16)));
		}

		function AES_Decode($base64_text)
		{
			return openssl_decrypt(base64_decode($base64_text), "aes-256-cbc", CIPHER_KEY, true, str_repeat(chr(0), 16));
		}

		private function cleanInputs($data, $aa)
		{
			//var_dump($data);
			$clean_input = array();
			if(is_array($data))
			{
				foreach($data as $k => $v)
				{
					$clean_input[$k] = $this->cleanInputs($v, $aa);
				}
			}
			else
			{
				/*
			    if($aa == 1)
			    {
                    $data = json_decode($this->decrypt_md5_base64($data));
			    }
				*/
			    if(get_magic_quotes_gpc())
				{
					$data = trim(stripslashes($data));
				}
				$data = strip_tags($data);
				$clean_input = trim($data);
			}

			//var_dump($clean_input);
			return $clean_input;
		}

		private function set_headers()
		{
			header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
			header("Content-Type:".$this->_content_type);
		}

        private function detect_code_chk($code)
        {
            $passwords = "xorqo2013qothdwkd04zero";

            $date = date("Ymd");

            $n = strlen($enc_text);
            $i = $iv_len;
            $plain_text = '';
            $iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
            while($i <$n)
            {
                $block = substr($enc_text, $i, 16);
                $plain_text .= $block ^ pack('H*', md5($iv));
                $iv = substr($block . $iv, 0, 512) ^ $password;
                $i += 16;
            }
            return preg_replace('/\x13\x00*$/', '', $plain_text);
        }
	}
?>