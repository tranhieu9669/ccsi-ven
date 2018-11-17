<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DEAURA - VOIP - MONITOR</title>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/bootstrap/css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/style.css"/>
    <!-- Custom Theme Style -->
    <link href="<?php echo base_url();?>assets/build/css/custom.min.css" rel="stylesheet">

    <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.12.3.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
    <style type="text/css">
      .registered{
        background: #006666;
        color: #FFFFFF;
      }

      .icon{

      }

      .icon-ring{
        background-image: url("./assets/images/icon_ring.gif");
      }

      .icon-connected{
        background-image: url("./assets/images/icon_connected.gif");
        z-index: 99999999;
      }

      .tile-stats .icon, .icon-ring, .icon-connected{
        right: 5px;
        top: 5px;
        width: 16px;
        height: 16px;
        color: #BAB8B8;
        position: absolute;
        /*z-index: 1;*/
      }

      .tile-stats .count, .tile-stats h3, .tile-stats p{
        margin: 0 0 0 2px;
      }

      .tile-stats .count{
        font-size: 22px;
        line-height: 24px;
      }

      .tile-stats h3{
        font-size: 11px;
        color: #FFFFFF;
      }

      .tile-stats p{
        font-size: 8px;
      }

      .nav-md .container.body .right_col, .main_container .top_nav, footer{
        margin-left:0px;
      }

      .site-title{
        line-height: 56px;
        margin-left: 25px;
      }

      .navbar{
        border-radius: 0px;
      }

      .navbar-brand{
        padding: 0px;
      }

      .nav > li > a {
          padding: 10px 15px;
      }

      .navbar-collapse{
        padding-right: 0px;
      }
    </style>
  </head>
  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <!-- top navigation -->
        <div class="navbar navbar-default" role="navigation">
          <div class="navbar-header">            
              <a class="navbar-brand" title="User Center" href="<?php echo base_url().$this->_role;?>">
                  <img src="<?php echo base_url();?>assets/images/logo.png" height="50px">
              </a>
          </div>
          <div class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                  <!-- <li class="<?php echo $home;?>">
                      <a href="<?php echo base_url().$this->_role;?>">Trang chính</a>
                  </li> -->
              </ul>
              <!-- Right nav --> 
              <ul class="nav navbar-nav navbar-right">
                  <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><i class="fa fa-user"></i> <?php echo $this->_agent_lname . '-' . $this->_ext . '(' . $this->_role . ')';?><span class="caret"></span></a>
                      <ul class="dropdown-menu">
                          <li><a _modal="dialog_infor_detail" _title="Thông tin tài khoản" href="<?php echo base_url() . 'account/info'?>" class="sys_modal">Thông tin Tài khoản</a></li>
                          <li><a _modal="dialog_infor_detail" _title="Thay đổi mật khẩu" href="<?php echo base_url() . 'account/changepass';?>">Đổi mật khẩu</a></li>
                          <li><a href="<?php echo base_url() . 'logout'?>">Thoát hệ thống</a></li>
                      </ul>
                  </li>
              </ul>

            </div><!--/.nav-collapse -->
        </div>
        <!-- /top navigation -->
        <!-- page content -->
        <div class="right_col" role="main">
          <?php
            if(isset($content) AND !empty($content)){
                $this->load->view($content);
            }
          ?>
        </div>
        <!-- /page content -->
        <!-- footer content -->
        <div class="panel panel-default" style="border-radius: 0px; margin-bottom:0px;">
          <div class="panel-footer">
            <p>CÔNG TY TNHH CCSI VIỆT NAM (CCSI VIET NAM CO.,LTD).</p>
            <p>Địa chỉ : Số 26, ngõ 394 Đội Cấn, Phường Cống Vị, Quận Ba Đình, Hà Nội.</p>
            <p>Hỗ trợ : Email-phungmanhhuong@gmail.com / Phone-0977.392.483.</p>
          </div>
        </div>
        <!-- /footer content -->
      </div>
    </div>
  </body>
</html>