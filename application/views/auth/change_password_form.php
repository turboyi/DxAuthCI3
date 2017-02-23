<?php
$old_password = array(
	'class' => 'form-control',
	'name'	=> 'old_password',
	'id'		=> 'old_password',
	'size' 	=> 30,
	'value' => set_value('old_password')
);

$new_password = array(
	'class' => 'form-control',
	'name'	=> 'new_password',
	'id'		=> 'new_password',
	'size'	=> 30
);

$confirm_new_password = array(
	'class' => 'form-control',
	'name'	=> 'confirm_new_password',
	'id'		=> 'confirm_new_password',
	'size' 	=> 30
);

$labelClass = array('class' => 'col-sm-2 control-label');
?>

<div>
<h3>修改密码</h3>
<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal')); ?>

<?php echo $this->dx_auth->get_auth_error(); ?>

<div class="form-group">
	<?php echo form_label('旧密码', $old_password['id'], $labelClass); ?>
	<div class="col-sm-3"><?php echo form_password($old_password); ?></div>
	<div class="col-sm-6"><?php echo form_error($old_password['name']); ?></div>
</div>
<div class="form-group">
	<?php echo form_label('新密码', $new_password['id'], $labelClass); ?>
	<div class="col-sm-3"><?php echo form_password($new_password); ?></div>
	<div class="col-sm-6"><?php echo form_error($new_password['name']); ?></div>
</div>
<div class="form-group">
	<?php echo form_label('确认新密码', $confirm_new_password['id'], $labelClass); ?>
	<div class="col-sm-3"><?php echo form_password($confirm_new_password); ?></div>
	<div class="col-sm-6"><?php echo form_error($confirm_new_password['name']); ?></div>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-3"><?php echo form_submit('change', '修改密码', 'class="btn btn-success btn-block"'); ?></div>
</div>

<?php echo form_close(); ?>
</fieldset>