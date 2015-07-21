<?php
require_once("./lib/class.MySQL.php");

/**
 * 전문 Model
 * @author 변종원 <codeigniterk@gmail.com>
 */
class Engine {

	function __construct()
	{
	    // Initiate Database connection
        $this->dbConnect();
    }

    /*
     *  Database connection
    */
    private function dbConnect()
    {
		$this->db = new MySQL('데이터베이스', '아이디', '비밀번호', 'localhost');
    }

	/**
 * 광고 on, off get flag
 * 앱 실행 첫화면의 광고 유무
 * @author 변종원 <codeigniterk@gmail.com>
 */
    function get_flag()
    {
        $sql = "SELECT code_value FROM checks WHERE code_name = 'ad_start'";
        $result = $this->db->ExecuteSQL($sql);

		$sql1 = "SELECT code_value as ver FROM checks WHERE code_name = 'cur_ver'";
        $result1 = $this->db->ExecuteSQL($sql1);

		$sql2 = "SELECT code_value as updates FROM checks WHERE code_name = 'update'";
        $result2 = $this->db->ExecuteSQL($sql2);


		$ver = explode("|", $result1[0]['ver']);

		$result[0]['and_ver'] = $ver[0];
		$result[0]['ios_ver'] = $ver[1];

		$result[0]['update'] = $result2[0]['updates'];

        return $result;
    }

    /**
     * id에 해당하는 push_end 내용 반환
     * @author 변종원 <codeigniterk@gmail.com>
     */
    function get_push_end($id)
    {
        $sql = "SELECT * FROM push_end WHERE id = '".$id."'";
        $result = $this->db->ExecuteSQL($sql);

        return $result[0];
    }


	/**
	 * insert 액션처리
	 *
	 */
	function insert_act($arr, $table)
	{
        $result = 'fail';

		$sql0 = "SELECT * FROM push_db WHERE hp = '".$arr['hp']."'";
        $result0 = $this->db->ExecuteSQL($sql0);

		if(count($result0[0]) > 0)
		{
			//device_id가 존재하여 업데이트
			$sql = "update push_db set device_id='".$arr['device_id']."', type='".$arr['type']."' where hp='".$arr['hp']."'";
            $this->db->ExecuteSQL($sql);

            $result = 'success';
		}
		else
		{
			$this->db->Insert($arr, $table);

			//첫 푸시 전송
			$push_arr = array(
				'hp' => $arr['hp'],
				'client_id' => '2',
				'subject' => '푸시윙을 설치해주셔서 감사합니다!',
				'contents' => '푸시윙은 앱이 없는 웹사이트를 위한 통합 푸시 알림 수신 서비스입니다. 자주가는 웹사이트에서 내가 올린 질문에 댓글이 달릴 때, 친구 신청을 받을 때, 쪽지를 받을 때 바로바로 알림을 받아보세요. 아래 웹사이트 방문 버튼을 눌러 지원되는 웹사이트 목록을 확인하고, 없으면 추가 신청을 해주세요.',
				'ymd' => date("ymd"),
				'time' => date("G"),
				'timestamp' => time(),
				'url' => 'http://www.pushwing.com/main/partners'
			);

			$this->db->Insert($push_arr, 'push_wait');
            $result = 'success';
		}

        return $result;
	}


    /**
     * 공지사항 리스트 및 주소
     *
     */
    function get_notice_list()
    {
        $where = "select id, subject, hit, reg_date from board where table_id='2' and is_delete = 'N' and is_list = 'Y' order by id desc limit 10";

        $result = $this->db->ExecuteSQL($where);

        return $result;
    }

    /**
     * 공지사항 내용 보기
     * @param $board_id
     * @return mixed
     */
    function get_notice_view($board_id)
    {
        $sql = "select id, subject, contents, hit, reg_date from board where id='".$board_id."' and is_delete = 'N' and is_list = 'Y'";

        $result = $this->db->ExecuteSQL($sql);

        return $result;
    }
}