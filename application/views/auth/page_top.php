<?php
$uri = $this->uri;

?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
    <title><?=$this->config->item('site_name')?>管理</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/lib/bootstrap/css/bootstrap-datetimepicker.min.css">
    <!-- JavaScript -->
    <script type="text/javascript" src="/lib/jquery.min.js"></script>
    <script type="text/javascript" src="/lib/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/lib/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="/lib/bootstrap/js/bootstrap-datetimepicker.zh-CN.js"></script>
    <style>
        div#header img {max-height: 22px;}
        div#header div#logo img {height: 32px; max-height: 32px;}
        div#content {margin: 0 auto; padding: 80px 0 0;}
        
        i.error {color:red;}
        @media print {
            div#content {margin: 0px auto; padding: 0 0 0;}
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-dxauth-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">
            <img alt="Brand" src="/lib/logo.png" width="24">
        </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-dxauth-navbar-collapse-1">
        <ul class="nav navbar-nav">
<?php if ($this->dx_auth->is_logged_in()):?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">模块导航 <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><?php echo anchor('backend/', '管理首页', 'target="_blank"');?></li>
                    <li role="separator" class="divider"></li>
<?php foreach ($this->config->item('apps_info') as $k=>$v):?>
                    <li><?php echo anchor($k . '/', $v, 'target="_blank"');?></li>
<?php endforeach;?>
                </ul>
            </li>
            <li role="separator" class="divider"></li>
<?php   if ($this->dx_auth->is_admin()):?>
            <li <?php echo $uri->segment(2)=='users'?' class="active"':'';?>>
                <?php echo anchor('backend/users', '用户管理');?>
            </li>
            <li <?php echo $uri->segment(2)=='roles'?' class="active"':'';?>>
                <?php echo anchor('backend/roles', '角色管理');?>
            </li>
            <li <?php echo $uri->segment(2)=='uri_permissions'?' class="active"':'';?>>
                <?php echo anchor('backend/uri_permissions', 'URI权限管理');?>
            </li>
            <li <?php echo $uri->segment(2)=='custom_permissions'?' class="active"':'';?>>
                <?php echo anchor('backend/custom_permissions', '自定义权限管理');?>
            </li>
<?php   endif;?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li <?php echo ($uri->segment(1)=='auth' && $uri->segment(2)=='custom_permissions')?' class="active"':'';?>>
                <?php echo anchor('auth/custom_permissions', '当前权限');?>
            </li>
            <li><?php echo anchor('auth/change_password', '修改密码', 'target="_blank"');?></li>
            <li><?php echo anchor('auth/logout', '退出系统');?></li>
<?php endif;?>
        </ul>
        </div>
    </div>
    </nav>
    <div id="content">
        <div class="container">
            <div class="row-fluid">
