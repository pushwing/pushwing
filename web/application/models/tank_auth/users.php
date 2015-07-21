<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users
 *
 * This model represents user authentication data. It operates the following tables:
 * - user account data,
 * - user profiles
 *
 * @package	Tank_auth
 * @author	Ilya Konyukhov (http://konyukhov.com/soft/)
 */
class Users extends CI_Model
{
	private $table_name			= 'users';			// user accounts
	private $profile_table_name	= 'user_profiles';	// user profiles

	function __construct()
	{
		parent::__construct();

		$ci =& get_instance();
		$this->table_name			= $ci->config->item('db_table_prefix', 'tank_auth').$this->table_name;
		$this->profile_table_name	= $ci->config->item('db_table_prefix', 'tank_auth').$this->profile_table_name;
	}

	/**
	 * Get user record by Id
	 *
	 * @param	int
	 * @param	bool
	 * @return	object
	 */
	function get_user_by_id($user_id, $activated)
	{
		$this->db->where('id', $user_id);
		$this->db->where('activated', $activated ? 1 : 0);

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}

	/**
	 * Get user record by login (username or email)
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_login($login)
	{
		$this->db->where('LOWER(username)=', strtolower($login));
		$this->db->or_where('LOWER(email)=', strtolower($login));

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}

	/**
	 * Get user record by username
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_username($username)
	{
		$this->db->where('LOWER(username)=', strtolower($username));

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}

	/**
	 * Get user record by email
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_email($email)
	{
		$this->db->where('LOWER(email)=', strtolower($email));

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}

	/**
	 * Check if username available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_username_available($username)
	{
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(username)=', strtolower($username));

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 0;
	}

	/**
	 * Check if email available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_email_available($email)
	{
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(email)=', strtolower($email));
		$this->db->or_where('LOWER(new_email)=', strtolower($email));

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 0;
	}

	/**
	 * Create new user record
	 *
	 * @param	array
	 * @param	bool
	 * @return	array
	 */
	function create_user($data, $activated = TRUE)
	{
		$data['created'] = date('Y-m-d H:i:s');
		$data['activated'] = $activated ? 1 : 0;

		if ($this->db->insert($this->table_name, $data)) {
			$user_id = big_last_id();

			if ($activated)	$this->create_profile($user_id);
			return array('user_id' => $user_id);

			$arr = array('user_id'=>$user_id);
			$this->db->insert('user_profiles', $arr);
		}
		return NULL;
	}

	function point_action($type, $amount, $reason, $user_id, $entrys_id='')
	{
		/*
		$this->db->select('total_point');
		$this->db->order_by('id', 'desc');
		$this->db->limit(1);
		$query = $this->db->get_where('points', array('user_id'=>$user_id));
		$row = $query->row();

		if(@$row->total_point)
		{
			$tots = $row->total_point;
		}
		else
		{
			$tots = '0';
		}

		if($type == '+')
		{
			$tot = $tots + $amount;
		}
		else
		{
			$tot = $tots - $amount;
		}



		$arr = array('type'=>$type,
					'amount'=>$amount,
					'total_point'=>$tot,
					'reason'=>$reason,
					'user_id'=>$user_id,
					'entrys_id'=>$entrys_id,
					'reg_date'=>time()
			);

		$this->db->insert('points', $arr);
		return big_last_id();
		*/
	}

	/**
	 * Activate user if activation key is valid.
	 * Can be called for not activated users only.
	 *
	 * @param	int
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function activate_user($user_id, $activation_key, $activate_by_email)
	{
		$this->db->select('1', FALSE);
		$this->db->where('user_id', $user_id);
		if ($activate_by_email) {
			$this->db->where('new_email_key', $activation_key);
		} else {
			$this->db->where('new_password_key', $activation_key);
		}
		$this->db->where('activated', 0);
		$query = $this->db->get($this->table_name);

		if ($query->num_rows() == 1) {

			$this->db->set('activated', 1);
			$this->db->set('new_email_key', NULL);
			$this->db->where('user_id', $user_id);
			$this->db->update($this->table_name);

			$this->create_profile($user_id);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Purge table of non-activated users
	 *
	 * @param	int
	 * @return	void
	 */
	function purge_na($expire_period = 172800)
	{
		$this->db->where('activated', 0);
		$this->db->where('UNIX_TIMESTAMP(created) <', time() - $expire_period);
		$this->db->delete($this->table_name);
	}

	/**
	 * Delete user record
	 *
	 * @param	int
	 * @return	bool
	 */
	function delete_user($user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->delete($this->table_name);
		if ($this->db->affected_rows() > 0) {
			$this->delete_profile($user_id);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Set new password key for user.
	 * This key can be used for authentication when resetting user's password.
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function set_password_key($user_id, $new_pass_key)
	{
		$this->db->set('new_password_key', $new_pass_key);
		$this->db->set('new_password_requested', date('Y-m-d H:i:s'));
		$this->db->where('id', $user_id);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Check if given password key is valid and user is authenticated.
	 *
	 * @param	int
	 * @param	string
	 * @param	int
	 * @return	void
	 */
	function can_reset_password($user_id, $new_pass_key, $expire_period = 900)
	{
		$this->db->select('1', FALSE);
		$this->db->where('id', $user_id);
		$this->db->where('new_password_key', $new_pass_key);
		$this->db->where('UNIX_TIMESTAMP(new_password_requested) >', time() - $expire_period);

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 1;
	}

	/**
	 * Change user password if password key is valid and user is authenticated.
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	function reset_password($user_id, $new_pass, $new_pass_key, $expire_period = 900)
	{
		$this->db->set('password', $new_pass);
//		$this->db->set('new_password_key', NULL);
//		$this->db->set('new_password_requested', NULL);
		$this->db->where('id', $user_id);
//		$this->db->where('new_password_key', $new_pass_key);
//		$this->db->where('UNIX_TIMESTAMP(new_password_requested) >=', time() - $expire_period);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Change user password
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function change_password($user_id, $new_pass)
	{
		$this->db->set('password', $new_pass);
		$this->db->where('id', $user_id);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Set new email for user (may be activated or not).
	 * The new email cannot be used for login or notification before it is activated.
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function set_new_email($user_id, $new_email, $new_email_key, $activated)
	{
		$this->db->set($activated ? 'new_email' : 'email', $new_email);
		$this->db->set('new_email_key', $new_email_key);
		$this->db->where('id', $user_id);
		$this->db->where('activated', $activated ? 1 : 0);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Activate new email (replace old email with new one) if activation key is valid.
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function activate_new_email($user_id, $new_email_key)
	{
		$this->db->set('email', 'new_email', FALSE);
		$this->db->set('new_email', NULL);
		$this->db->set('new_email_key', NULL);
		$this->db->where('id', $user_id);
		$this->db->where('new_email_key', $new_email_key);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Update user login info, such as IP-address or login time, and
	 * clear previously generated (but not activated) passwords.
	 *
	 * @param	int
	 * @param	bool
	 * @param	bool
	 * @return	void
	 */
	function update_login_info($user_id, $record_ip, $record_time)
	{
		$dt = date("Y-m-d");
		$this->db->select('id');
		$this->db->like('last_login', $dt, 'after');
		$quey = $this->db->get_where('users', array('id'=>$user_id));
		$rows = $quey->num_rows();

		$this->db->set('new_password_key', NULL);
		$this->db->set('new_password_requested', NULL);

		if ($record_ip)		$this->db->set('last_ip', $this->input->ip_address());
		if ($record_time)	$this->db->set('last_login', date('Y-m-d H:i:s'));

		$this->db->where('id', $user_id);
		$this->db->update($this->table_name);
	}

	/**
	 * Ban user
	 *
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	function ban_user($user_id, $reason = NULL)
	{
		$this->db->where('id', $user_id);
		$this->db->update($this->table_name, array(
			'banned'		=> 1,
			'ban_reason'	=> $reason,
		));
	}

	/**
	 * Unban user
	 *
	 * @param	int
	 * @return	void
	 */
	function unban_user($user_id)
	{
		$this->db->where('id', $user_id);
		$this->db->update($this->table_name, array(
			'banned'		=> 0,
			'ban_reason'	=> NULL,
		));
	}

	/**
	 * Create an empty profile for a new user
	 *
	 * @param	int
	 * @return	bool
	 */
	private function create_profile($user_id)
	{
		$this->db->set('id', $user_id);
		return $this->db->insert($this->profile_table_name);
	}

	/**
	 * Delete user profile
	 *
	 * @param	int
	 * @return	void
	 */
	private function delete_profile($user_id)
	{
		$this->db->where('id', $user_id);
		$this->db->delete($this->profile_table_name);
	}

	// 사용자 프로필 생성 유무 체크
	public function is_user_profile_created($user_id)
	{
		$this->db->where('user_id', $user_id);
		$query = $this->db->get($this->profile_table_name);
		return $query->num_rows() == 1;
	}

	// 트위터 아이디 프로필 정보 유무 체크
	public function twitter_id_exists($twitter_id)
	{
		$this->db->where('twitter_id', $twitter_id);
		$query = $this->db->get($this->profile_table_name);
		return $query->num_rows() == 1;
	}

	// 페이스북 아이디 프로필 정보 유무 체크
	public function facebook_id_exists($facebook_id)
	{
		$this->db->where('facebook_id', $facebook_id);
		$query = $this->db->get($this->profile_table_name);
		return $query->num_rows() == 1;
	}

	// 프로필에 트위터와 사용자 아이디를 매핑 하여 생성
	public function create_profile_twitter($user_id, $twitter_id)
	{
		$this->db->set('user_id', $user_id);
		$this->db->set('twitter_id', $twitter_id);
		return $this->db->insert($this->profile_table_name);
	}

	// 프로필에 페이스북과 사용자 아이디를 매핑 하여 생성
	public function create_profile_facebook($user_id, $facebook_id)
	{
		$this->db->set('user_id', $user_id);
		$this->db->set('facebook_id', $facebook_id);
		return $this->db->insert($this->profile_table_name);
	}

	// 트위터 아이디를 유져의 프로필에 매핑
	public function map_profile_twitter($user_id, $twitter_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->update($this->profile_table_name, array('twitter_id' => $twitter_id));
	}

	// 페이스북 아이디를 유져의 프로필에 매핑
	public function map_profile_facebook($user_id, $facebook_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->update($this->profile_table_name, array('facebook_id' => $facebook_id));
	}

	// 트위터 아이디로 매핑된 유져 아이디를 가져옴
	public function get_map_user_id_by_twitter($twitter_id)
	{
		$this->db->where('twitter_id', $twitter_id);
		$query = $this->db->get($this->profile_table_name);
		if ($query->num_rows() == 1) return $query->row();
		else return null;
	}

	// 페이스북 아이디로 매핑된 유져 아이디를 가져옴
	public function get_map_user_id_by_facebook($facebook_id)
	{
		$this->db->where('facebook_id', $facebook_id);
		$query = $this->db->get($this->profile_table_name);
		if ($query->num_rows() == 1) return $query->row();
		else return null;
	}
}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */