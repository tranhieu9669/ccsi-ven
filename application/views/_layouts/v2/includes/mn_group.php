<ul class="nav nav-list">
    <li>
        <label class="tree-toggler nav-header">Khách hàng </label>
        <ul class="nav nav-list tree">
            <li><a href="<?php echo base_url() . 'group/customer/assign'; ?>">Phân bổ Khách hàng</a></li>
            <!-- <li><a href="javascript:void(0);">Lấy lại Khách hàng</a></li> -->
        </ul>
    </li>

    <li>
        <label class="tree-toggler nav-header">Dữ liệu</label>
        <ul class="nav nav-list tree">
            <li><a href="<?php echo base_url().'group/account'?>">Tài khoản</a></li>
			<li><a href="<?php echo base_url().'group/inout'?>">In-Out hệ thống</a></li>
            <li><a href="<?php echo base_url().'group/report'?>">Thống kê cuộc gọi</a></li>
            <li><a href="<?php echo base_url().'group/call/list'?>">Danh sách cuộc gọi</a></li>
            <li><a href="<?php echo base_url().'group/call/appointment'?>">Danh sách lịch hẹn</a></li>
            <li><a href="<?php echo base_url().'group/record'?>">Ghi âm cuộc gọi</a></li>
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