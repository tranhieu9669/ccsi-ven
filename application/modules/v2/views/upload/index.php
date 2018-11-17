<script type="text/javascript">
    var url_introduced = "<?php echo base_url();?>fileup";
    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var grid_config = {
            'target': '#grid',
            'url': url_introduced,
            'toolbar_template': 'toolbar_template',
            'limit': 20,
            'columns': [
            	{ field: "order"        , title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
            	{ field: "title", title: 'Tiêu đề dữ liệu', sortable: false,},
                { field: "source_name", title: 'Nguồn', width: column_properties._wth_source, sortable: false,},
                { field: "num_cus", title: 'Số KH', width: column_properties._wth_num_cus, sortable: false,},
                { field: "num_dup", title: 'Số DU', width: column_properties._wth_num_dup, sortable: false,},
                { field: "updated_at", title: 'Thời gian up', width: column_properties._wth_up_time, sortable: false,},
                { field: "updated_by", title: 'Người up', width: column_properties._wth_up_by, sortable: false,},
                { field: "status", title: 'T.Thái', width: column_properties._wth_status, sortable: false,},
                /*{ field: "extract", title: 'Extract', width: column_properties._wth_status, sortable: false, template: function(data){
                    if( data.status == 'new' ){
                        var html = '<a _modal="dialog_infor_detail" _title="Sửa thông tin tài khoản" href="<?php echo base_url();?>extract/' + data.id + '" class="sys_modal btn btn-primary btn_edit btn-default">';
                        html = html + 'Extract';
                        html = html + '</a>';
                        return html;
                    }
                }},*/
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
                    <a href="<?php echo base_url()?>customertmpl"><span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> File dữ liệu</a>
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                	<a _modal="dialog_infor_detail" _title="Up dữ liệu khách hàng" href="<?php echo base_url()?>fileup/detail" class="sys_modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Thêm thông tin
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>