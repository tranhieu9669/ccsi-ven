<script type="text/javascript">
    var url_blacklist = "<?php echo base_url();?>blacklist";
    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var grid_config = {
            'target': '#grid',
            'url': url_blacklist,
            'toolbar_template': 'toolbar_template',
            'limit': 20,
            'columns': [
            	{ field: "order"        , title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "fullname", title: 'Tên Khách hàng', width: column_properties._wth_name, sortable: false,},
                { field: "mobile", title: 'Số ĐT', width: column_properties._wth_mobile, sortable: false,},
                { field: "comment", title: 'Ghi chú', sortable: false,},
                { field: "status", title: 'T.Thái', width: column_properties._wth_status, sortable: false,},
                { field: "updated_by", title: 'Người tạo', width: column_properties._wth_time, sortable: false,},
                { field: "edit", title: 'Hủy', width: column_properties._wth_action, sortable: false, template: function(data){
                    var html = '<a _modal="dialog_infor_detail" _title="Sửa thông tin" href="<?php echo base_url();?>blacklist/detail/' + data.id + '" class="sys_modal btn btn-primary btn_edit btn-default">';
                    html = html + 'Hủy';
                    html = html + '</a>';
                    return html;
                }},
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
                    <i class="halflings-icon list"></i>
                    <span class="title-template-grid">Danh sách khóa số</span>
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                	<a _modal="dialog_infor_detail" _title="Thêm mới thông tin" href="<?php echo base_url()?>blacklist/detail" class="sys_modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true">Thêm thông tin
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>