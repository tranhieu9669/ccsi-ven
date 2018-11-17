<ul class="nav nav-list">
    <li>
        <label class="tree-toggler nav-header">Khách hàng </label>
        <ul class="nav nav-list tree">
            <li><a href="<?php echo base_url() . 'admin/source'; ?>">Nguồn dữ liệu</a></li>
            <!--<li><a href="<?php echo base_url() . 'admin/upload'; ?>">Up Khách hàng</a></li>-->
        </ul>
    </li>

    <li>
        <label class="tree-toggler nav-header">Quản lý Thông tin</label>
        <ul class="nav nav-list tree">
            <li><a href="<?php echo base_url() . 'admin/center'; ?>">Trung tâm</a></li>
            <li><a href="<?php echo base_url() . 'admin/department'; ?>">Phòng ban</a></li>
            <li><a href="<?php echo base_url() . 'admin/group'; ?>">Nhóm</a></li>
            <li><a href="<?php echo base_url() . 'admin/account'; ?>">Tài khoản</a></li>
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