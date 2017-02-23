<?php
// Build drop down menu
foreach ($roles as $role)
{
    $options[$role->id] = $role->name;
}
?>
<?php if ($this->session->flashdata('message') != ''):?>
<div class="alert alert-success" role="alert" id="form-message">
	<p><?=$this->session->flashdata('message');?></p>
</div>
<?php endif;?>

<h3 class="demo-panel-title">
	<?=anchor('backend/user_add', '新增用户');?>
	<?=$user->id!=0 ? (' | ' . anchor('backend/user_edit', '编辑用户信息')) : '';?>
</h3>
<hr />
<?php
echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal'));
echo form_hidden('id', $user->id);
?>
<div class="form-group">
	<label class="control-label col-sm-2" for="username">用户名</label>
	<div class="col-sm-3"><input class="form-control" type="text" name="username" id="username" value="<?php echo set_value('username', $user->username);?>" /></div>
	<div class="col-sm-7"><i class="error"><?php echo form_error('username'); ?></i></div>
</div>
<div class="form-group">
	<label class="control-label col-sm-2" for="email">用户邮箱</label>
	<div class="col-sm-3"><input class="form-control" type="text" name="email" id="email" value="<?php echo set_value('email', $user->email);?>" /></div>
	<div class="col-sm-7"><i class="error"><?php echo form_error('email'); ?></i></div>
</div>
<div class="form-group">
	<label class="control-label col-sm-2" for="role_id">角色</label>
	<div class="col-sm-3"><?php echo form_dropdown('role_id', $options, set_value('role_id', $user->role_id), ' class="form-control"');?></div>
	<div class="col-sm-7"><i class="error"><?php echo form_error('role_id'); ?></i></div>
</div>
<div class="form-group">
	<label class="control-label col-sm-2" for="home">用户入口URI</label>
	<div class="col-sm-3"><input class="form-control" type="text" name="home" id="home" value="<?php echo set_value('home', $user->home);?>" /></div>
	<div class="col-sm-7"><i class="error"><?php echo form_error('home'); ?></i></div>
</div>
<div class="form-group">
	<label class="control-label col-sm-2" for="password">新密码</label>
	<div class="col-sm-3"><input class="form-control" type="password" name="password" id="password" value="" /></div>
	<div class="col-sm-7"><i class="error"><?php echo form_error('password'); ?></i></div>
</div>
<div class="form-group">
	<label class="control-label col-sm-2" for="password2">再次输入新密码</label>
	<div class="col-sm-3"><input class="form-control" type="password" name="password2" id="password2" value="" /></div>
	<div class="col-sm-7"><i class="error" id="password2error"><?php echo form_error('password2'); ?></i></div>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-2">
		<?php echo form_submit('', '保存', 'class="btn btn-success btn-block"'); ?>
	</div>
	<div class="col-sm-2">
		<?php echo form_reset('', '重填', 'class="btn btn-default"'); ?>
	</div>
</div>
<?php echo form_close()?>
<script>
$(document).ready(function(){
	$(':password').change(function(){
		var pass1 = $('input[name=password]').val();
		var pass2 = $('input[name=password2]').val();
		if (pass1 != pass2) {
			$(':submit').prop('disabled', 'disabled');
			$('#password2error').html('两次的密码不一致！');
		} else {
			$(':submit').removeProp('disabled');
			if (pass1 == '') {
				$('#password2error').html('密码不修改！')
			} else {
				$('#password2error').html('');
			}
		}
	});
});
</script>
