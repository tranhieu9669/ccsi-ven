<script type="text/javascript">
    var url_introduced = "<?php echo base_url();?>department";
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
            	{ field: "cenname", title: 'Tên Trung tâm', sortable: false,},
                { field: "depname", title: 'Tên Phòng ban', sortable: false,},
                { field: "con", title: 'Số Nv', width: column_properties._wth_num, sortable: false,},
                { field: "cagent", title: 'Số Ag', width: column_properties._wth_num, sortable: false,},
                { field: "first_ext", title: 'Ext', width: column_properties._wth_num, sortable: false,},
                /*{ field: "status", title: 'T.Thái', width: column_properties._wth_status, sortable: false,},*/
                { field: "status", title: 'T.Thái', width: column_properties._wth_status, sortable: false, template: function(data){
                    var html = '<label class="switch">';
                    if(data.status == 'on'){
                        var html = html + '<input OnChange="statuschange('+data.id+');" id="switch-'+data.id+'" type="checkbox" checked>';
                    }else{
                        var html = html + '<input OnChange="statuschange('+data.id+');" id="switch-'+data.id+'" type="checkbox" >';
                    }
                    var html = html + '<div class="slider round"></div>';
                    var html = html + '</label>';
                    return html;
                }},
                { field: "edit", title: 'Sửa', width: column_properties._wth_action, sortable: false, template: function(data){
                    var html = '<a _modal="dialog_infor_detail" _title="Sửa thông tin Phòng ban" href="<?php echo base_url();?>department/detail/' + data.id + '" class="sys_modal btn btn-primary btn_edit btn-default">';
                    html = html + 'Sửa';
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
                    <span class="title-template-grid">Danh sách Phòng ban</span>
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                	<a _modal="dialog_infor_detail" _title="Thêm mới Phòng ban" href="<?php echo base_url()?>department/detail" class="sys_modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Thêm thông tin
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    function statuschange(id){
        var status = 'off';
        if($('#switch-'+id).is(':checked')){
            status = 'on';
        }

        // Post
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>department/onoff',
            data: {'id':id, 'status':status},
            success: function(data){
                if(data !== 'SUCCESS'){
                    console.log('Cap nhat khong thanh cong');
                    if(status == 'on'){
                        $('#switch-'+id).prop('checked', false);
                    }else{
                        $('#switch-'+id).prop('checked', true);
                    }
                }else{
                    console.log('Cap nhat thanh cong');
                }
            }
        });

    }
</script>