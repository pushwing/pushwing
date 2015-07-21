<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @author Jongwon Byun <codeigniterk@gmail.com>
 * @version 1.0
 */
class Board_m extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		//$CI =& get_instance();
		$this->table='board';
        $this->comments_table = 'board_comments';
		$this->files_table="board_files";
		$this->tags_table="board_tags";
    }

	/**
     * 게시판 데이터 입력
     * @param	array	(POST내용)
	 * @param	string  (원글0, 답글은 원글번호)
	 * @return	int
     */
    function insert_board($post, $type, $user_id, $username)
	{

       	$general_setting = '';
       	$this_date = date("Y-m-d H:i:s");

		//원글, 답글 처리
		//no 유니크번호, board_pid 게시물 순차번호, reply_order 답글 정렬순서

		if($type == '0')
		{
			//원글
			$this->db->select_max('board_pid');
			$this->db->where('is_delete', 'N');
			$query = $this->db->get($this->table);
			$board_pids = $query->row();
			$board_pid = $board_pids->board_pid + 1;
            $reply_order_no = 0;
			//echo $this->db->last_query();
		}
		else if($type > '0')
		{
			//답글
			$this->db->select('board_pid, reply_order');
			$this->db->where('is_delete', 'N');
			$this->db->where('board_pid', $type);
            $this->db->order_by('reply_order', 'desc');
			$query = $this->db->get($this->table);
			$board_pids = $query->row();
			$board_pid = $board_pids->board_pid;
            $reply_order_no = $board_pids->reply_order + 1;
		}
		//print_r($board_pid);

       	$file_count = '0';

		$data = array(
					'table_id' => MENU_ID,
					'board_pid' => $board_pid,
					//'division' => $this->security->xss_clean($post['category_word']), //말머리
					'user_id' => $user_id,
					'user_name' => $username,
					'reg_date' => $this_date,
					'modify_date' => $this_date,
					//'is_officail' => '0',
					'is_secret' => '',
					'subject' => $this->security->xss_clean($post['subject']),
					'general_setting' => $general_setting,
					'contents' => $post['contents'],
					'files_count' => $file_count,
					'download_count' => '0',
					'scrap_count' => '0',
					'hit' => '0',
					'comment_count' => '0',
					'reply_count' => '0',
					'voted_count' => '0',
					'blamed_count' => '0',
					'ip' => $this->input->ip_address(),
					'password' => @$post['password'],
					'reply_order' => $reply_order_no
				);
		//print_r($data);

		$this->db->insert($this->table, $data);
		$last_id = big_last_id();


        //답글일 경우 답글 수 + 처리
        if($type > '0')
		{
            //$this->db->set('comment_count', 'comment_count+1', FALSE);
			$this->db->set('reply_count', 'reply_count+1', FALSE);
      		$this->db->where(array('board_pid' => $type, 'reply_order' => '0', 'is_delete' => 'N'));
    		$this->db->update($this->table);
		}


		//태그처리
		//echo $last_id;
       	if ($post['tags'])
		{
       		$tag_arr = explode(",", $this->security->xss_clean($post['tags']));
       		$cnt = count($tag_arr);
       		for ($i=0; $i < $cnt; $i++)
			{
       			$tagss=array(
       						'module_name'=> MENU_ID,
       						'parent_no'=>$last_id,
       						'tag_name'=>trim($tag_arr[$i]),
       						'reg_date'=>$this_date
				   			);
         		$this->db->insert($this->tags_table, $tagss);
			}

        }

        //첨부파일 DB 입력
		$file_cnt = $this->common->strip_image_tags_fck($post['contents'], $last_id, '', $this->table, MENU_ID); //글내용, 게시글번호, 타입(있으면 리플, 테이블명, 테이블번호)

		//파일갯수 업데이트
		$data_file = array(
               'files_count ' => $file_cnt
            );
  		$this->db->where('id', $last_id);
		$this->db->update($this->table, $data_file);

        return $last_id;

	}

	function update_board($no, $post) //글 수정
	{
       	if (@$post['fixed'] == 'on') {
			$fixed = "Y";
   		} else {
  			$fixed = "N";
       	}
       	if (@$post['secret'] == 'on') {
			$secret = "Y";
   		} else {
  			$secret = "N";
       	}
       	$general_setting = '';
       	$this_date = date("Y-m-d H:i:s");

		if(@$post['subject']) {
			$data = array(
						'modify_date' => $this_date,
						'is_notice' => $fixed,
						'is_secret' => $secret,
						'subject' => $post['subject'],
                        //'division' => $this->security->xss_clean($post['category_word']), //말머리
						'general_setting' => $general_setting,
						'contents' => $post['contents'],
						'ip' => $this->input->ip_address(),
						'password' => @$post['password']
			);
		} else {
			$data = array(
						'modify_date' => $this_date,
						'is_notice' => $fixed,
						'is_secret' => $secret,
						'general_setting' => $general_setting,
						'contents' => $post['contents'],
						'ip' => $this->input->ip_address(),
						'password' => @$post['password']
			);
		}
		$this->db->where('id', $no);
		$this->db->update($this->table, $data);

		//태그처리
		if (@$post['tags']) {
       		$this->db->delete($this->tags_table, array('parent_no'=>$no, 'module_name'=> MENU_ID));
       		$tag_arr = explode(",", $post['tags']);
       		$cnt = count($tag_arr);
       		for ($i=0; $i < $cnt; $i++) {
       			$tagss=array(
       						'module_name'=> $this->table,
							'module_type'=> 'reply',
       						'parent_no'=> $no,
       						'tag_name'=> trim($tag_arr[$i]),
       						'reg_date'=> $this_date
				   			);
         		$this->db->insert($this->tags_table, $tagss);
         }

        }
        //첨부파일 DB 입력
		$file_cnt = $this->strip_image_tags_edit($post['contents'], $no, ''); //글내용, 게시글번호, 타입(있으면 리플)

		//기존 파일 갯수
		$qur = $this->db->get_where($this->table, array('id'=>$no));
		$rows = $qur->row();
		$file_cnt = $file_cnt+$rows->files_count;

		//파일갯수 업데이트
		$data_file = array(
               'files_count ' => $file_cnt
            );
  		$this->db->where('id', $no);
		$this->db->update($this->table, $data_file);

	}

	function strip_image_tags_edit($str, $no, $type)
	{
		//기존 db내용 삭제
      	$this->db->delete($this->files_table, array('board_id'=>$no, 'file_type'=>'', 'original_name'=>''));
		preg_match_all("<img [^<>]*>", $str, $out, PREG_PATTERN_ORDER);
		$strs = $out[0];

		$cnt = count($strs);
		for ($i=0;$i<$cnt;$i++) {
  			$arr = preg_replace("#img\s+.*?src\s*=\s*[\"']\s*\/uploads/\s*(.+?)[\"'].*?\/#", "\\1", $strs[$i]);
			$data = array(
			  			'board_id'=>$no,
						'module_type'=>$type,
						'file_name'=>$arr,
  						'reg_date'=>date("Y-m-d H:i:s")
			  			);
			if ( count($arr) <= 25 ) {
				$this->db->insert($this->files_table, $data);
			}
  		}

  		return $cnt;
	}

	function file_list($no)
	{
		$this->db->not_like('file_name','gif');
		$this->db->not_like('file_name','jpg');
		$this->db->not_like('file_name','bmp');
		$this->db->not_like('file_name','png');
		$this->db->not_like('file_name','jpeg');
		$query=$this->db->get_where($this->files_table, array('board_id'=>$no));

		return $query->result_array();
	}

	function load_list($page, $rp, $post, $table_id)
	{
		$where = "";
		$this->db->select($this->table.'.*, '.$this->files_table.'.file_name, users.nickname');
		//$this->db->order_by($this->table.'.no', 'desc');
		$this->db->group_by($this->table.'.id');
		$this->db->limit($rp, $page);
		if (@$post['method'])
        {
			if($post['method'] == 'all')
            {
				$where = "(subject like '%".$post['s_word']."%' or contents like '%".$post['s_word']."%') and ";
			}
            else
            {
				$this->db->like($post['method'], $post['s_word']);
				$where = "";
			}
  		}
  		$this->db->join($this->files_table, $this->files_table.'.board_id='.$this->table.'.id', 'left');
		$this->db->join('users', 'users.id='.$this->table.'.user_id', 'left');

		$where .= "((board.is_delete='N' and board.is_list = 'Y') or (board.is_delete='Y' and board.is_list = 'Y')) and ";
		$where .= "(board.table_id = '".$table_id."')";

        $this->db->where($where, NULL, FALSE);

		if(@$post['division'])
        {
			$this->db->where($this->table.'.division', urldecode($post['division']));
		}

        $this->db->order_by('board.board_pid desc, board.reply_order asc, board.id desc');
		$query = $this->db->get($this->table);

        return $query->result_array();
	}

	function load_list_total($post, $table_id)
	{
		$where = "";
		$this->db->select($this->table.'.user_id');
		if (@$post['method'])
        {
			if(@$post['method'] == 'all')
            {
				$where = "(subject like '%".$post['s_word']."%' or contents like '%".$post['s_word']."%') and ";
			}
            else
            {
				$this->db->like($post['method'], $post['s_word']);
				$where = "";
			}
  		}
		if(@$post['division'])
        {
			$where .= "board.division = '".urldecode($post['division'])."' and ";
		}
		$this->db->join('users', 'users.id=board.user_id', 'left');

        $where .= "((board.is_delete='N' and board.is_list = 'Y') or (board.is_delete='Y' and board.is_list = 'Y')) and ";
		$where .= " board.table_id = '".$table_id."'";

  		$this->db->where($where, NULL, FALSE);

		$query = $this->db->get($this->table);

        return $query->num_rows();
	}

	function board_tag($no, $m_no) //태그 가져오기
	{
		$this->db->select('tag_name');
		//$this->db->order_by('id', 'asc');
		$query = $this->db->get_where($this->tags_table, array('board_id' => $no));
		$row = $query->result_array();
		$result = '';
		
		foreach ( $row as $val ) 
		{
       		$result .= $val['tag_name'].", ";
		}
		$result = rtrim($result, ', ');
		
		return $result;
	}

	function board_view($no, $mode) //게시물 가져오기
	{
		if ($mode == 'view') 
        {
			$sql = "update `".$this->table."` set hit=hit+1 where id = '".$no."' ";
			$this->db->query($sql);
  		}
        
		$this->db->select($this->table.'.*, users.nickname, users.username');
		$this->db->join('users', 'users.id='.$this->table.'.user_id', 'left');
		
		$query = $this->db->get_where($this->table, array('board.id' => $no, 'board.is_delete' => 'N'));

        return $query->row_array();
	}

	function id_check($no) //게시물 작성 유저번호 반환
	{
		$this->db->select('user_id');
		$query = $this->db->get_where($this->table, array('id' => $no));

        return $query->row_array();
	}

    function comment_id_check($no) //게시물 작성 유저번호 반환
	{
		$this->db->select('user_id');
		$query = $this->db->get_where($this->comments_table, array('id' => $no));

        return $query->row_array();
	}

	function delete_post($no) //게시물 삭제
	{
		//원글이 삭제된다고 해서 답글까지 삭제되는 로직이 아님.
		//is_list Y, is_delete가 Y일 경우 리스트에서 제목이 아닌 "삭제된 글입니다." 표시
        //is_list N, is_delete Y 일 경우 리스트에서 표시 안함

        //답글이 있는지 체크, 있다면 is_list Y is_delete Y, 아니면 is_list N is_delete Y

        $this->db->select('id, reply_count, reply_order');
		$this->db->where('id', $no);
        $ques = $this->db->get($this->table);
        $ori_no = $ques->row();

        if ($ori_no->reply_order == '0') //게시판 원글
        {
            if ($ori_no->reply_count > '0') //답글이 있으면 is_delete만 Y
            {
                $data = array('is_delete' => 'Y');
            }
            else //답글이 없을 경우는 완전삭제
            {
                $data = array('is_list' => 'N', 'is_delete' => 'Y');
            }
        }
        else if($ori_no->reply_order >'0') //게시판 답글
        {
            $data = array('is_list' => 'N', 'is_delete' => 'Y');

            //답글수 조정 -1
            $this->db->set('reply_count' ,'reply_count-1', FALSE);
        }


		$this->db->where('id', $no);
		$query = $this->db->update($this->table, $data);

	}

    //게시판 보기에서 댓글 가져오기
	function comment_view($no)
	{
		$this->db->select('board_comments.*, users.nickname, users.username');

		$this->db->join('users', 'users.id='.$this->comments_table.'.user_id', 'left');

        $this->db->order_by('comment_id asc, comment_order asc, id asc');

		$this->db->group_by('board_comments.id');

		$where = "((".$this->comments_table.".is_delete='N' and ".$this->comments_table.".is_list = 'Y') or (".$this->comments_table.".is_delete='Y' and ".$this->comments_table.".is_list = 'Y'))";
		$this->db->where($this->comments_table.'.board_id', $no);
		$this->db->where($where, NULL, FALSE);
		$query = $this->db->get($this->comments_table);

        return $query->result_array();
	}

    //댓글 1개 가져오기
    function comment_one($no)
	{
		$this->db->select('board_comments.*, users.nickname, users.username');

		$this->db->join('users', 'users.id='.$this->comments_table.'.user_id', 'left');
		$query = $this->db->get_where($this->comments_table, array('id'=>$no, 'is_delete'=>'N'));

        return $query->result_array();
	}

	function bottom_low($no)
	{
 		$prev_no = $this->db->query("
		 (Select MIN(id) as pn from `".$this->table."`  where id > '".$no."'  and is_delete = 'N' and original_no ='0')
union	(Select MAX(id) as nn from `".$this->table."`  where id < '".$no."' and is_delete = 'N' and original_no ='0')");
 		$prev = $prev_no->result();
 		return $prev;
 	}


	function auto_link($str)
	{

		// 속도 향상 031011
		$str = preg_replace("/&lt;/", "\t_lt_\t", $str);
		$str = preg_replace("/&gt;/", "\t_gt_\t", $str);
		$str = preg_replace("/&amp;/", "&", $str);
		$str = preg_replace("/&quot;/", "\"", $str);
		$str = preg_replace("/&nbsp;/", "\t_nbsp_\t", $str);
		$str = preg_replace("/([^(http:\/\/)]|\(|^)(www\.[^[:space:]]+)/i", "\\1<A HREF=\"http://\\2\" TARGET='_blank'><font color=blue><u>\\2</u></font></A>", $str);
		$str = preg_replace("/([^(HREF=\"?'?)|(SRC=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,]+)/i", "\\1<A HREF=\"\\2\" TARGET='_blank'><font color=blue><u>\\2</u></font></A>", $str);
		// 이메일 정규표현식 수정 061004
		//$str = preg_replace("/(([a-z0-9_]|\-|\.)+@([^[:space:]]*)([[:alnum:]-]))/i", "<a href='mailto:\\1'>\\1</a>", $str);
		$str = preg_replace("/([0-9a-z]([-_\.]?[0-9a-z])*@[0-9a-z]([-_\.]?[0-9a-z])*\.[a-z]{2,4})/i", "<a href='mailto:\\1'>\\1</a>", $str);
		$str = preg_replace("/\t_nbsp_\t/", "&nbsp;" , $str);
		$str = preg_replace("/\t_lt_\t/", "&lt;", $str);
		$str = preg_replace("/\t_gt_\t/", "&gt;", $str);

		return $str;
	}

    function update_comment($no, $post) //댓글 수정
	{
       	$this_date = date("Y-m-d H:i:s");

		$data = array(
					'modify_date' => $this_date,
					'contents' => $this->security->xss_clean($post['contents']),
					'ip' => $this->input->ip_address()
		);

		$this->db->where('id', $no);
		$this->db->update($this->comments_table, $data);
  }

	// 게시물에 첨부된 이미지 가져오기
	function attached_images($no)
	{
		$this->db->select('file_name');
		$this->db->where('board_id', $no);
		$this->db->from($this->files_table);

		$query = $this->db->get();
		$result = array();

		foreach ($query->result() as $row) 
		{
			$file = $row->file_name;
			$extension = substr($file, strpos($file, '.') + 1);

			if (in_array(strtolower($extension), array('jpg', 'png', 'gif'))) $result[] = $row->file_name;
		}

		return $result;
	}
}
/* End of file Functions.php */
/* Location: ./plugins/board/model/board_model.php */