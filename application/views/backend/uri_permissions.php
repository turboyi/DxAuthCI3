<div>
	<h1>管理URI权限</h1>
<?php  				
	// Build drop down menu
	foreach ($roles as $role)
	{
		$options[$role->id] = $role->name;
	}

	// Change allowed uri to string to be inserted in text area
	if ( ! empty($allowed_uris))
	{
		$allowed_uris = implode("\n", $allowed_uris);
	}
	// Build form
	echo form_open($this->uri->uri_string(), array('class' => 'form-inline'));
?>
	<div class="row">	
		<div class="form-group">
			<?php echo form_label('角色', 'role_name_label'); ?>
			<?php echo form_dropdown('role', $options, $role_id, 'class="form-control"');?>
		</div>
		<div class="form-group">
			<?php echo form_label('角色名', 'role_name_label');?>
			<?php echo form_input('role_name', '', 'class="form-control"');?>
		</div>
		<div class="form-group">
			<?php echo form_submit('show', '查看URI权限', 'class="form-control"');?> 
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-xs-6">
		<?php echo form_label('允许访问的URI:', 'allowed_uris') . '<br/>';
		echo form_textarea('allowed_uris', $allowed_uris, 'class="form-control"');
		form_submit('save', '保存URI权限', 'class="form-control"');
		?> 
		</div>
		<div class="col-xs-6">
			输入 '/' 表示 允许角色访问所有URI。<br />
			输入 '/controller/' 表示 允许角色访问'controller'和它的所有功能。<br />
			输入 '/controller/function/' 表示 允许角色只能访问'controller/function'。<br />
			只有你在控制器里使用了 check_uri_permissions() 函数，这些规则才有效果。<br />
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<?php echo form_submit('save', '保存URI权限', 'class="form-control"');?>
		</div>
	</div>
<?php echo form_close();?>
</div>
<script>
$(document).ready(function(){
	$('select[name=role]').change(function(){
		$('input[name=show]').click();
	});	
});
</script>