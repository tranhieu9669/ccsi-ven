<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico"/>
	<title>CCSI - HỆ THỐNG QUẢN LÝ CUỘC GỌI</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/bootstrap/css/bootstrap.min.css';?>"/>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/css/select2.css';?>"/>
	<style type="text/css">
		.panel-info{
			border: none;
		}

		form{
			padding-top: 50px;
		}

		.form-control, .input-group-addon, .btn{
			border-radius: 0px;
		}

		.input-group{
			width: 100%;
		}

		.select2-container .select2-choice{
		    height: 34px;
			line-height: 34px;
			border-radius: 0px;
		}

		.form-control-select{
		    width: 100%;
		}
	</style>
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
		        <div style="padding-top:30px" class="panel-body">
		        	<?php if($type == 'user'){ ?>
		        	<form id="usernameform" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST" class="form-horizontal" role="form">
		                <?php
		                if( isset($msg_error) AND $msg_error ){
		                    echo '<div id="login-alert" style="padding:8px; width:100%; text-align: center;" class="alert alert-danger col-sm-12">' . $msg_error . '</div><br>';
		                }
		                ?>
		                <div style="margin-bottom: 5px" class="input-group">
		                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
		                    <input type="text" class="form-control" name="username" id="username" placeholder="Tên đăng nhập">
		                </div>
		                <span class="error"><?php echo form_error('username'); ?></span>
		                <div style="margin-top:10px" class="form-group">
		                	<div class="col-sm-12 controls">
		                        <input type="submit" name="submit_username" id="btn-login" class="btn btn-success" value="Tiếp theo" style="width: 100%;" />
							</div>
		                </div>
		            </form>
		        	<?php }else{ ?>
		        	<form id="passwordform" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST" class="form-horizontal" role="form">
		                <?php
		                if( isset($msg_error) AND $msg_error ){
		                    echo '<div id="login-alert" style="padding:8px; width:100%; text-align: center;" class="alert alert-danger col-sm-12">' . $msg_error . '</div><br>';
		                }
		                ?>
		                <input type="hidden" name="dhid_account" id="dhid_account" value="<?php echo $id_account;?>">
		                <input type="hidden" name="hdusername" id="hdusername" value="<?php echo $username;?>">
		                <div style="margin-bottom: 10px" class="input-group">
		                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
		                    <input type="password" class="form-control" name="password" id="password" placeholder="Mật khẩu">
		                </div>
		                <span class="error"><?php echo form_error('password'); ?></span>
		                <?php
		                if(isset($center) AND !empty($center)){
		                	echo '<div style="margin-bottom: 5px" class="input-group">';
		                	echo '<select id="id_center" name="id_center" class="form-control-select" style="width: 100%">';
		                	if(isset($center) AND !empty($center)){
	                    		foreach ($center as $key => $value) {
	                    			echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
	                    		}
	                    	}
	                    	echo '</select>';
	                    	echo '</div>';
		                }
		                ?>
		                <div style="margin-top:10px" class="form-group">
		                	<div class="col-sm-12 controls">
		                        <input type="submit" name="submit_password" id="btn-login" class="btn btn-success" value="Xác nhận"  style="width: 100%;"/>
		                        <a href="<?php base_url() . 'auth';?>" class="btn btn-success" value="Xác nhận"  style="width: 100%;margin-top: 3px;">Tài khoản khác</a>
							</div>
		                </div>
		            </form>
	        		<?php }?>
		        </div>
		    </div>  
		</div>
	</div>
</body>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.12.3.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/select2.min.js"></script>
<script type="text/javascript">
	$(function(){
		$('#id_center').select2({
		    closeOnSelect: true
		});
	});
</script>
</html>