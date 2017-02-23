<?php
$labelClass = array('class' => 'col-sm-1 control-label text-right');
$inputClass = array('class' => 'form-control');
?>
<div>
	<h1>管理角色</h1>
	<div class="row">
	<?php
		// Show error
		echo validation_errors();
		
		// Build drop down menu
		$options[0] = 'None';
		foreach ($roles as $role)
		{
			$options[$role->id] = $role->name;
		}
	
		// Build form
		echo form_open($this->uri->uri_string(), array('class' => 'form-inline'));
?>
		<div class="form-group">
			<?php echo form_label('父角色', 'role_parent_label'); ?>
			<?php echo form_dropdown('role_parent', $options, '0', 'class="form-control"');?>
		</div>
		<div class="form-group">
			<?php echo form_label('角色名', 'role_name_label');?>
			<?php echo form_input('role_name', '', 'class="form-control"');?>
		</div>
		<div class="form-group">
			<?php echo form_submit('add', '增加角色', 'class="form-control"');?> 
			<?php echo form_submit('delete', '删除所选角色', 'class="form-control"');?>
		</div>
	</div>
	<hr />
	<div class="col-xs-6">
	<table class="table table-hover">
    <thead>
    <tr>
        <th>ID</th>
        <th>角色名</th>
        <th>父角色ID</th>
	</tr>
	</thead>
	<tbody>
<?php foreach ($roles as $role):?>
	<tr>
		<td><label><input type="checkbox" name="checkbox_<?=$role->id;?>" value="<?=$role->id;?>" /> <?=$role->id;?></label></td>
		<td><?=$role->name;?></td>
		<td><?=$role->parent_id;?></td>
	</tr>
<?php endforeach;?>
	</tbody>
	</table>
	</div>
<?php echo form_close(); ?>
</div>