<div>
	<h1>管理用户</h1>
	<div class="row">
	<?php  				
		// Show reset password message if exist
		if (isset($reset_message))
			echo $reset_message;
		
		// Show error
		echo validation_errors();
		echo form_open($this->uri->uri_string(), array('class' => 'form-inline'));
?>
		<div class="btn-group" role="group" aria-label="...">
		<button type="submit" class="btn btn-default" name="ban">禁止用户登录</button>
		<button type="submit" class="btn btn-default" name="unban">允许用户登录</button>
		<button type="submit" class="btn btn-default" name="reset_pass">重置密码</button>
		</div>
	<hr/>
	<div class="col-xs-12">
	<table class="table table-hover">
    <thead>
    <tr>
        <th>用户名</th>
        <th>电子邮箱地址</th>
        <th>角色</th>
        <th>禁止登录</th>
        <th>上次登录IP</th>
        <th>上次登录时间</th>
        <th>创建时间</th>
		<th><?=anchor('backend/user_add', '新增用户');?></th>
	</tr>
	</thead>
	<tbody>
<?php foreach ($users as $user):?>
	<tr>
		<td><label><input type="checkbox" name="checkbox_<?=$user->id;?>" value="<?=$user->id;?>" /> <?=$user->username;?></label></td>
		<td><?=$user->email;?></td>
		<td><?=$user->role_name;?></td>
		<td><?=($user->banned == 1 ? '是' : '否');?></td>
		<td><?=$user->last_ip;?></td>
		<td><?=date('Y-m-d', strtotime($user->last_login));?></td>
		<td><?=date('Y-m-d', strtotime($user->created));?></td>
		<td><?=anchor('backend/user_edit/' . $user->id, '编辑用户');?></td>
	</tr>
<?php endforeach;?>
	</tbody>
	</table>
	</div>
<?php echo $pagination; ?>
<?php echo form_close(); ?>
	</div>
</div>