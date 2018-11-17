<style type="text/css">
    .line-height-search{
        font-weight: bold;
        color: red;
    }
</style>
<script type="text/javascript">
    var url = "<?php echo base_url();?>staff/call";
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
            'limit': 20,
            'columns': [
            	{ field: "order", title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "customer", title: 'Khách hàng', width: column_properties._wth_customer, sortable: false, template: '#=ColorTextSearch(customer)#'},
                { field: "status", title: 'Trạng thái', width: column_properties._wth_status, sortable: false,},
                { field: "content_call", title: 'Ghi chú', sortable: false,},
                { field: "created_at", title: 'T/G Gọi', width: column_properties._wth_time, sortable: false,},
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
                    <select id="id_department" name="id_department" class="form-control-select">
                        <option value="0" > Chọn Phòng</option>
                        <?php
                        if( isset($status_call) AND !empty($status_call) ){
                            foreach ($status_call as $key => $value) {
                                echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
                <li style="width:220px;">
                    <select id="id_group" name="id_group" class="form-control-select">
                        <option value="0" > Chọn Nhóm</option>
                        <?php
                        if( isset($status_call) AND !empty($status_call) ){
                            foreach ($status_call as $key => $value) {
                                echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
                <li style="width:220px;">
                    <select id="id_status" name="id_status" class="form-control-select">
                        <option value="0" > Trạng thái</option>
                        <?php
                        if( isset($status_call) AND !empty($status_call) ){
                            foreach ($status_call as $key => $value) {
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
        var id_status = $('#id_status').val();
        _param += '&id_status='+id_status;

        var startdate = $('#startdate').val();
        _param += '&startdate='+startdate;

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
        $("#id_department").select2({
            closeOnSelect: true
        });
        
        $("#id_group").select2({
            closeOnSelect: true
        });
        
        $("#id_agent").select2({
            closeOnSelect: true
        });
        
        $("#id_status").select2({
            closeOnSelect: true
        });

        $(".date-picker").kendoDatePicker({
            format : 'yyyy-MM-dd',
            //min : '<?php echo date('Y-m-d')?>'
        });

        $('#id_status').change(function(){
            onLoadGrid();
        });

        $('#startdate').change(function(){
            onLoadGrid();
        });

        $("#text_search").keypress(function(e){
            if(e.keyCode == 13){
                onLoadGrid();
            }
        });
    });
</script>