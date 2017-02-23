<?php
class Backend extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('Table');
		$this->load->library('Pagination');
		$this->load->library('DX_Auth');
		$this->load->library('Form_validation');
		
		$this->load->helper('form');
		$this->load->helper('url');
			
		// Protect entire controller so only admin, 
		// and users that have granted role in permissions table can access it.
		$this->dx_auth->check_uri_permissions();
	}
	
	function index()
	{
		$this->users();
	}

	/**
	 * 生成页面
	 *
	 */
	private function _render_page($html, $menu='')
	{
		$buffer = '';
		$buffer .= $this->load->view('auth/page_top', '', TRUE);
		$buffer .= $html;
		$buffer .= $this->load->view('auth/page_bottom', '', TRUE);

		echo $buffer;
	}
	
	function users()
	{
		$html = '';
		$this->load->model('dx_auth/users', 'users');			
		
		// Search checkbox in post array
		foreach ($_POST as $key => $value)
		{
			// If checkbox found
			if (substr($key, 0, 9) == 'checkbox_')
			{
				// If ban button pressed
				if (isset($_POST['ban']))
				{
					// Ban user based on checkbox value (id)
					$this->users->ban_user($value);
				}
				// If unban button pressed
				else if (isset($_POST['unban']))
				{
					// Unban user
					$this->users->unban_user($value);
				}
				else if (isset($_POST['reset_pass']))
				{
					// Set default message
					$data['reset_message'] = '重置密码失败。';
				
					// Get user and check if User ID exist
					if ($query = $this->users->get_user_by_id($value) AND $query->num_rows() == 1)
					{		
						// Get user record				
						$user = $query->row();
						
						// Create new key, password and send email to user
						if ($this->dx_auth->forgot_password($user->username))
						{
							// Query once again, because the database is updated after calling forgot_password.
							$query = $this->users->get_user_by_id($value);
							// Get user record
							$user = $query->row();
														
							// Reset the password
							if ($this->dx_auth->reset_password($user->username, $user->newpass_key))
							{							
								$data['reset_message'] = '重置密码成功';
							}
						}
					}
				} /* Todo ... delete user
				   * TurboY 2016/5/21
				else if (isset($_POST['delete']))
				{
					$this->db->where_in('id', $value)->delete('users');
					$this->db->where_in('id', $value)->delete('user_profile');
				} */
			}				
		}
		
		/* Showing page to user */
		
		// Get offset and limit for page viewing
		$offset = (int) $this->uri->segment(3);
		// Number of record showing per page
		$row_count = 10;
		
		// Get all users
		$data['users'] = $this->users->get_all($offset, $row_count)->result();
		
		// Pagination config
		$p_config['base_url'] = '/backend/users/';
		$p_config['uri_segment'] = 3;
		$p_config['num_links'] = 2;
		$p_config['total_rows'] = $this->users->get_all()->num_rows();
		$p_config['per_page'] = $row_count;
				
		// Init pagination
		$this->pagination->initialize($p_config);		
		// Create pagination links
		$data['pagination'] = $this->pagination->create_links();
		
		// Load view
		$html .= $this->load->view('backend/users', $data, TRUE);
		
		$this->_render_page($html);
	}
	
	function unactivated_users()
	{
		$this->load->model('dx_auth/user_temp', 'user_temp');
		
		/* Database related */
		
		// If activate button pressed
		if ($this->input->post('activate'))
		{
			// Search checkbox in post array
			foreach ($_POST as $key => $value)
			{
				// If checkbox found
				if (substr($key, 0, 9) == 'checkbox_')
				{
					// Check if user exist, $value is username
					if ($query = $this->user_temp->get_login($value) AND $query->num_rows() == 1)
					{
						// Activate user
						$this->dx_auth->activate($value, $query->row()->activation_key);
					}
				}				
			}
		}
		
		/* Showing page to user */
		
		// Get offset and limit for page viewing
		$offset = (int) $this->uri->segment(3);
		// Number of record showing per page
		$row_count = 10;
		
		// Get all unactivated users
		$data['users'] = $this->user_temp->get_all($offset, $row_count)->result();
		
		// Pagination config
		$p_config['base_url'] = '/backend/unactivated_users/';
		$p_config['uri_segment'] = 3;
		$p_config['num_links'] = 2;
		$p_config['total_rows'] = $this->user_temp->get_all()->num_rows();
		$p_config['per_page'] = $row_count;
				
		// Init pagination
		$this->pagination->initialize($p_config);		
		// Create pagination links
		$data['pagination'] = $this->pagination->create_links();
		
		// Load view
		$html = $this->load->view('backend/unactivated_users', $data, TRUE);
		
		$this->_render_page($html);
	}


	function user_add()
	{
		$this->_user_edit(0);
	}
	
	function user_edit($user_id=-1)
	{
		$this->_user_edit($user_id);
	}
	
	/*
	 * user_edit()
	 * Add by TURBOY 2016
	 */
	function _user_edit($user_id=0)
	{
		$html = '';
		$this->load->model('dx_auth/users', 'users');
		$this->load->model('dx_auth/roles', 'roles');
		
		$users = $this->users->get_all($offset, $row_count)->result();
		
		if ($user_id == 0) {
			$data['user'] = (object) array(
				'id' => 0,
				'username' => '',
				'email' => '',
				'role_id' => 1,
				'home' => ''
			);
		}
		else {
			$data['user'] = NULL;
			foreach ($users as $u) {
				if ($user_id == $u->id) {
					$data['user'] = $u;
					break;
				}
			}
			if ($data['user'] == NULL) {
				$html = '用户名不存在。';
				$this->_render_page($html);
				return;
			}
		}
		
		$val = $this->form_validation;	
		$val->set_rules(
			array(
				array(
					'field'   => 'id', 
					'label'   => '用户ID',
					'rules'   => 'trim|strtolower|required|xss_clean|integer'
				),
				array(
					'field'   => 'username', 
					'label'   => '用户名',
					'rules'   => 'trim|strtolower|required|xss_clean|alpha_dash|max_length[20]' . ($user_id == 0 ? '|is_unique[users.username]' : '')
				),
				array(
					'field'   => 'email', 
					'label'   => '用户邮箱',
					'rules'   => 'trim|strtolower|required|xss_clean|valid_email' . ($user_id == 0 ? '|is_unique[users.email]' : '')
				),
				array(
					'field'   => 'role_id', 
					'label'   => '角色',
					'rules'   => 'trim|strtolower|required|xss_clean|integer'
				),
				array(
					'field'   => 'home', 
					'label'   => '用户入口URI',
					'rules'   => 'trim|strtolower|required|xss_clean'
				),
				array(
					'field'   => 'password', 
					'label'   => '新密码',
					'rules'   => 'trim|strtolower|' . ($user_id == 0 ? 'required|' : '') . 'xss_clean|max_length[20]'
				),
				array(
					'field'   => 'password2', 
					'label'   => '再次输入新密码',
					'rules'   => 'trim|strtolower|' . ($user_id == 0 ? 'required|' : '') . 'xss_clean|max_length[20]|matches[password]'
				)
			)
		);
		$val->set_message('is_unique', '%s 已经使用过。');
		if ($val->run() == FALSE) {
			$data['roles'] = $this->roles->get_all()->result();
			$html = $this->load->view('backend/user_editor', $data, TRUE);
		}
		else {
			if (intval($val->set_value('id')) == intval($user_id)) {
				if ($user_id == 0) {
					//register new user
					$this->dx_auth->register($val->set_value('username'), $val->set_value('password'), $val->set_value('email'));
					$newusers = $this->users->get_user_by_username($val->set_value('username'))->result();
					if (count($newusers) > 0) {
						$user = $newusers[0];
						$this->users->set_user($user->id, array('role_id'=>$val->set_value('role_id'), 'home'=>$val->set_value('home')));
						$html .= '新增用户 “' . $val->set_value('username') . '”  (id=' . $user->id . ') 成功。';
					}
					$this->session->set_flashdata('message', date('Y-m-d H:i:s') . ' ' . $html);
					redirect('backend/user_edit/' . $user->id);
				}
				else { //修改用户信息、密码
					$sql_data = array();
					if ($data['user']->username != $val->set_value('username')) {
						$sql_data['username'] = $val->set_value('username');
					}
					if ($data['user']->email != $val->set_value('email')) {
						$sql_data['email'] = $val->set_value('email');
					}
					if ($data['user']->role_id != $val->set_value('role_id')) {
						$sql_data['role_id'] = $val->set_value('role_id');
					}
					if ($data['user']->home != $val->set_value('home')) {
						$sql_data['home'] = $val->set_value('home');
					}
					if ($val->set_value('password') != '') {
						$newpassword = $val->set_value('password');
						$sql_data['password'] = crypt($this->dx_auth->_encode($newpassword), '$1$' . $this->dx_auth->_gen_pass(8) . '$');
						$html .= '用户' . $data['user']->username . '的密码已修改。';
					}
					if (count($sql_data) > 0) {
						$this->users->set_user($user_id, $sql_data);
						if (!array_key_exists('password', $sql_data)) {
							$html .= '用户' . $data['user']->username . '的信息已修改。';
						}
						$this->session->set_flashdata('message', date('Y-m-d H:i:s') . ' ' . $html);
					}
					redirect($this->uri->uri_string());
				}
				return;
			}
			else {
				$html .= 'ID error.';
			}
		}
				
		$this->_render_page($html);
	}
	
	function roles()
	{
		$html = '';
		$this->load->model('dx_auth/roles', 'roles');
		
		/* Database related */
					
		// If Add role button pressed
		if ($this->input->post('add'))
		{
			// Create role
			$this->roles->create_role($this->input->post('role_name'), $this->input->post('role_parent'));
		}
		else if ($this->input->post('delete'))
		{				
			// Loop trough $_POST array and delete checked checkbox
			foreach ($_POST as $key => $value)
			{
				// If checkbox found
				if (substr($key, 0, 9) == 'checkbox_')
				{
					// Delete role
					$this->roles->delete_role($value);
				}				
			}
		}

		/* Showing page to user */
	
		// Get all roles from database
		$data['roles'] = $this->roles->get_all()->result();
		
		// Load view
		$html .= $this->load->view('backend/roles', $data, TRUE);
		
		$this->_render_page($html);
	}
	
	function uri_permissions()
	{
		$html = '';
		
		function trim_value(&$value) 
		{ 
			$value = trim($value); 
		}
	
		$this->load->model('dx_auth/roles', 'roles');
		$this->load->model('dx_auth/permissions', 'permissions');
		
		if ($this->input->post('save'))
		{
			// Convert back text area into array to be stored in permission data
			$allowed_uris = explode("\n", $this->input->post('allowed_uris'));
			
			// Remove white space if available
			array_walk($allowed_uris, 'trim_value');
		
			// Set URI permission data
			// IMPORTANT: uri permission data, is saved using 'uri' as key.
			// So this key name is preserved, if you want to use custom permission use other key.
			$this->permissions->set_permission_value($this->input->post('role'), 'uri', $allowed_uris);
		}
		
		/* Showing page to user */		
		
		// Default role_id that will be showed
		$role_id = $this->input->post('role') ? $this->input->post('role') : 1;
		$data['role_id'] = $role_id;
		
		// Get all role from database
		$data['roles'] = $this->roles->get_all()->result();
		// Get allowed uri permissions
		$data['allowed_uris'] = $this->permissions->get_permission_value($role_id, 'uri');
		
		// Load view
		$html .= $this->load->view('backend/uri_permissions', $data, TRUE);
		
		$this->_render_page($html);
	}
	
	function custom_permissions()
	{
		$html = '';
		$dx_permission_keys = $this->config->item('dx_permission_keys');
		
		// Load models
		$this->load->model('dx_auth/roles', 'roles');
		$this->load->model('dx_auth/permissions', 'permissions');
	
		/* Get post input and apply it to database */
		
		// If button save pressed
		if ($this->input->post('save'))
		{
			// Note: Since in this case we want to insert two key with each value at once,
			// it's not advisable using set_permission_value() function						
			// If you calling that function twice that means, you will query database 4 times,
			// because set_permission_value() will access table 2 times, 
			// one for get previous permission and the other one is to save it.
			
			// For this case (or you need to insert few key with each value at once) 
			// Use the example below
		
			// Get role_id permission data first. 
			// So the previously set permission array key won't be overwritten with new array with key $key only, 
			// when calling set_permission_data later.
			$permission_data = $this->permissions->get_permission_data($this->input->post('role'));
		
			// Set value in permission data array
			foreach ($dx_permission_keys as $pk) {
				if ($this->input->post($pk) != '') {
					$permission_data[$pk] = $this->input->post($pk);
				}
				else {
					$permission_data[$pk] = 0;
				}
			}
			
			// Set permission data for role_id
			$this->permissions->set_permission_data($this->input->post('role'), $permission_data);
		}
	
		/* Showing page to user */		
		
		// Default role_id that will be showed
		$role_id = $this->input->post('role') ? $this->input->post('role') : 1;
		$data['role_id'] = $role_id;
		
		// Get all role from database
		$data['roles'] = $this->roles->get_all()->result();
		// Get edit and delete permissions
		$data['dx_permission_keys'] = array();
		foreach ($dx_permission_keys as $pk) {
			$data['dx_permission_keys'][$pk] = $this->permissions->get_permission_value($role_id, $pk);
		}
	
		// Load view
		$html .= $this->load->view('backend/custom_permissions', $data, TRUE);
		
		$this->_render_page($html);
	}
}
?>