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
            <li><a href="<?php echo base_url() . 'department/customer/assign'; ?>">Phân bổ Khách hàng</a></li>
			<li><a href="<?php echo base_url() . 'department/work'; ?>">Khai báo Agent(MKT-PG)</a></li>
            <li><a href="<?php echo base_url() . 'department/search'; ?>">Thông tin lịch hẹn</a></li>
			<li><a href="<?php echo base_url() . 'department/request'; ?>">Gửi yêu cầu</a></li>
        </ul>
    </li>

    <li>
        <label class="tree-toggler nav-header">Dữ liệu</label>
        <ul class="nav nav-list tree">
            <li><a href="<?php echo base_url().'department/account'?>">Tài khoản</a></li>
			<li><a href="<?php echo base_url().'department/inout'?>">In-Out hệ thống</a></li>
            <li><a href="<?php echo base_url().'department/call/appointment';?>">Danh sách lịch hẹn</a></li>
            <li><a href="<?php echo base_url().'department/record'?>">Ghi âm cuộc gọi</a></li>
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