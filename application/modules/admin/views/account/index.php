<script type="text/javascript">
    var url = "<?php echo base_url();?>admin/account";
    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 20,
            'columns': [
            	{ field: "order"        , title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "full_name", title: 'Tên đầy đủ', sortable: false,},
                { field: "username", title: 'Tài khoản', width: column_properties._wth_username, sortable: false,},
                { field: "email", title: 'Email', width: column_properties._wth_email, sortable: false,},
                { field: "ext", title: 'Ext', width: 65, sortable: false,},
                { field: "mobile", title: 'Đ.Thoại', width: column_properties._wth_mobile, sortable: false,},
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
                    var html = '<a _modal="dialog_infor_detail" _title="Sửa thông tin tài khoản" href="<?php echo base_url();?>admin/account/edit/' + data.id + '" class="sys_modal btn btn-primary btn_edit btn-default">';
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
            <ul class="fieldlist" style="float:left; margin-left:0px;">
                <li>
                    <input id="text_search" class="k-header-input-search" placeholder="Điện thoại hoặc tên" type="search" style="line-height: 26px;">
                </li>
                <li style="width:200px;">
                    <select id="_id_center" name="_id_center" class="form-control-select">
                        <option value="0" > Trung tâm</option>
                        <?php
                        if( isset($center) AND !empty($center) ){
                            foreach ($center as $key => $value) {
                                echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
                <li style="width:200px;">
                    <select id="_id_department" name="_id_department" class="form-control-select">
                        <option value="0" > Phòng ban</option>
                        <?php
                        if( isset($department) AND !empty($department) ){
                            foreach ($department as $key => $value) {
                                echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <a href="<?php echo base_url()?>admin/account/active" style="margin-right: 5px;">
                        <span class="glyphicon glyphicon-share" aria-hidden="true"></span> Kích hoạt
                    </a>

                    <a href="<?php echo base_url()?>admin/account/file" style="margin-right: 5px;">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> File dữ liệu
                    </a>

                    <a _modal="dialog_infor_detail" _title="Thêm thông tin Tài khoản" href="<?php echo base_url()?>admin/account/add" class="sys_modal" style="margin-right: 5px;">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Thêm tài khoản
                    </a>

                    <a _modal="dialog_infor_detail" _title="Thêm danh sách Tài khoản" href="<?php echo base_url();?>admin/account/addlist" class="sys_modal">
                        <span class="glyphicon glyphicon-link" aria-hidden="true"></span> Thêm danh sách
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
            url: '<?php echo base_url();?>admin/account/onoff',
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

    function onLoadGrid(){
        var _param = '?dm=1';
        var id_center = $('#_id_center').val();
        _param += '&id_center='+id_center;

        var id_department = $('#_id_department').val();
        _param += '&id_department='+id_department;

        var inputsearch = $('#text_search').val();
        inputsearch = inputsearch.replace(/ /g, '');
        _param += '&inputsearch='+inputsearch;

        var _url = url + _param;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function(){
        $("#_id_center").select2({
            closeOnSelect: true
        });

        $("#_id_center").change(function(){
            var id_center = $(this).val();
            $.ajax({
              url     : '<?php echo base_url();?>admin/ajax/debyce',
              type    : "GET",
              data    : {'id_center':id_center},
              success : function( data ) {
                var obj_decode = $.parseJSON(data);
                var department = obj_decode;

                var html_department = '<option value="0" selected="selected"> Phòng ban</option>';
                for(var key in department){
                  var detail  = department[key];
                  var id      = detail['id'];
                  var name    = detail['name'];
                  html_department += '<option value="' + id + '">' + name + '</option>';
                }
                $('#_id_department').html(html_department);
                $("#_id_department").select2("val", "0");
              },

              error   : function(msg){
                var html_department = '<option value="0" selected="selected"> Phòng ban</option>';
                $('#_id_department').html(html_department);
                $("#_id_department").select2("val", "0");
              }
            });

            onLoadGrid();
        });

        $("#_id_department").select2({
            closeOnSelect: true
        });

        $("#_id_department").change(function(){
            onLoadGrid();
        });

        $("#text_search").keypress(function(e){
            if(e.keyCode == 13){
                onLoadGrid();
            }
        });
    });
</script>