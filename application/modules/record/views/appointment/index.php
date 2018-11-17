<style type="text/css">
    .line-height-search{
        font-weight: bold;
        color: red;
    }
</style>
<script type="text/javascript">
    var url = "<?php echo base_url();?>record/appointment";
    function ColorTextSearch(inputsearch){
        var txtsearch = $("#text_search").val();
        txtsearch     = txtsearch.replace(/ /g, '');
        if(inputsearch.search(txtsearch) >= 0)
            inputsearch = inputsearch.replace(new RegExp(txtsearch,'g'), "<font class=\"line-height-search\">"+txtsearch+"</font>");
        return inputsearch;
    }
    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 50,
            'columns': [
            	{ field: "order"        , title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "cus_first_name", title: 'Họ tên đệm', width: column_properties._wth_first_name, sortable: false,},
                { field: "cus_last_name", title: 'Tên', width: column_properties._wth_last_name, sortable: false,},
                { field: "cus_mobile", title: 'Điện thoại', width: column_properties._wth_mobile+10, sortable: false, template: '#=ColorTextSearch(cus_mobile)#'},
                { field: "app_datetime", title: 'Giờ hẹn', width: column_properties._wth_time, sortable: false,},
                { field: "name", title: 'Trung tâm', sortable: false,},
                { field: "sms_status", title: 'SMS', width: column_properties._wth_status, sortable: false,},
                { field: "app_status", title: 'Loại', width: column_properties._wth_status, sortable: false,},
		        { field: "app_created_at", title: 'T/G Tạo', width: column_properties._wth_time, sortable: false,},
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
                    <input id="text_search" class="k-header-input-search" placeholder="Điện thoại hoặc tên" type="search" style="line-height: 26px;">
                </li>
                <li style="width:220px;">
                    <select id="id_center" name="id_center" class="form-control-select">
                        <option value="0" > Trung tâm spa</option>
                        <?php
                        if( isset($centerspa) AND !empty($centerspa) ){
                            foreach ($centerspa as $key => $value) {
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
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    function onLoadGrid(){
        var _param = '?dm=1';

        var startdate = $('#startdate').val();
        _param += '&startdate='+startdate;

        var inputsearch = $('#text_search').val();
        inputsearch = inputsearch.replace(/ /g, '');
        _param += '&inputsearch='+inputsearch;

        var id_center = $('#id_center').val();
        _param += '&id_center='+id_center;

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
        });

        $('#startdate').change(function(){
            onLoadGrid();
        });

        $("#text_search").keypress(function(e){
            if(e.keyCode == 13){
                onLoadGrid();
            }
        });

        $('#id_center').change(function(){
            onLoadGrid();
        });
    });
</script>