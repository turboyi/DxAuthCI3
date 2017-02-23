<?php
$username = array(
	'class' => 'form-control',
	'name'	=> 'username',
	'id'	=> 'username',
	'size'	=> 30,
	'value' => set_value('username')
);

$password = array(
	'class' => 'form-control',
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30
);

$remember = array(
	'name'	=> 'remember',
	'id'	=> 'remember',
	'value'	=> 1,
	'checked'	=> set_value('remember'),
	'style' => 'margin:0;padding:0'
);

$confirmation_code = array(
	'class' => 'form-control',
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8
);

$labelClass = array('class' => 'col-sm-2 control-label');
$inputClass = array('class' => 'form-control');
?>

<div style="width:67%; margin:0 auto;">
<h3>登录</h3>
<hr />
<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal'))?>

<?php echo $this->dx_auth->get_auth_error(); ?>


<div class="form-group">
	<?php echo form_label('用户名', $username['id'], $labelClass);?>
	<div class="col-sm-3"><?php echo form_input($username)?></div>
	<div class="col-sm-7"><?php echo form_error($username['name']); ?></div>
</div>	
<div class="form-group">
	<?php echo form_label('密码', $password['id'], $labelClass);?>
	<div class="col-sm-3"><?php echo form_password($password)?></div>
	<div class="col-sm-7"><?php echo form_error($password['name']); ?></div>
</div>	

<?php if ($show_captcha): ?>
<div class="form-group">
	<?php echo form_label('验证码', $confirmation_code['id'], $labelClass);?>
	<div class="col-sm-10"><?php echo $this->dx_auth->get_captcha_image(); ?> 请阅读左侧的验证码，并在下面原样输入。</div>
	<!-- <div class="col-sm-7">请阅读左侧的验证码，并在下面原样输入。</div> -->
</div>	
<div class="form-group">
	<?php echo form_label('输入验证码', $confirmation_code['id'], $labelClass);?>
	<div class="col-sm-3"><?php echo form_input($confirmation_code);?></div>
	<div class="col-sm-7"><?php echo form_error($confirmation_code['name']); ?></div>
</div>	
<?php endif; ?>

<div class="form-group">
	<div class="col-sm-2"></div>
	<div class="col-sm-10">
		<?php echo form_label(form_checkbox($remember) . ' 记住我', $remember['id']);?>
		<?php echo anchor($this->dx_auth->forgot_password_uri, '忘记密码');?> 
		<?php
			if ($this->dx_auth->allow_registration) {
				echo anchor($this->dx_auth->register_uri, '开始注册');
			};
		?>
	</div>
</div>	
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-2"><?php echo form_submit('login','登录', 'class="form-control btn btn-default"');?></div>
</div>	

<?php echo form_close()?>
</div>
