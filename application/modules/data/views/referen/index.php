<script type="text/javascript">
    var url_introduced = "<?php echo base_url();?>data/referen";
    $(function() {
        var grid_config = {
            'target': '#grid',
            'url': url_introduced,
            'toolbar_template': 'toolbar_template',
            'limit': 20,
            'columns': [
            	{ field: "order"        , title: 'No', sortable: false, width: 50, template: function(order){
                    return grid_number = grid_number + 1;
                }},
            	{ field: "fullname", title: 'Tên khách hàng', sortable: false,},
                { field: "mobile", title: 'Số KH', width: 120, sortable: false,},
                { field: "employeeCode", title: 'Mã NV', width: 120, sortable: false,},
                { field: "employeeInput", title: 'T/G Cập nhật', width: 180, sortable: false,}
            ]
        };
        var grid = create_grid(grid_config);
    });
</script>

<div id="grid"></div>

<script id="toolbar_template" type="text/x-kendo-template">
	<div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <a href="<?php echo base_url()?>admin/upload/file"><span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> File dữ liệu</a>
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                	<a _modal="dialog_infor_detail" _title="Up dữ liệu khách hàng" href="<?php echo base_url()?>data/referen/fileup" class="sys_modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Thêm thông tin
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>