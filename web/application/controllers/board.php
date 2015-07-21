<?php

class Board extends CI_Controller {

	function __construct()
	{
		parent::__construct();

        $this->lang->load('board', 'korean');

		$this->load->helper(array('alert', 'common'));
		$this->load->model('board_m');
        
        $this->load->library('tank_auth');
		$this->lang->load('tank_auth');
        
        is_admin_login(3);
        
		$perm = explode("|", '0|0|3|3');
		$this->list_perm = $perm[0];
		$this->view_perm = $perm[1];
		$this->comment_perm = $perm[2];
		$this->write_perm = $perm[3];

        if($this->uri->segment(2) == 'notice')
        {
            $title = '공지사항';
            $num = 2;
        }
        define('MENU_BOARD_NAME', $title);
        define('MENU_ID', $num);
        define('MENU_CATEGORY_WORD', '');

		$this->output->enable_profiler(false);

		$this->seg_exp = segment_explode($this->uri->uri_string());
		//var_dump($this->seg_exp);
		$this->load->helper('ckeditor');

		//Ckeditor's configuration
		$this->data['ckeditor'] = array(

			//ID of the textarea that will be replaced
			'id' 	=> 	'contents', 	// Must match the textarea's id
			'path'	=>	'include/ckeditor',	// Path to the ckeditor folder relative to index.php

			//Optionnal values
			'config' => array(
				'toolbar' 	=> 	"Full", 	//Using the Full toolbar
				'width' 	=> 	"550px",	//Setting a custom width
				'height' 	=> 	'100px',	//Setting a custom height

			),

			//Replacing styles from the "Styles tool"
			'styles' => array(

				//Creating a new style named "style 1"
				'style 1' => array (
					'name' 		=> 	'Blue Title',
					'element' 	=> 	'h2',
					'styles' => array(
						'color' 		=> 	'Blue',
						'font-weight' 		=> 	'bold'
					)
				),

				//Creating a new style named "style 2"
				'style 2' => array (
					'name' 		=> 	'Red Title',
					'element' 	=> 	'h2',
					'styles' => array(
						'color' 		=> 	'Red',
						'font-weight' 		=> 	'bold',
						'text-decoration'	=> 	'underline'
					)
				)
			)
		);
	}

    /**
     * 사이트 헤더, 푸터를 자동으로 추가해준다.
     *
     */
    public function _remap($method)
    {
        //헤더 include
        $this->load->view('top_v');

        if( method_exists($this, $method) )
        {
            $this->{"{$method}"}();
        }

        //푸터 include
        $this->load->view('bottom_v');
    }

	function index()
    {
		switch($this->uri->segment(3)) {
		case 'lists':
			$this->lists();
		break;
		case 'view':
			$this->view();
		break;
		case 'write':
			$this->write();
		break;
		case 'delete':
			$this->delete();
		break;
		case 'edit':
			$this->edit();
		break;
		case 'reply_edit':
			$this->reply_edit();
		break;
		case 'download':
			$this->download();
		break;
		case 'sns':
			$this->sns();
		break;
		case 'sns_comment':
			$this->sns_comment();
		break;
		default:
			$this->lists();
		break;
		}
	}

