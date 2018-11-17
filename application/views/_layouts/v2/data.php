<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico"/>
	<title>CCSI - DATA</title>

    <!-- <link rel="stylesheet" href="<?php echo base_url();?>assets/selectcheckbox/docs/css/bootstrap-3.3.2.min.css" type="text/css"> -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/bootstrap/css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/select2.css"/>

    
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.12.3.min.js"></script>
    <!--<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-2.1.1.min.js"></script>-->
	<script type="text/javascript" src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/style.css"/>

    <!-- Kendoui -->
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url();?>assets/kendoui/styles/kendo.common.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url();?>assets/kendoui/styles/kendo.default.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url();?>assets/kendoui/styles/kendo.mobile.all.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url();?>assets/kendoui/styles.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/popup.css"/>

    <script type="text/javascript" src="<?php echo  base_url();?>assets/kendoui/js/kendo.all.min.js"></script>
    <script type="text/javascript" src="<?php echo  base_url();?>assets/kendoui/grid_view.js"></script>

    <script type="text/javascript">
        kendo.culture().calendar.firstDay = 1;

        $(function(){
            var ft_html = '<div class="panel panel-default">';
            ft_html += '<div class="panel-footer">';
            ft_html += '<p>CÔNG TY TNHH CCSI VIỆT NAM (CCSI VIET NAM CO.,LTD).</p>';
            ft_html += '<p>Địa chỉ : Số 26, ngõ 394 Đội Cấn, Phường Cống Vị, Quận Ba Đình, Hà Nội.</p>';
            ft_html += '<p>Hỗ trợ : Email-phungmanhhuong@gmail.com / Phone-0977.392.483.</p>';
            ft_html += '</div>';
            ft_html += '</div>';
            $('body').append(ft_html);
        });

        function isNumberKey(evt){
             var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 32 && (charCode < 48 || charCode > 57)||(charCode==32)){
              return false;
            }
            return true;
        }
    </script>

    <style type="text/css">
        @media (min-width: 768px){
            .modal-dialog {
                width: 100%;
                margin: 30px auto;
            }
        }

        /*.date-picker .k-input{
            height: 25px;
        }*/

        td > .k-header{
            min-height:24px;
        }

        td > .k-header > .k-state-default{
            min-height:24px;
            border-radius: 0px;
        }

        td > .k-header > .k-state-default > .k-input{
            height: 24px;
            line-height: 24px;
        }

        div.k-window{
            border: 0px;
        }

        div.k-window-content{
            padding: 0px;
        }

        .k-window-titlebar{
            border-radius: 0px;
        }

        .k-block > .k-header, .k-window-titlebar{
            padding: 2px 0px;
        }

        .error{
            /*float: left;
            position: relative;
            margin-top: -28px;
            margin-left: -5px;*/
        }

        .error > p{
            margin: 0px;
        }

        .error > p > b{
            color: red;
        }

        .panel{
            /*margin-bottom: 0px;
            overflow: hidden;*/
        }

        .panel-footer{
            padding: 3px 15px;
        }

        .panel-footer > p{
            margin: 0px;
        }

        .container-fluid{
            /*margin-top: 20px;*/
            padding-left: 0px;
            padding-right: 0px;
        }

        .input-group-addon{
            padding: 5px 12px;
        }

        .form-control{
            border-radius: 0px;
        }

        .marquee-left {
            color: blue;
        }

        .marquee-right {
            color: red;
        }

        .select2-container .select2-choice, .select2-drop.select2-drop-above, .select2-container.select2-drop-above .select2-choice{
            border-radius: 0px;
        }

        .ex_btn{
            vertical-align: top;
            height: 30px;
            line-height: 30px;
            padding: 0 8px;
        }

        /*sub-menu*/
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu>.dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -6px;
            margin-left: -1px;
            -webkit-border-radius: 0 6px 6px 6px;
            -moz-border-radius: 0 6px 6px;
            border-radius: 0 6px 6px 6px;
        }

        .dropdown-submenu:hover>.dropdown-menu {
            display: block;
        }

        .dropdown-submenu>a:after {
            display: block;
            content: " ";
            float: right;
            width: 0;
            height: 0;
            border-color: transparent;
            border-style: solid;
            border-width: 5px 0 5px 5px;
            border-left-color: #ccc;
            margin-top: 5px;
            margin-right: -10px;
        }

        .dropdown-submenu:hover>a:after {
            border-left-color: #fff;
        }

        .dropdown-submenu.pull-left {
            float: none;
        }

        .dropdown-submenu.pull-left>.dropdown-menu {
            left: -100%;
            margin-left: 10px;
            -webkit-border-radius: 6px 0 6px 6px;
            -moz-border-radius: 6px 0 6px 6px;
            border-radius: 6px 0 6px 6px;
        }

        .k-grid-header th.k-header{
            vertical-align: middle;
            text-align: center;
        }

        .k-dropdown, .k-dropdown-wrap, .k-state-default, .k-state-hover, .k-state-focused, .k-state-active, .k-state-border-down{
            border-radius: 0px;
        }

        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control{
            background: #fff;
        }

        body{
            /*width: 1200px;*/
            width: 100%;
            margin: 0 auto;
        }

        table.k-content > tbody > tr > td.k-weekend{
            background-color: red;
            border-radius: 0px;
            color: #FFF;
        }

        table.k-content > tbody > tr > td.k-weekend > a{
            color: #FFF;
        }

        #timerun{
            position: absolute;
            top: 0px;
            right: 0px;
            width: 75px;
            height: 75px;
            border: 1px solid red;
            background: burlywood;
            border-radius: 75px;
            color: #FFF;
            text-align: center;
            line-height: 73px;
            font-weight: bold;
        }

        .k-state-default > .k-select{
            z-index: 100;
        }

        .switch {
            position: relative;
            display: inline-block;
            /*width: 60px;
            height: 34px;*/
            width: 60px;
            height: 26px;
            margin-bottom: 0px;
        }

        .switch input {display:none;}

        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            /*height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;*/
            height: 22px;
            width: 22px;
            left: 0px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
          background-color: #2196F3;
        }

        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
          -webkit-transform: translateX(26px);
          -ms-transform: translateX(26px);
          transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
          border-radius: 34px;
        }

        .slider.round:before {
          border-radius: 50%;
        }

        .form-control.k-widget{
            width: 100%;
        }

        #alert_callback{
            float: right;
        }
    </style>
