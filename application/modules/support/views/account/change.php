<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico"/>
  <title>CCSI - ĐỔI MẬT KHẨU HỆ THỐNG</title>
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
                <div class="panel-title">ĐỔI MẬT KHẨU HỆ THỐNG</div>
            </div>

            <div style="padding-top:30px" class="panel-body">
                <form id="loginform" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST" class="form-horizontal" role="form" autocomplete="off">
                    <?php
                    if( isset($msg_error) AND $msg_error ){
                        echo '<div id="login-alert" style="padding:8px; width:100%; text-align: center;" class="alert alert-danger col-sm-12">' . $msg_error . '</div><br>';
                    }
                    ?>
                        
                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Mật khẩu cũ">
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control" name="newpass" id="newpass" placeholder="Mật khẩu mới">
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control" name="newpasscf" id="newpasscf" placeholder="Xác nhận mật khẩu">
                    </div>

                    <div style="margin-top:10px" class="form-group">
                      <div class="col-sm-12 controls">
                            <input type="submit" name="submit_action" id="btn-login" class="btn btn-success" value="Xác nhận đổi" />
              </div>
                    </div>
                </form>
            </div>                     
        </div>  
    </div>
  </div>
</body>
</html>