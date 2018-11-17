<script type="text/javascript">
    var url_introduced = "<?php echo base_url();?>account";
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
            	{ field: "cenname", title: 'Trun tâm', sortable: false,},
                { field: "depname", title: 'Tên phòng', sortable: false,},
                { field: "groname", title: 'Tên nhóm', sortable: false,},
                { field: "full_name", title: 'Tên đầy đủ', sortable: false,},
                { field: "username", title: 'Tài khoản', width: column_properties._wth_username, sortable: false,},
                { field: "password", title: 'MK Ext', width: 100, sortable: false,},
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
                    var html = '<a _modal="dialog_infor_detail" _title="Sửa thông tin tài khoản" href="<?php echo base_url();?>account/detail/' + data.id + '" class="sys_modal btn btn-primary btn_edit btn-default">';
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
                    <a href="<?php echo base_url()?>accounttmpl"><span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> File dữ liệu</a>
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <a _modal="dialog_infor_detail" _title="Thêm thông tin Tài khoản" href="<?php echo base_url()?>account/detail" class="sys_modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Thêm tài khoản
                    </a>
                    <?php
                    if( in_array($this->_role, array('supadmin', 'admin', 'center')) ){
                        echo '<a _modal="dialog_infor_detail" _title="Thêm danh sách Tài khoản" href="'.base_url().'account/createlist" class="sys_modal"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> Thêm danh sách</a>';
                    }
                    ?>
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
            url: '<?php echo base_url();?>account/onoff',
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