</head>
<body>
	<div class="navbar navbar-default" role="navigation">
        <div class="navbar-header">            
            <a class="navbar-brand" title="User Center" href="<?php echo base_url().$this->_role;?>">
                <img src="<?php echo base_url();?>assets/images/logo.png" height="50px">
            </a>
        </div>
        <div class="navbar-collapse collapse">
            <!-- Left nav -->
            <?php
            $fetch_class    = strtolower( $this->router->fetch_class() );
            $fetch_method   = $this->router->fetch_method();
            
            $home       = '';

            switch ($fetch_class) {
                default:
                    #$home       = 'active';
                    break;
            }
            ?>
            <ul class="nav navbar-nav">
                <li class="<?php echo $home;?>">
                    <a href="<?php echo base_url().$this->_role;?>">Trang chính</a>
                </li>
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

    <div class="breadcrumbs">
        <?php
        echo '<ul class="breadcrumb">';
            if(isset($breadcrumb)){
                $count = count($breadcrumb);
                $_count = 0;
                foreach ($breadcrumb as $key => $value) {
                    $_count++;
                    if($_count == $count){
                        echo '<li class="active">' . $value[0] . '</li>';
                    }else{
                        echo '<li><a href="' . $value[1] . '">' . $value[0] . '</a></li>';
                    }
                }
            }
            echo '<li id="alert_callback"></li>';
        echo '</ul>';
        ?>
    </div>
    
    <!-- End Static navbar -->
    <div id="containerid" class="container-fluid">
    <div class="col-sm-2">
        <?php
            include_once('includes/mn_data.php');
        ?>
    </div>
    <div class="col-sm-10">
        <?php
            if(isset($content) AND !empty($content)){
                $this->load->view($content);
            }
        ?>
    </div>

    <script type="text/javascript">
        $(function(){
            //init_modal('#dialog_detail', '', '100%');
            init_modal('#dialog_detail', '', '1200px');
            init_modal('#dialog_infor_detail', '', '650px');

            $("#dialog_infor_detail").data("kendoWindow").bind('open', function(){
                var uid = '<?php echo $this->_uid;?>';
                console.log(uid);
                if( uid == 'false' ){
                    window.location.href = '<?php echo base_url() . "auth";?>';
                }
            });

            $('body').bind("contextmenu", function(e) {
                e.preventDefault();
            });
        });
    </script>
</body>
</html>