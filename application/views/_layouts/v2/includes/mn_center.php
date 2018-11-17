<style type="text/css">
    .un-active{
        /*display: none;*/
    }

    .gr-active{
        /*display: block;*/
    }
</style>

<ul class="nav nav-list">
    <li>
        <label class="tree-toggler nav-header">Khách hàng </label>
        <ul class="nav nav-list tree">
            <li><a href="<?php echo base_url().'center/customer/assign'; ?>">Phân bổ Khách hàng</a></li>
        </ul>
    </li>

    <li>
        <label class="tree-toggler nav-header">Quản lý Thông tin</label>
        <ul class="nav nav-list tree">
            <li><a href="<?php echo base_url() . 'center/frametime'; ?>">Danh sách ca</a></li>
            <li><a href="<?php echo base_url() . 'center/limit'; ?>">Giới hạn Trung tâm</a></li>
            <li><a href="<?php echo base_url() . 'center/limit/department'; ?>">Giới hạn Phòng</a></li>
            <li><a href="<?php echo base_url() . 'center/approved'; ?>">Danh sách yêu cầu</a></li>
            <li><a href="<?php echo base_url() . 'center/config'; ?>">Cấu hình tham số</a></li>
        </ul>
    </li>

    <li>
        <label class="tree-toggler nav-header">Báo cáo</label>
        <ul class="nav nav-list tree">
            <li><a href="javascript:void(0);">Tài khoản</a></li>
            <li><a href="<?php echo base_url() . 'center/call/appointment'; ?>">Danh sách lịch hẹn</a></li>
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
