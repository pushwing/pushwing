<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Client_m extends CI_Model
{
	public function __construct()
    {
        parent::__construct();
    }

    public function get_lists_count($condition = array(), $auth_code='3')
	{
		$sql = "SELECT COUNT(A.id) AS `count` FROM `users` A
    		LEFT JOIN client B ON A.`id` = B.`user_id`
    		WHERE A.auth_code = '".$auth_code."'
		";

		$where = array();

        if ($auth_code == '9')
        {
            foreach ($condition as $key => $val)
            {
                if (!empty($key) && !empty($val))
                {
                    switch ($key)
                    {
                        case 'scope':
                            if ($val != '')
                            {
                                $where[] = 'A.`level` LIKE \'%' . $val . '%\'';
                            }
                        break;
                        case 'keyword':
                            if ($val != '')
                            {
                                $where[] = "(A.`nickname` LIKE '%" . $val . "%' OR A.`username` LIKE '%" . $val . "%')";
                            }
                        break;
                    }
                }
            }
        }
        else if ($auth_code == '3')
        {
            foreach ($condition as $key => $val)
            {
                if (!empty($key) && !empty($val))
                {
                    switch ($key)
                    {
                        case 'scope':
                            if ($val == 'c')
                            {
                                $where[] = 'B.`name` LIKE \'%' . $condition['keyword'] . '%\'';
                            }
                            else if ($val == 'b')
                            {
                                $where[] = 'B.`charge_person_name` LIKE \'%' . $condition['keyword'] . '%\'';
                            }
                        break;
                    }
                }
            }
        }
        if (sizeof($where) > 0) $sql .= ' AND ' . implode(' AND ', $where);

		$query = $this->db->query($sql);
		$result = $query->row_array();

		return $result['count'];
	}

	public function get_lists($condition = array(), $page = 1, $limit = 20, $auth_code='3')
	{
		$sql = "SELECT A.*, B.*
		FROM `users` A
		LEFT JOIN client B ON A.`id` = B.`user_id`
		WHERE A.auth_code = '".$auth_code."'
		";

		$where = array();

		if ($auth_code == '9')
        {
            foreach ($condition as $key => $val)
            {
                if (!empty($key) && !empty($val))
                {
                    switch ($key)
                    {
                        case 'scope':
                            if ($val != '')
                            {
                                $where[] = 'A.`level` LIKE \'%' . $val . '%\'';
                            }
                        break;
                        case 'keyword':
                            if ($val != '')
                            {
                                $where[] = "(A.`nickname` LIKE '%" . $val . "%' OR A.`username` LIKE '%" . $val . "%')";
                            }
                        break;
                    }
                }
            }
        }
        else if ($auth_code == '3')
        {
            foreach ($condition as $key => $val)
            {
                if (!empty($key) && !empty($val))
                {
                    switch ($key)
                    {
                        case 'scope':
                            if ($val == 'c')
                            {
                                $where[] = 'B.`name` LIKE \'%' . $condition['keyword'] . '%\'';
                            }
                            else if ($val == 'b')
                            {
                                $where[] = 'B.`charge_person_name` LIKE \'%' . $condition['keyword'] . '%\'';
                            }
                        break;
                    }
                }
            }
        }

		if (sizeof($where) > 0) $sql .=  ' AND ' .implode(' AND ', $where);

		$sql .= ' ORDER BY A.`id` DESC LIMIT ' . (($page - 1) * $limit) . ', ' . $limit;

		$query = $this->db->query($sql);

		return $query->result_array();
	}


    function my_info($id)
    {
        $this->db->select('users.*, client.*');
        $this->db->join('client','client.user_id = users.id','left');
        $query = $this->db->get_where('users',array('id' => $id));
        return $query->row();
    }

    /**
     * 클라이언트 정보 수정
     *
     */
    function update_user($user_id, $usr, $adv)
    {
        if (count($usr) > 0)
        {
            $this->db->where('id',$user_id);
            $this->db->update('users',$usr);
        }

        if (count($adv) > 0)
        {
            $this->db->where('user_id',$user_id);
            $this->db->update('client',$adv);
        }
    }

    /**
     * 클라이언트 등록
     *
     */
    function set_user($usr, $adv)
    {
        if (count($usr) > 0)
        {
            $this->db->insert('users',$usr);
            $last_id = big_last_id();
        }

        $adv['user_id'] = $last_id;

        if (count($adv) > 0)
        {
            $this->db->insert('client',$adv);
        }

        return $last_id;
    }

    public function get_contact_lists_count($condition = array(), $auth_code='3')
    {
        $sql = "SELECT COUNT(*) AS count FROM contact A";

        $where = " where A.content ='' and check_date = '0000-00-00 00:00:00' and status ='1'";

        if ($condition['scope'] and $condition['keyword'])
        {
            $where = ' AND A.`'.$condition['scope'].'` LIKE \'%' . $condition['keyword'] . '%\'';
        }

        $sql .= $where;

        $query = $this->db->query($sql);
        $result = $query->row_array();

        return $result['count'];
    }

    public function get_contact_lists($condition = array(), $page = 1, $limit = 20, $auth_code='3')
    {
        $sql = "SELECT A.* 	FROM contact A";

        $where = " where A.content ='' and check_date = '0000-00-00 00:00:00' and status='1'";

        if ($condition['scope'] and $condition['keyword'])
        {
            $where = ' AND A.`'.$condition['scope'].'` LIKE \'%' . $condition['keyword'] . '%\'';
        }

        $sql .=  $where;

        $sql .= ' ORDER BY A.`id` DESC LIMIT ' . (($page - 1) * $limit) . ', ' . $limit;

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    function get_contact_view($id)
    {
        $query = $this->db->get_where('contact',array('id' => $id));
        return $query->row_array();
    }

    function get_no_start_client()
    {
        //푸시 발송시에 마지막 푸시 발송일을 업데이트 하는 방식으로 바꿔야 할 듯.
        $time = date('Ymd',strtotime("-1 week", time()));
        $query = "select * from client where last_push_time is null and reg_date between 1 and {$time} ";
        return $this->db->query($query)->result_array();
    }

    function get_no_push_in_week()
    {
        //푸시를 발송했던 기록이 있는 클라이언트 중
        //마지막 푸시 발송일이 7일 전인 클라이언
        $time = strtotime("-1 week", time());
        $query = "select * from client where last_push_time < {$time}";
        return $this->db->query($query)->result_array();
    }
}