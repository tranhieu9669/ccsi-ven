<style type="text/css">
	.mnactive{
		background: #CCC;
	}
</style>
<?php
	$fetch_class    = strtolower( $this->router->fetch_class() );
    $fetch_method   = $this->router->fetch_method();

    $cl_call1 = ''; $cl_call2 = ''; $cl_call3 = ''; $cl_call4 = ''; $cl_call5 = '';

    if(strtolower($fetch_class) == 'call'){
    	switch (strtolower($fetch_method)) {
    		case 'appointment':
    			$cl_call2 = 'mnactive';
    			break;

			case 'nostatus':
    			$cl_call3 = 'mnactive';
    			break;

            case 'callback':
                $cl_call5 = 'mnactive';
                break;
    		
    		default:
    			$cl_call1 = 'mnactive';
    			break;
    	}
    }elseif(strtolower($fetch_class) == 'staff'){
        switch (strtolower($fetch_method)) {
            case 'listcall':
                $cl_call4 = 'mnactive';
                break;

            default:
                break;
        }
    }
?>

<ul class="nav nav-list">
    <li style="background: #FFCCAA;"><a href="<?php echo base_url().'staff/startcase'; ?>"">Bắt đầu gọi</a></li>
    <li class="<?php echo $cl_call1;?>"><a href="<?php echo base_url().'staff/call';?>">Danh sách cuộc gọi</a></li>
    <li class="<?php echo $cl_call5;?>"><a href="<?php echo base_url().'staff/call/callback';?>">Danh sách gọi lại</a></li>
    <li class="<?php echo $cl_call4;?>"><a href="<?php echo base_url().'staff/list';?>">Danh sách chưa gọi</a></li>
    <!-- <li class="<?php echo $cl_call5;?>"><a href="<?php echo base_url().'staff/call/introduce';?>">Danh sách giới thiệu</a></li> -->
    <li class="<?php echo $cl_call3;?>"><a href="<?php echo base_url() . 'staff/call/nostatus';?>">Danh sách quyên lưu</a></li>
    <li class="<?php echo $cl_call2;?>"><a href="<?php echo base_url() . 'staff/call/appointment';?>">Danh sách lịch hẹn</a></li>
    <li class="<?php echo $cl_call2;?>"><a href="<?php echo base_url() . 'staff/call/misscall';?>">Danh sách gọi nhỡ</a></li>
</ul>

<script type="text/javascript">
    $(document).ready(function () {
        $('label.tree-toggler').click(function () {
            $(this).parent().children('ul.tree').toggle(300);
        });
    });
</script>