	function lists() //$plugin, $function, $skin
	{
		//$this->output->enable_profiler(false);
		if(in_array("q", $this->seg_exp)) 
		{
			$arr_key = array_keys($this->seg_exp, "q");
			$arr_val = $arr_key[0] + 1;

            if(@$this->seg_exp[$arr_val])
			{
			    $search_word = $this->seg_exp[$arr_val];
            } 
			else 
			{
                $search_word = '검색어없음/';
            }
            
			$arr_key1 = array_keys($this->seg_exp, "sfl");
            
			if(@$arr_key1[0])
			{
                $arr_val1 = $arr_key1[0] + 1;
            } 
			else 
			{
                $arr_val1 = 10;
            }
            
            if(@$this->seg_exp[$arr_val1])
			{
			    $sfl = $this->seg_exp[$arr_val1];
            } 
			else 
			{
                $sfl = 'subject';
            }
            
			$post = array('method'=>$sfl, 's_word'=> urldecode($search_word));
		} 
		else 
		{
    		$post = '';
    	}

		if(($this->session->userdata('auth_code') == 'ADMIN' ) or ($this->session->userdata('auth_code') >= 0) or ($this->list_perm == 1) )
		{

			if(in_array("page", $this->seg_exp))
			{
				$arr_key = array_keys($this->seg_exp, "page");
				$arr_val = $arr_key[0] + 1;
                if(@$this->seg_exp[$arr_val])
				{
				    $data['page_account']=$page = $this->seg_exp[$arr_val];
                } 
				else
				{
                    $data['page_account']=$page = 1;
                }

			} 
			else
			{
				$data['page_account']=$page = 1;
			}

			$data['division'] = $post['division'] = urldecode(url_explode($this->seg_exp, 'division'));

			$data['list_total'] = $total = $this->board_m->load_list_total($post, MENU_ID);

            $data['page_entry'] = $post['page_entry'] = url_explode($this->seg_exp, 'page_entry');

            if ($data['page_entry'])
            {
                $rp = $data['page_entry']; 
            }
            else
            {
                $rp = 20; 
            }

			$limit = 9; 
			
			if(!is_numeric($page)) 
			{
				$page = 1;
			}

			$start = (($page-1) * $rp);

			//print_r($this->seg_exp);
			$this->url_seg = $this->seg_exp;
			$arr_s = array_search('page', $this->url_seg);

			if($arr_s)
			{
				array_splice($this->url_seg, $arr_s, 2);
			}
			
			$urls = implode('/', $this->url_seg);


			$data['pagination_links'] = pagination($urls."/page", paging($page,$rp,$total,$limit));

			$data['list'] = $this->board_m->load_list($start, $rp, $post, MENU_ID);

			$this->load->view('board/default/lists_v', $data);
		}
		else
		{
			if(!$this->session->userdata('userid'))
			{
				$rpath = str_replace("index.php/", "", $this->input->server('PHP_SELF'));
				$data['rpath_encode'] = strtr(base64_encode(addslashes(gzcompress(serialize($rpath), 9))), '+/=', '-_.');
				$this->load->view('login_view_v',$data);
			}
			else
			{
				$data['perm'] = "권한이 없습니다..";
				$this->load->view('perm_view_v',$data);
			}
		}

	}

	function edit()
	{
		$write_id = $this->board_m->id_check($this->uri->segment(4));

		if(($this->tank_auth->get_user_id() == $write_id['user_id']) )
		{
			$data['views'] = $views = $this->board_m->board_view($this->uri->segment(4), 'edit');
			$data['tags'] = $this->board_m->board_tag($this->uri->segment(4), MENU_BOARD_NAME_EN);

	        $this->load->library('form_validation');
	        $this->form_validation->set_rules('subject', '제목', 'required');
			$this->form_validation->set_rules('contents', '내용', 'required');


			if ($this->form_validation->run() == FALSE || @$file_error)
			{

				$this->load->view('board/default/edit_v', $data);

			}
			else
			{
				$this->board_m->update_board($this->uri->segment(4), $_POST);
				
?>

				<script>
					//FrameDialog 
					//$(document).ready(function() { jQuery.FrameDialog.closeDialog(); });
					alert('수정되었습니다.')
					document.location= '/<?php echo $this->uri->segment(1)?>/view/<?php echo $this->uri->segment(3)?>/<?php echo $this->uri->segment(4)?>/page/<?php echo $this->uri->segment(6)?>';
					//parent.document.location.reload();
				</script>
<?php

			}
		}
		else
		{
			if(!$this->session->userdata('userid'))
			{
				$rpath = str_replace("index.php/", "", $this->input->server('PHP_SELF'));
				//$rpath_encode = base64_encode($rpath);
				$rpath_encode = strtr(base64_encode(addslashes(gzcompress(serialize($rpath), 9))), '+/=', '-_.');
?>
				<table width="95%" height="95%">
				<tr>
					<td align="center">로그인하여야 합니다.br><br><a href='/auth/login/<?php echo $rpath_encode?>'>로그인</a></td>
				</tr>
				</table>
<?php
			}
			else
			{ 
                $data['perm'] = "권한이 없습니다..";
                $this->load->view('perm_view_v',$data);
			}
		}

	}

