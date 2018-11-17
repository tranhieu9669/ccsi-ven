<script type="text/javascript">
    var url = "<?php echo base_url();?>center/limit/department";
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
                { field: "date", title: 'Ngày', sortable: false, width: column_properties._wth_date},
                { field: "name", title: 'Ca', sortable: false,}, // width: column_properties._wth_name
                { field: "limited", title: 'Giới hạn', sortable: false, width: column_properties._wth_limited},
                { field: "appointment", title: 'Số lịch', sortable: false, width: column_properties._wth_appointment},
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
                    <select id="id_center_call" name="id_center_call" class="form-control-select">
                        <option value="0" > TRUNG TÂM CALL</option>
                        <?php
                        if( isset($center_call) AND !empty($center_call) ){
                            foreach ($center_call as $key => $value) {
                                echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
                <li style="width:220px;">
                    <select id="id_department" name="id_department" class="form-control-select">
                        <option value="0" > CHỌN PHÒNG</option>
                    </select>
                </li>
                <li style="width:220px;">
                    <select id="id_center_spa" name="id_center_spa" class="form-control-select">
                        <option value="0" > TRUNG TÂM SPA</option>
                        <?php
                        if( isset($center_spa) AND !empty($center_spa) ){
                            foreach ($center_spa as $key => $value) {
                                echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
                <li>
                    <input type="text" id="startdate" name="startdate" class="form-control date-picker" placeholder="Ngày bắt đầu" value="<?php echo date('Y-m-d');?>">
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                	<a _modal="dialog_infor_detail" _title="Thêm thông tin ca" href="<?php echo base_url()?>center/limit/department/add" class="sys_modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Thêm thông tin
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    function gridLoad(){
        var _param = '?dm=1';

        var id_center_call = $('#id_center_call').val();
        _param += '&id_center_call='+id_center_call;

        var id_department = $('#id_department').val();
        _param += '&id_department='+id_department;

        var id_center_spa = $('#id_center_spa').val();
        _param += '&id_center_spa='+id_center_spa;

        var startdate = $('#startdate').val();
        _param += '&startdate='+startdate;

        var _url = url + _param;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function(){
        $("#id_center_call").select2({
            closeOnSelect: true
        });

        $("#id_department").select2({
            closeOnSelect: true
        });

        $("#id_center_spa").select2({
            closeOnSelect: true
        });

        $(".date-picker").kendoDatePicker({
            format : 'yyyy-MM-dd',
            min : '<?php echo date('Y-m-d')?>'
        });

        $('#id_center_call').change(function(){
            gridLoad();
        });

        $('#id_center_spa').change(function(){
            gridLoad();
        });

        $('#startdate').change(function(){
            gridLoad();
        });

        $('#enddate').change(function(){
            gridLoad();
        });

        $('#id_center_call').change(function(){
            var id_center = $(this).val();
            $.ajax({
              url     : '<?php echo base_url();?>center/ajax/debyce',
              type    : "GET",
              data    : {'id_center':id_center},
              success : function( data ) {
                var obj_decode = $.parseJSON(data);
                var district = obj_decode;
                  
                var html_district = '<option value="" selected="selected"> CHỌN PHÒNG</option>';
                for(var key in district){
                  var detail  = district[key];
                  var id      = detail['id'];
                  var name    = detail['name'];
                  html_district += '<option value="' + id + '">' + name + '</option>';
                }
                $('#id_department').html(html_district);
                $("#id_department").select2("val", "");
              },

              error   : function(msg){
                var html_district = '<option value="" selected="selected"> CHỌN PHÒNG</option>';
                $('#id_department').html(html_district);
                $("#id_department").select2("val", "");
              }
            });
        });
    });
</script>