<style type="text/css">
    .form-control{
        padding: 0px 0px 0px 3px;
    }

    .dialog-control{
        padding-left: 3px;
        padding-right: 3px;
    }

    .form-horizontal .form-group{
        margin-left: 0px;
        margin-right: 0px;
    }

    .form-horizontal .control-label{
        padding-right: 8px;
        /*margin-left: -10px;*/
        padding-top: 3px;
    }

    .form-horizontal{
        padding: 5px 0px;
        padding-bottom: 0px;
    }

    input[type="checkbox"], input[type="radio"]{
        margin: 2px 20px 0px 5px;
    }

    span.k-datepicker{
        width: 100%;
    }

    .form-group{
        margin-bottom: 5px;
    }

    .btn-group > .multiselect {
        width: 100%;
        text-align: left;
        /*height: 30px;*/
        padding: 5px 8px;
    }

    .btn .caret{
        margin-top: -10px;
        float: right;
    }

    .multiselect-clear-filter{
        padding: 4px 8px;
    }

    .input-group-addon{
        border-radius: 0px;
    }

    .k-header{
        height: 30px;
    }

    .k-header-input-search{
        border-radius: 0px;
        border: 1px solid rgb(204, 204, 204);
        /*margin-top: 1px;*/
        padding: 0px 5px;
        height: 32px;
    }

    .line-height-search{
        font-weight: bold;
        color: red;
    }
</style>

<script type="text/javascript">
    var url = "<?php echo base_url();?>limitdepartment/<?php echo $id_center_limit;?>";
    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 20,
            'columns': [
            	{ field: "order" , title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
            	{ field: "depname", title: 'Phòng', sortable: false,},
                { field: "date", title: 'Ngày',  width: column_properties._wth_date, sortable: false,},
                { field: "franame", title: 'Ca', width: column_properties._wth_franame, sortable: false,},
                { field: "start", title: 'B.Đầu', width: column_properties._wth_time, sortable: false,},
                { field: "end", title: 'K.Thúc', width: column_properties._wth_time, sortable: false,},
                { field: "limit", title: 'G.Hạn', width: column_properties._wth_limit, sortable: false,},
                { field: "schedule", title: 'S.Hẹn', width: column_properties._wth_limit, sortable: false,},
                { field: "status", title: 'T.Thái', width: column_properties._wth_status, sortable: false,},
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
                <li style="width:220px;">
                    <select id="id_center" name="id_center" class="form-control-select">
                        <option value="0" > Chọn Trung tâm</option>
                        <?php
                        if( isset($center) AND !empty($center) ){
                            foreach ($center as $key => $value) {
                                echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
                <li style="width:220px;">
                    <select id="id_department" name="id_department" class="form-control-select">
                        <option value="0" > Chọn Phòng</option>
                        ?>
                    </select>
                </li>
                <li>
                    <input type="text" id="startdate" name="startdate" class="form-control date-picker" placeholder="Ngày bắt đầu">
                </li>
                <li>
                    <input type="text" id="enddate" name="enddate" class="form-control date-picker" placeholder="Ngày kết thúc">
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                	<a _modal="dialog_infor_detail" _title="Thêm mới giới hạn Phòng" href="<?php echo base_url()?>limitdepartment/detail/<?php echo $id_center_limit;?>" class="sys_modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Thêm thông tin
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    $(function(){
        $("#id_center").select2({
            closeOnSelect: true
        });

        $("#id_department").select2({
            closeOnSelect: true
        });

        $(".date-picker").kendoDatePicker({
            format  : 'yyyy-MM-dd',
        });

        $('#id_center').change(function(){
            var id_center = $(this).val();
            $.ajax({
                url     : '<?php echo base_url();?>ajax/departmentbycenter',
                type    : "GET",
                data    : {'id_center':id_center},
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var department = obj_decode;
                  
                    var html_department = '<option value="" selected="selected"> Chọn phòng</option>';
                    for(var key in department){
                        var detail  = department[key];
                        var id      = detail['id'];
                        var name    = detail['name'];
                        html_department += '<option value="' + id + '">' + name + '</option>';
                    }
                    $('#id_department').html(html_department);
                    $("#id_department").select2("val", "");
                },

                error   : function(msg){
                var html_department = '<option value="" selected="selected"> Chọn phòng</option>';
                    $('#id_department').html(html_department);
                    $("#id_department").select2("val", "");
                }
            });

            gridLoad();
        });

        $('#id_department').change(function(){
            gridLoad();
        });

        $('#startdate').change(function(){
            gridLoad();
        });

        $('#enddate').change(function(){
            gridLoad();
        });
    });

    function statuschange(id){
        var status = 'off';
        if($('#switch-'+id).is(':checked')){
            status = 'on';
        }

        // Post
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>limitcenter/onoff',
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

    function gridLoad(){
        var param_url = '?l=1';
        var id_center   = $('#id_center').val();
        if(id_center != '' && parseInt(id_center) > 0){
            param_url += '&id_center='+id_center;
        }

        var id_department = $('#id_department').val();
        if(id_department != '' && parseInt(id_department) > 0){
            param_url += '&id_department='+id_department;
        }

        var startdate   = $('#startdate').val();
        if(startdate != ''){
            param_url += '&startdate='+startdate;
        }

        var enddate     = $('#enddate').val();
        if(enddate != ''){
            param_url += '&enddate='+enddate;
        }

        var _url = url + param_url;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function(){
        $('#id_center').change(function(){
            var id_center = $(this).val();

            $.ajax({
              url     : '<?php echo base_url();?>ajax/departmentbycenter',
              type    : "GET",
              data    : {'id_center':id_center},
              success : function( data ) {
                var obj_decode = $.parseJSON(data);
                var department = obj_decode;
                  
                var html_department = '<option value="" selected="selected"> Chọn Phòng ban</option>';
                for(var key in department){
                  var detail  = department[key];
                  var id      = detail['id'];
                  var name    = detail['name'];
                  html_department += '<option value="' + id + '">' + name + '</option>';
                }
                $('#id_department').html(html_department);
                $("#id_department").select2("val", "");
              },

              error   : function(msg){
                var html_department = '<option value="" selected="selected"> Chọn phòng</option>';
                $('#id_department').html(html_department);
                $("#id_department").select2("val", "");
              }
            });
        });

        $('.btnSearch').click(function(){
            gridLoad();
        });
    });
</script>