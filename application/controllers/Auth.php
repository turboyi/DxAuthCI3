<?php
class Auth extends CI_Controller
{
	// Used for registering and changing password form validation
	var $min_username = 4;
	var $max_username = 20;
	var $min_password = 4;
	var $max_password = 20;

	function __construct()
	{
		parent::__construct();
		
		$this->load->library('Form_validation');
		$this->load->library('DX_Auth');			
		
		$this->load->helper('url');
		$this->load->helper('form');		
	}
	
	function index()
	{
		$this->login();
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
	
	/* Callback function */
	
	function username_check($username)
	{
		$result = $this->dx_auth->is_username_available($username);
		if ( ! $result)
		{
			$this->form_validation->set_message('username_check', '此用户名已经被其他用户注册。 请另选一个用户名。');
		}
				
		return $result;
	}

	function email_check($email)
	{
		$result = $this->dx_auth->is_email_available($email);
		if ( ! $result)
		{
			$this->form_validation->set_message('email_check', '此电子邮箱已被其他用户使用。 请另选择一个邮箱地址。');
		}
				
		return $result;
	}

	function captcha_check($code)
	{
		$result = TRUE;
		
		if ($this->dx_auth->is_captcha_expired())
		{
			// Will replace this error msg with $lang
			$this->form_validation->set_message('captcha_check', '你输入的验证码已过期。 请再次输入。');			
			$result = FALSE;
		}
		elseif ( ! $this->dx_auth->is_captcha_match($code))
		{
			$this->form_validation->set_message('captcha_check', '你输入的验证码与图片中的不同！ 请再次输入。');			
			$result = FALSE;
		}

		return $result;
	}
	
	function recaptcha_check()
	{
		$result = $this->dx_auth->is_recaptcha_match();		
		if ( ! $result)
		{
			$this->form_validation->set_message('recaptcha_check', '你输入的验证码与图片中的不同！ 请再次输入。');
		}
		
		return $result;
	}
	
	/* End of Callback function */
	
	
	function login()
	{
		$html = '';
		
		if ( ! $this->dx_auth->is_logged_in())
		{
			$val = $this->form_validation;
			
			// Set form validation rules
			$val->set_rules('username', 'Username', 'trim|required');
			$val->set_rules('password', 'Password', 'trim|required');
			$val->set_rules('remember', 'Remember me', 'integer');

			// Set captcha rules if login attempts exceed max attempts in config
			if ($this->dx_auth->is_max_login_attempts_exceeded())
			{
				$val->set_rules('captcha', 'Confirmation Code', 'trim|required|callback_captcha_check');
			}
				
			if ($val->run() AND $this->dx_auth->login($val->set_value('username'), $val->set_value('password'), $val->set_value('remember')))
			{
				// Redirect to homepage
				//redirect('', 'location');
				redirect($this->dx_auth->get_user_home(), 'location');
			}
			else
			{
				// Check if the user is failed logged in because user is banned user or not
				if ($this->dx_auth->is_banned())
				{
					// Redirect to banned uri
					$this->dx_auth->deny_access('banned');
				}
				else
				{						
					// Default is we don't show captcha until max login attempts eceeded
					$data['show_captcha'] = FALSE;
				
					// Show captcha if login attempts exceed max attempts in config
					if ($this->dx_auth->is_max_login_attempts_exceeded())
					{
						// Create catpcha						
						$this->dx_auth->captcha();
						
						// Set view data to show captcha on view file
						$data['show_captcha'] = TRUE;
					}
					
					// Load login page view
					$html = $this->load->view($this->dx_auth->login_view, $data, TRUE);
				}
			}
		}
		else
		{
			$data['auth_message'] = '你已经登录进系统。';
			$html = $this->load->view($this->dx_auth->logged_in_view, $data, TRUE);
		}
		$this->_render_page($html);
	}
	
	function logout()
	{
		$html = '';
		
		$this->dx_auth->logout();
		
		$data['auth_message'] = '你已退出系统。';		
		$html = $this->load->view($this->dx_auth->logout_view, $data, TRUE);
		$this->_render_page($html);
	}
	
	function register()
	{
		$html = '';
		
		if ( ! $this->dx_auth->is_logged_in() AND $this->dx_auth->allow_registration)
		{	
			$val = $this->form_validation;
			
			// Set form validation rules			
			$val->set_rules('username', 'Username', 'trim|required|min_length['.$this->min_username.']|max_length['.$this->max_username.']|callback_username_check|alpha_dash');
			$val->set_rules('password', 'Password', 'trim|required|min_length['.$this->min_password.']|max_length['.$this->max_password.']|matches[confirm_password]');
			$val->set_rules('confirm_password', 'Confirm Password', 'trim|required');
			$val->set_rules('email', 'Email', 'trim|required|valid_email|callback_email_check');
			
			if ($this->dx_auth->captcha_registration)
			{
				$val->set_rules('captcha', 'Confirmation Code', 'trim|required|callback_captcha_check');
			}

			// Run form validation and register user if it's pass the validation
			if ($val->run() AND $this->dx_auth->register($val->set_value('username'), $val->set_value('password'), $val->set_value('email')))
			{	
				// Set success message accordingly
				if ($this->dx_auth->email_activation)
				{
					$data['auth_message'] = '你已经成功注册！ 请到你的邮箱中查收邮件并激活你的账户。';
				}
				else
				{					
					$data['auth_message'] = '你已经成功注册！ '.anchor(site_url($this->dx_auth->login_uri), '登录');
				}
				
				// Load registration success page
				$html .= $this->load->view($this->dx_auth->register_success_view, $data, TRUE);
			}
			else
			{
				// Is registration using captcha
				if ($this->dx_auth->captcha_registration)
				{
					$this->dx_auth->captcha();										
				}

				// Load registration page
				$html .= $this->load->view($this->dx_auth->register_view, NULL, TRUE);
			}
		}
		elseif ( ! $this->dx_auth->allow_registration)
		{
			$data['auth_message'] = '系统禁用了注册功能。';
			$html .= $this->load->view($this->dx_auth->register_disabled_view, $data, TRUE);
		}
		else
		{
			$data['auth_message'] = '必须退出当前用户后，才能注册新用户。';
			$html .= $this->load->view($this->dx_auth->logged_in_view, $data, TRUE);
		}
		$this->_render_page($html);
	}
	
	function register_recaptcha()
	{
		$html = '';
		
		if ( ! $this->dx_auth->is_logged_in() AND $this->dx_auth->allow_registration)
		{	
			$val = $this->form_validation;
			
			// Set form validation rules
			$val->set_rules('username', 'Username', 'trim|required|min_length['.$this->min_username.']|max_length['.$this->max_username.']|callback_username_check|alpha_dash');
			$val->set_rules('password', 'Password', 'trim|required|min_length['.$this->min_password.']|max_length['.$this->max_password.']|matches[confirm_password]');
			$val->set_rules('confirm_password', 'Confirm Password', 'trim|required');
			$val->set_rules('email', 'Email', 'trim|required|valid_email|callback_email_check');
			
			// Is registration using captcha
			if ($this->dx_auth->captcha_registration)
			{
				// Set recaptcha rules.
				// IMPORTANT: Do not change 'recaptcha_response_field' because it's used by reCAPTCHA API,
				// This is because the limitation of reCAPTCHA, not DX Auth library
				$val->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|required|callback_recaptcha_check');
			}

			// Run form validation and register user if it's pass the validation
			if ($val->run() AND $this->dx_auth->register($val->set_value('username'), $val->set_value('password'), $val->set_value('email')))
			{	
				// Set success message accordingly
				if ($this->dx_auth->email_activation)
				{
					$data['auth_message'] = '你已经成功注册！ 请到你的邮箱中查收邮件并激活你的账户。';
				}
				else
				{					
					$data['auth_message'] = '你已经成功注册！ '.anchor(site_url($this->dx_auth->login_uri), '登录');
				}
				
				// Load registration success page
				$html .= $this->load->view($this->dx_auth->register_success_view, $data, TRUE);
			}
			else
			{
				// Load registration page
				$html .= $this->load->view('auth/register_recaptcha_form', NULL, TRUE);
			}
		}
		elseif ( ! $this->dx_auth->allow_registration)
		{
			$data['auth_message'] = '系统禁用了注册功能。';
			$html .= $this->load->view($this->dx_auth->register_disabled_view, $data, TRUE);
		}
		else
		{
			$data['auth_message'] = '必须退出当前用户后，才能注册新用户。';
			$html .= $this->load->view($this->dx_auth->logged_in_view, $data, TRUE);
		}
		$this->_render_page($html);
	}
	
	function activate()
	{
		$html = '';
		
		// Get username and key
		$username = $this->uri->segment(3);
		$key = $this->uri->segment(4);

		// Activate user
		if ($this->dx_auth->activate($username, $key)) 
		{
			$data['auth_message'] = '你的账号已经成功激活。 '.anchor(site_url($this->dx_auth->login_uri), '登录');
			$html .= $this->load->view($this->dx_auth->activate_success_view, $data, TRUE);
		}
		else
		{
			$data['auth_message'] = '你输入的激活码不正确。 请重新检查电子邮件。';
			$html .= $this->load->view($this->dx_auth->activate_failed_view, $data, TRUE);
		}
		
		$this->_render_page($html);
	}
	
	function forgot_password()
	{
		$html = '';
		
		$val = $this->form_validation;
		
		// Set form validation rules
		$val->set_rules('login', '用户名或电子邮箱地址', 'trim|required');

		// Validate rules and call forgot password function
		if ($val->run() AND $this->dx_auth->forgot_password($val->set_value('login')))
		{
			$data['auth_message'] = '我们将发送一封邮件到你的电子邮箱，请查收，邮件会告诉你如何修改密码。';
			$html .= $this->load->view($this->dx_auth->forgot_password_success_view, $data, TRUE);
		}
		else
		{
			$html .= $this->load->view($this->dx_auth->forgot_password_view, NULL, TRUE);
		}
		
		$this->_render_page($html);
	}
	
	function reset_password()
	{
		$html = '';
		
		// Get username and key
		$username = $this->uri->segment(3);
		$key = $this->uri->segment(4);

		// Reset password
		if ($this->dx_auth->reset_password($username, $key))
		{
			$data['auth_message'] = '你已经成功地重置了密码。 '.anchor(site_url($this->dx_auth->login_uri), '登录');
			$html .= $this->load->view($this->dx_auth->reset_password_success_view, $data, TRUE);
		}
		else
		{
			$data['auth_message'] = '重置失败。 你的用户名或激活码不正确。 请重新查看邮件，按操作指南进行。';
			$html .= $this->load->view($this->dx_auth->reset_password_failed_view, $data, TRUE);
		}
		
		$this->_render_page($html);
	}
	
	function change_password()
	{
		$html = '';
		
		// Check if user logged in or not
		if ($this->dx_auth->is_logged_in())
		{			
			$val = $this->form_validation;
			
			// Set form validation
			$val->set_rules('old_password', 'Old Password', 'trim|required|min_length['.$this->min_password.']|max_length['.$this->max_password.']');
			$val->set_rules('new_password', 'New Password', 'trim|required|min_length['.$this->min_password.']|max_length['.$this->max_password.']|matches[confirm_new_password]');
			$val->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required');
			
			// Validate rules and change password
			if ($val->run() AND $this->dx_auth->change_password($val->set_value('old_password'), $val->set_value('new_password')))
			{
				$data['auth_message'] = '你的密码已经修改。';
				$html .= $this->load->view($this->dx_auth->change_password_success_view, $data, TRUE);
			}
			else
			{
				$html .= $this->load->view($this->dx_auth->change_password_view, NULL, TRUE);
			}
			
			$this->_render_page($html);
		}
		else
		{
			// Redirect to login page
			$this->dx_auth->deny_access('login');
		}
	}	
	
	function cancel_account()
	{
		$html = '';
		
		// Check if user logged in or not
		if ($this->dx_auth->is_logged_in())
		{			
			$val = $this->form_validation;
			
			// Set form validation rules
			$val->set_rules('password', 'Password', "trim|required");
			
			// Validate rules and change password
			if ($val->run() AND $this->dx_auth->cancel_account($val->set_value('password')))
			{
				// Redirect to homepage
				redirect('', 'location');
			}
			else
			{
				$html = $this->load->view($this->dx_auth->cancel_account_view, NULL ,TRUE);
			}
		}
		else
		{
			// Redirect to login page
			$this->dx_auth->deny_access('login');
		}
		$this->_render_page($html);
	}

	// Example how to get permissions you set permission in /backend/custom_permissions/
	function custom_permissions()
	{
		$html = '';
		
		if ($this->dx_auth->is_logged_in())
		{
			$html .= '我的角色: '.$this->dx_auth->get_role_name().'<br/>';
			$html .= '我的权限: <br/>';
			
			$dx_permission_keys = $this->config->item('dx_permission_keys');
			foreach ($dx_permission_keys as $pk) {
				if ($this->dx_auth->get_permission_value($pk) != NULL AND $this->dx_auth->get_permission_value($pk)) {
					$html .= $pk . ' : 允许';
				}
				else {
					$html .= $pk . ' : 禁止';
				}
				$html .= '<br/>';
			}
		}
		$this->_render_page($html);
	}

	//
	function deny()
	{
		$html = '';
		
		if ($this->dx_auth->is_logged_in())
		{
			$html .= '你无权访问此功能。';
		}
		$this->_render_page($html);
	}
}
?>