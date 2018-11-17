<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico"/>
	<title>CCSI - HỆ THỐNG QUẢN LÝ CUỘC GỌI</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/bootstrap/css/bootstrap.min.css';?>"/>

	<style type="text/css">
	  	.alert{
	    	padding: 8px;
	    	margin-bottom: 0px;
	    	border-radius: 0px
	  	}

	  	.form-control{
	    	padding: 0px 0px 0px 3px;
	  	}

	  	.dialog-control{
	    	padding-left: 0px;
	    	padding-right: 0px;
	  	}

	  	.form-horizontal .form-group{
	    	margin-left: 0px;
	    	margin-right: 0px;
	  	}

	  	.form-horizontal .control-label{
	    	padding-right: 8px;
	    	margin-left: -10px;
	  	}

	  	.question-box{
	    	border: 1px solid #ccc;
	    	min-height: 100px;
	    	max-height: 175px;
	    	overflow-y: scroll;
	  	}

	  	.checkbox{
	    	float: left;
	    	margin-right: 20px;
	  	}

	  	.question-box > .checkbox{
	    	float: left;
	    	clear: both;
	    	margin-left: 2px;
	  	}

	  	form#formSubmit{
	    	padding: 15px 10px;  
	  	}

	  	input[type="checkbox"], input[type="radio"]{
	    	margin: 2px 20px 0px 5px;
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
		        <div class="panel-heading">
		            <div class="panel-title">THAY ĐỔI MẬT KHẨU HỆ THỐNG</div>
		        </div>

		        <div style="padding-top:30px" class="panel-body">
		            <form id="loginform" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST" class="form-horizontal" role="form" autocomplete="off">
		                <?php
		                if( isset($msg) AND $msg ){
		                	echo '<div class="form-group">';
		                		echo '<div class="col-sm-offset-3 col-sm-9">';
		                        	echo '<div id="login-alert" style="width:100%; text-align: center;" class="alert alert-danger col-sm-12">' . $msg . '</div><br>';
								echo '</div>';
		                	echo '</div>';
		                }
		                ?>

		                <div class="form-group">
		                    <label class="control-label col-sm-3 dialog-control" for="pwd">Tên đăng nhập </label>
  							<div class="col-sm-9 dialog-control">
		                    	<input type="text" class="form-control" name="username" id="username" placeholder="Tên đăng nhập">
		                    	<span class="error"><?php echo form_error('username'); ?></span>
	                    	</div>
		                </div>
		                    
		                <div class="form-group">
		                    <label class="control-label col-sm-3 dialog-control" for="pwd">Mật khẩu cũ </label>
  							<div class="col-sm-9 dialog-control">
		                    	<input type="password" class="form-control" name="password" id="password" placeholder="Mật khẩu cũ">
		                    	<span class="error"><?php echo form_error('password'); ?></span>
	                    	</div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-sm-3 dialog-control" for="pwd">Mật khẩu mới </label>
  							<div class="col-sm-9 dialog-control">
		                    	<input type="password" class="form-control" name="newpass" id="newpass" placeholder="Mật khẩu mới">
		                    	<span class="error"><?php echo form_error('newpass'); ?></span>
	                    	</div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-sm-3 dialog-control" for="pwd">Xác nhận mật khẩu </label>
  							<div class="col-sm-9 dialog-control">
		                    	<input type="password" class="form-control" name="newpasscf" id="newpasscf" placeholder="Xác nhận mật khẩu">
		                    	<span class="error"><?php echo form_error('newpasscf'); ?></span>
	                    	</div>
		                </div>

		                <div class="form-group">
		                	<div class="col-sm-offset-3 col-sm-9">
		                        <input type="submit" name="submit_action" id="btn-login" class="btn btn-success" value="Thay đổi mật khẩu" />
							</div>
		                </div>
		            </form>
		        </div>                     
		    </div>  
		</div>
	</div>
</body>
</html>