	function reply_edit()
	{
		$write_id = $this->board_m->comment_id_check($this->uri->segment(3));

		if(($this->tank_auth->get_user_id() == $write_id['user_no']) )
        {
			$data['views'] = $views = $this->board_m->comment_one($this->uri->segment(3));

			$this->load->library('form_validation');
	        $this->form_validation->set_rules('contents', '내용', 'required');


			if ($this->form_validation->run() == FALSE || @$file_error)
			{
				$this->load->view('board/default/reply_edit_v', $data);
			}
			else
			{
				$this->board_m->update_comment($this->uri->segment(3), $_POST);
            ?>
				<script type="text/javascript"  src="<?php echo JS_DIR?>/jquery-1.3.2.min.js"></script>
				<script type="text/javascript"  src="<?php echo JS_DIR?>/jquery.framedialog.js"></script>
				<script>
					//FrameDialog ?リ린
					$(document).ready(function() { jQuery.FrameDialog.closeDialog(); });
					alert('수정되었습니다.')
					parent.document.location.reload();
				</script>
				<?php
				$this->db->cache_delete('default', 'index');
            }
		}
		else
		{
			if(!$this->tank_auth->get_user_id())
            {
				$rpath = str_replace("index.php/", "", $this->input->server('PHP_SELF'));
				$rpath_encode = strtr(base64_encode(addslashes(gzcompress(serialize($rpath), 9))), '+/=', '-_.');?>
				<table width="95%" height="95%">
				<tr>
					<td align="center">로그인필요<br><br><a href='/auth/login/<?php echo $rpath_encode?>'>로그인</a></td>
				</tr>
				</table>
			<?php
			}
			else
			{
			    $data['perm'] = "권한이 없습니다..";
                $this->load->view('perm_view_v',$data); 
			}
		}

	}

	function write()
	{
		$this->load->helper('ckeditor');

		if(in_array("page", $this->seg_exp)) 
		{
			$arr_key = array_keys($this->seg_exp, "page");
			$arr_val = $arr_key[0] + 1;
			$page = $this->seg_exp[$arr_val];
		} 
		else 
		{
			$page = 1;
		}

		//if(($this->tank_auth->get_username() == 'blumine' ) or ($this->session->userdata('auth_code') >= $this->write_perm) or ($this->write_perm == 1))
		if(($this->session->userdata('auth_code') >= 9) or ($this->write_perm == 1))
		{
			if(!$this->tank_auth->is_logged_in())
			{
				$rpath = str_replace("index.php/", "", $this->input->server('PHP_SELF'));
				$rpath_encode = strtr(base64_encode(addslashes(gzcompress(serialize($rpath), 9))), '+/=', '-_.');?>
				<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
				<table width="95%" height="95%">
				<tr>
					<td align="center">로그인 필요<br><br><a href='/auth/login/<?php echo $rpath_encode?>'>로그인</a></td>
				</tr>
				</table>
			<?php
			}
			$this->load->library('form_validation');
			$this->form_validation->set_rules('subject', '제목', 'required');
			$this->form_validation->set_rules('contents', '내용', 'required');

			if ($this->form_validation->run() == FALSE || @$file_error)
			{
				$data['ckeditor_value'] = '';
				if(@set_value('contents')) $data['ckeditor_value'] = htmlspecialchars_decode(set_value('contents'));

				$this->load->view('board/default/write_com_v', $data);

			}
			else
			{
				$user_no = $this->tank_auth->get_user_id();
				$user_name = $this->tank_auth->get_username();
				$last_no = $this->board_m->insert_board($this->input->post(NULL, true), $this->uri->segment(4), $user_no, $user_name);


				alert('글이 등록되었습니다.', '/admin/'.$this->uri->segment(2).'/list/0/page/'.$page.'');

			}


		}
		else
		{
			if(!$this->tank_auth->is_logged_in())
			{
				$rpath = str_replace("index.php/", "", $this->input->server('PHP_SELF'));
				$rpath_encode = strtr(base64_encode(addslashes(gzcompress(serialize($rpath), 9))), '+/=', '-_.');?>
				<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
				<table width="95%" height="95%">
				<tr>
					<td align="center">로그인 필요<br><br><a href='/auth/login/<?php echo $rpath_encode?>'>로그인</a></td>
				</tr>
				</table>
			<?php
			}
			else
			{ 
                $data['perm'] = "권한이 없습니다..";
                $this->load->view('perm_view_v',$data);
			}
		}


	}

