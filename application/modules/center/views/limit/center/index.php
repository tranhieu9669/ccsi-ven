<script type="text/javascript">
    var url = "<?php echo base_url();?>center/limit";
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
				/*{ field: "edit", title: 'Sửa', width: column_properties._wth_action, sortable: false, template: function(data){
                    var html = '<a _modal="dialog_infor_detail" _title="Sửa thông tin ca" href="<?php echo base_url();?>center/limit/edit/' + data.id + '" class="sys_modal btn btn-primary btn_edit btn-default">';
                    html = html + 'Sửa';
                    html = html + '</a>';
                    return html;
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
                <li>
                    <input type="text" id="startdate" name="startdate" class="form-control date-picker" placeholder="Ngày bắt đầu" value="<?php echo date('Y-m-d');?>">
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                	<a _modal="dialog_infor_detail" _title="Thêm thông tin ca" href="<?php echo base_url()?>center/limit/add" class="sys_modal">
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
        var id_center = $('#id_center').val();
        _param += '&id_center='+id_center;

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
        $("#id_center").select2({
            closeOnSelect: true
        });

        $(".date-picker").kendoDatePicker({
            format : 'yyyy-MM-dd',
            min : '<?php echo date('Y-m-d');?>',
        });

        $('#id_center').change(function(){
            gridLoad();
        });

        $('#startdate').change(function(){
            gridLoad();
        });

        $('#enddate').change(function(){
            gridLoad();
        });
    });
</script>