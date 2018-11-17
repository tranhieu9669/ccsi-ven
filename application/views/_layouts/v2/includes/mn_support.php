<ul class="nav nav-list">
    <li>
        <label class="tree-toggler nav-header">Khách hàng </label>
        <ul class="nav nav-list tree">
            <li><a href="<?php echo base_url() . 'data/source'; ?>">Nguồn dữ liệu</a></li>
            <li><a href="<?php echo base_url() . 'data'; ?>">Up dữ liệu</a></li>
            <li><a href="<?php echo base_url() . 'data/customer'; ?>">Khách hàng</a></li>
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