	function view()
	{
		if(($this->session->userdata('auth_code') == 'ADMIN' ) or ($this->session->userdata('auth_code') >= $this->view_perm) or ($this->view_perm == 1) )
		{
			if(in_array("page", $this->seg_exp))
			{
				$arr_key = array_keys($this->seg_exp, "page");
				$arr_val = $arr_key[0] + 1;
				$data['page_account']=$page = $this->seg_exp[$arr_val];
			}
			else
			{
				$data['page_account']=$page = 1;
			}
			
            $board_id = $this->uri->segment(5);
            
			$data['views'] = $this->board_m->board_view($board_id, 'view');
			if(!$data['views'] or $data['views'] == ''){
				alert('삭제되거나 없는 글입니다.');
				exit;
			}
			$data['tags'] = $this->board_m->board_tag($board_id, MENU_ID);
			//var_dump($data['tags']);
			$data['comment_perm'] = $this->comment_perm;
			$data['comments'] = $this->board_m->comment_view($board_id);

			$data['files'] = $this->board_m->file_list($board_id);
			$data['files_cnt'] = count($data['files']);

			$rp = 20;

			if(!is_numeric($page))
			{
				$page = 1;
			}

			$start = (($page-1) * $rp);

			if (in_array('q', $this->seg_exp) and in_array('sfl', $this->seg_exp))
			{
				$arr_key = array_keys($this->seg_exp, "q");
				$arr_val = $arr_key[0] + 1;
				$search_word = $this->seg_exp[$arr_val];
				$arr_key1 = array_keys($this->seg_exp, "sfl");
				$arr_val1 = $arr_key1[0] + 1;
				$sfl = $this->seg_exp[$arr_val1];

				$post = array('method'=>$sfl, 's_word'=>$search_word);
			}
			else
			{
				$post ='';
			}

	        $this->load->library('form_validation');
	        $this->form_validation->set_rules('subject', '제목', 'required');
			$this->form_validation->set_rules('wcontent', '내용', 'required');

			$this->load->view('board/default/view_v', $data);
		}
		else
		{
			if(!$this->session->userdata('userid'))
			{
				$rpath = str_replace("index.php/", "", $this->input->server('PHP_SELF'));
				$rpath_encode = strtr(base64_encode(addslashes(gzcompress(serialize($rpath), 9))), '+/=', '-_.');?>
				<table width="95%" height="95%">
				<tr>
					<td align="center">로그인 필요<br><br><a href='/auth/login/<?php echo $rpath_encode?>'>로그인</a></td>
				</tr>
				</table>
			<?php
			}
			else
			{
			    $data['perm'] = "권한이 없습니다..";
                $this->load->view('perm_view_v',$data); 
			}
		}
	}

	function download() {

			$this->load->helper('alert');

			$this->db->select('original_name, file_name');
			$qry = $this->db->get_where('files', array(
				'module_name' => MENU_BOARD_NAME_EN,
				'module_no'=> $this->uri->segment(3),
				'no' => $this->uri->segment(4)
			));
			$file = $qry->row_array();

			if (!isset($file['file_name']))
				alert("파일이 없습니다.");

			$this->load->helper('download');
			$data = file_get_contents($this->input->server('DOCUMENT_ROOT')."/data/files/".$file['file_name']);
			
			if (!force_download(urlencode($file['original_name']), $data))
				alert('파일이 없습니다.');
	}

	function delete()
	{
		if(!$this->tank_auth->is_logged_in())
		{
			$rpath = str_replace("index.php/", "", $this->input->server('PHP_SELF'));
			$rpath_encode = strtr(base64_encode(addslashes(gzcompress(serialize($rpath), 9))), '+/=', '-_.');

			?>
			<table width="95%" height="95%">
			<tr>
				<td align="center">로그인 필요<br><br><a href='/auth/login/<?php echo $rpath_encode?>'>로그인</a></td>
			</tr>
			</table>
		<?php

		}
		else
		{
			$delete_id = $this->board_m->id_check($this->uri->segment(5));
             
			if($this->tank_auth->get_user_id() == $delete_id['user_id'])
			{
				$this->board_m->delete_post($this->uri->segment(5));
				?>
				<script>
					alert('삭제되었습니다.')
					location= '/<?php echo $this->seg_exp[0]?>/<?php echo $this->uri->segment(2);?>/lists/<?php echo $this->uri->segment(4);?>';
				</script>
				<?php
				$this->db->cache_delete();

			}
			else
			{
			?>
				<table width="95%" height="95%">
				<tr>
					<td align="center">본인 글만 삭제가능.
					<br>
					<a href="/">홈으로 이동</a>
					</td>
				</tr>
				</table>
			<?php

			}
		}

	}


