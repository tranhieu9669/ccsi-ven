
<ul class="nav nav-list">
    <li>
        <label class="tree-toggler nav-header">Khách hàng </label>
        <ul class="nav nav-list tree">
			<?php if($this->_uid == 'huynqhn' or $this->_uid == 'lamnc'){ ?>
                <li><a href="<?php echo base_url() . 'data/undata'; ?>">Lấy lại KH</a></li>
            <?php } ?>
            <li><a href="<?php echo base_url() . 'data/assign'; ?>">Assign CCSI</a></li>
			<li><a href="<?php echo base_url() . 'data/assignAuto'; ?>">Assign Auto call</a></li>
            <li><a href="<?php echo base_url() . 'data/source'; ?>">Nguồn dữ liệu</a></li>
            <li><a href="<?php echo base_url() . 'data'; ?>">Up dữ liệu</a></li>
            <li><a href="<?php echo base_url() . 'data/referen'; ?>">Reference</a></li>
            <li><a href="<?php echo base_url() . 'data/except'; ?>">Exception</a></li>
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