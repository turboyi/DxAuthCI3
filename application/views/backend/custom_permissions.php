<div>
	<h1>管理自定义权限</h1>
	<div class="row">
		<b>此处为一个例子，用来解释自定义权限</b>
<?php
		// Build drop down menu
		foreach ($roles as $role)
		{
			$options[$role->id] = $role->name;
		}

		// Change allowed uri to string to be inserted in text area
		if ( ! empty($allowed_uri))
		{
			$allowed_uri = implode("\n", $allowed_uri);
		}
		
		if (empty($edit))
		{
			$edit = FALSE;
		}
			
		if (empty($delete))
		{
			$delete = FALSE;
		}
		
		// Build form
		echo form_open($this->uri->uri_string(), array('class' => 'form-inline'));
?>
		<div class="form-group">
			<?php echo form_label('角色', 'role_name_label'); ?>
			<?php echo form_dropdown('role', $options, $role_id, 'class="form-control"');?>
		</div>
		<div class="form-group">
			<?php echo form_submit('show', '查看URI权限', 'class="form-control"');?> 
		</div>
		<hr />
	<div class="row">
		<div class="col-xs-6">
		<?php echo form_label('自定义权限:', 'uri_label');?>
		<ul>
<?php foreach ($dx_permission_keys as $pk=>$pv):?>
			<li><?php echo form_label(form_checkbox($pk, '1', $pv) . '允许' . $pk, '');?></li>
<?php endforeach;?>
		</ul>
<?php echo form_submit('save', '保存权限', 'class="form-control"');?>
		</div>
		<div class="col-xs-6">
<?php
		echo '打开 '.anchor('auth/custom_permissions/').' 查看结果， 修改过权限的用户重新登录重新登陆后才能使结果生效。<br/>';
		echo '如果你修改了你自己的角色， 必须重新登陆才能使结果生效。';
?>
		</div>
<?php echo form_close();?>
	</div>
</div>
<script>
$(document).ready(function(){
	$('select[name=role]').change(function(){
		$('input[name=show]').click();
	});	
});
</script>