	function sns_comment()
	{
		$this->load->library('sns');
		$this->load->library('tinyurl');

		$this->load->model('shorturl_m', 'short');

		$seg = $this->seg_exp;

		$id = $seg[0];
		$no = $seg[3];
		$snsType = $seg[2];

		$short_url = $this->tinyurl->getShortUrl($this->config->item('base_url').'/'.$id.'/view/'.$no);

		$data = $this->board_m->comment_one($no);

		$contents = $this->_stripStringLines($data[0]['contents']);
		
		if (empty($contents)) $contents = 'no contents';

		switch (strtolower($snsType)) 
		{
			case 'twitter': 
				$twitter = $this->sns->twitter();
				$twitter->setCallback(site_url(implode('/', $seg)));

				if (!$twitter->isLoggedIn()) 
				{
					$twitter->login();
				} 
				else 
				{
					$current = $_SERVER['HTTP_REFERER'];
					$key = $this->tinyurl->getKey($current);
					$tiny = $this->tinyurl->getShortUrl($current);

					$this->short->add($key, $current);
					$contents = $twitter->cutString($contents, 139 - strlen($tiny), ' ') . $tiny;;

					$twitter->tweet($contents);
				}
				break;

			case 'facebook': 
				$facebook = $this->sns->facebook();
				$facebook->setCallback(site_url(implode('/', $seg)));

				if (!$facebook->isLoggedIn()) 
				{
					$facebook->login();
				}
				else 
				{
					$current = $_SERVER['HTTP_REFERER'];
					$key = $this->tinyurl->getKey($current);
					$tiny = $this->tinyurl->getShortUrl($current);

					$this->short->add($key, $current);
					$contents .= ' ' . $tiny;

					$facebook->post($contents);
				}
				break;
		}

		header('Content-type:text/html; charset=utf-8');
		echo '<script>alert("등록되었습니다");self.close();</script>';
	}


	function sns()
	{
		$this->load->library('sns');
		$this->load->library('tinyurl');

		$this->load->model('shorturl_m', 'short');

		$seg = $this->seg_exp;

		$id = $seg[0];
		$no = $seg[3];
		$snsType = $seg[2];

		$short_url = $this->tinyurl->getShortUrl($this->config->item('base_url').'/'.$id.'/view/'.$no);

		$imgNo = (isset($seg[4])) ? $seg[4] : null;

		switch (strtolower($snsType)) 
		{
			case 'twitter': 
				$twitter = $this->sns->twitter();
				$twitter->setCallback(site_url(implode('/', $seg)));

				if (!$twitter->isLoggedIn()) 
				{
					$twitter->login();
				} 
				else 
				{
					$data = $this->board_m->board_view($no, 'view');

					$contents = $this->_stripStringLines($data['contents']);
					if (empty($contents)) $contents = 'no contents';
					
					$current = $this->config->item('base_url').'/'.$id.'/view/'.$no;

					$key = $this->tinyurl->getKey($current);
					$tiny = $this->tinyurl->getShortUrl($current);

					$this->short->add($key, $current);
					$contents = $twitter->cutString($contents, 139 - strlen($tiny), ' ') . $tiny;;

					$twitter->tweet($contents);
				}
				break;

			case 'facebook': 
				$facebook = $this->sns->facebook();
				$facebook->setCallback(site_url(implode('/', $seg)));

				if (!$facebook->isLoggedIn()) 
				{
					$facebook->login();
				} 
				else 
				{
					$data = $this->board_m->board_view($no, 'view');

					$contents = $this->_stripStringLines($data['contents']);
					if (empty($contents)) $contents = 'no contents';

					$current = $this->config->item('base_url').'/'.$id.'/view/'.$no;

					$key = $this->tinyurl->getKey($current);
					$tiny = $this->tinyurl->getShortUrl($current);

					$this->short->add($key, $current);
					$contents .= ' ' . $tiny;

					if (!is_null($imgNo)) 
					{
						$images = $this->board_m->attached_images($no);
						$file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $images[$imgNo];

						$albums = $facebook->getAlbums();
						$albumId = null;

						for ($i = 0; $i < sizeof($albums['data']); $i++) 
						{
							if ($albums['data'][$i]['name'] == 'coms.com') 
							{
								$albumId = $albums['data'][$i]['id'];
								break;
							}
						}

						if (is_null($albumId)) $albumId = $facebook->createAlbum('coms.com', 'coms.com앨범', 'coms.com앨범생성');

						$facebook->uploadPhoto($contents, $file, $albumId);
					} 
					else 
					{
						$facebook->post($contents);
					}
				}
				break;
		}

		header('Content-type:text/html; charset=utf-8');
		echo '<script>alert("등록되었습니다.");self.close();</script>';
	}


	function _stripStringLines($str)
	{
		return trim(strip_tags(preg_replace('/(\t|\n|\r)/is', '', html_entity_decode($str, ENT_QUOTES, 'utf-8'))));
	}
}

/* End of file Board.php */
/* Location: ./system/application/controllers/board.php */