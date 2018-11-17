<style type="text/css">
    .un-active{
        /*display: none;*/
    }

    .gr-active{
        /*display: block;*/
    }
</style>
<?php
    $cl_group_call = isset($group_call) ? 'gr-active' : 'un-active';
    $cl_group_data = isset($group_data) ? 'gr-active' : 'un-active';
    $cl_group_cus  = isset($group_cus) ? 'gr-active' : 'un-active';
    $cl_group_mn   = isset($group_mn) ? 'gr-active' : 'un-active';
    $cl_group_rp   = isset($group_rp) ? 'gr-active' : 'un-active';

    $it_startcase  = '';
?>
<ul class="nav nav-list">
    <?php if( in_array($this->_role, array('staff')) ){#'supadmin',  ?>
    <li style="background: #FFCCAA;">
        <a href="<?php echo base_url() . 'startcase'; ?>" style="padding-left: 3px;">Bắt đầu gọi</a>
    </li>
    <?php } ?>

    <?php if( in_array($this->_role, array('supadmin', 'admin', 'center', 'department')) ){ ?>
    <li>
        <label class="tree-toggler nav-header">Khách hàng </label>
        <ul class="nav nav-list tree <?php echo $cl_group_cus;?>">
            <li><a href="<?php echo base_url() . 'source'; ?>">Nguồn dữ liệu</a></li>
            <li><a href="<?php echo base_url() . 'fileup'; ?>">Up Khách hàng</a></li>
            <?php if( in_array($this->_role, array('supadmin', 'admin', 'center')) ){ ?>
            <li><a href="<?php echo base_url() . 'customer/assign'; ?>">Phân bổ Khách hàng</a></li>
            <li><a href="<?php echo base_url() . 'customer/unassign'; ?>">Lấy lại Khách hàng</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>

    <?php if( in_array($this->_role, array('supadmin', 'admin', 'center')) ){ ?>
    <li>
        <label class="tree-toggler nav-header">Quản lý Thông tin</label>
        <ul class="nav nav-list tree <?php echo $cl_group_mn;?>">
            <li><a href="<?php echo base_url() . 'account'; ?>">Tài khoản</a></li>
            <li><a href="<?php echo base_url() . 'center'; ?>">Trung tâm</a></li>
            <li><a href="<?php echo base_url() . 'department'; ?>">Phòng ban</a></li>
            <li><a href="<?php echo base_url() . 'group'; ?>">Nhóm</a></li>
            <li><a href="<?php echo base_url() . 'frametime'; ?>">Danh sách ca</a></li>
            <li><a href="<?php echo base_url() . 'limit/center'; ?>">Giới hạn Trung tâm</a></li>
            <li><a href="<?php echo base_url() . 'limit/department'; ?>">Giới hạn Phòng</a></li>
            <?php if(in_array($this->_role, array('department'))){ ?>
            <li><a href="<?php echo base_url() . 'limit/transaction'; ?>">Trao đổi lịch hẹn</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>

    <li>
        <label class="tree-toggler nav-header">Báo cáo</label>
        <ul class="nav nav-list tree <?php echo $cl_group_rp;?>">
            <li><a href="<?php echo base_url() . 'listcall'; ?>">Danh sách cuộc gọi</a></li>
            <!-- <li><a href="<?php echo base_url() . 'listcallno'; ?>">Danh sách không lưu log</a></li> -->
            <!-- <li><a href="<?php echo base_url() . 'listcallback'; ?>">Danh sách gọi lại</a></li> -->
            <li><a href="<?php echo base_url() . 'listappointment'; ?>">Danh sách lịch hẹn</a></li>
        </ul>
    </li>
</ul>

<script type="text/javascript">
    $(document).ready(function () {
        $('label.tree-toggler').click(function () {
            $(this).parent().children('ul.tree').toggle(300);
        });
    });
</script>