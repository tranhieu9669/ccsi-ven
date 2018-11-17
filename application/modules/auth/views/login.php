<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico"/>
	<title>CCSI - HỆ THỐNG QUẢN LÝ CUỘC GỌI</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/bootstrap/css/bootstrap.min.css';?>"/>
</head>
<body>
	<?php 
		ini_set('display_errors', 'Off');
		ini_set('error_reporting', E_ALL);
	?>
	<div style="margin-left: 25%; width: 265px; margin-top: 25px;">
		<img src="<?php echo base_url() . 'assets/images/logo.png';?>" style="width: 265px;">
	</div>
	<div id="container">
		<div id="loginbox" style="margin-top:30px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
		    <div class="panel panel-info">
		        <div class="panel-heading">
		            <div class="panel-title">ĐĂNG NHẬP HỆ THỐNG CALL</div>
		        </div>

		        <div style="padding-top:30px" class="panel-body">
		            <form id="loginform" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST" class="form-horizontal" role="form">
		                <?php
		                if( isset($msg_error) AND $msg_error ){
		                    echo '<div id="login-alert" style="padding:8px; width:100%; text-align: center;" class="alert alert-danger col-sm-12">' . $msg_error . '</div><br>';
		                }
		                ?>

		                <div style="margin-bottom: 25px" class="input-group">
		                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
		                    <input type="text" class="form-control" name="username" id="username" placeholder="Tên đăng nhập">
		                    <span class="error"><?php echo form_error('username'); ?></span>
		                </div>
		                    
		                <div style="margin-bottom: 25px" class="input-group">
		                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
		                    <input type="password" class="form-control" name="password" id="password" placeholder="Mật khẩu">
		                    <span class="error"><?php echo form_error('password'); ?></span>
		                </div>

		                <div class="input-group">
		                  	<div class="checkbox">
		                        <label>
		                          	<input type="checkbox" name="remember" value="1"> Remember me
		                        </label>
		                  	</div>
		                </div>

		                <div style="margin-top:10px" class="form-group">
		                	<div class="col-sm-12 controls">
		                        <input type="submit" name="submit_action" id="btn-login" class="btn btn-success" value="Đăng nhập hệ thống" />
							</div>
		                </div>
		            </form>
		        </div>                     
		    </div>  
		</div>
	</div>
</body>
</html>