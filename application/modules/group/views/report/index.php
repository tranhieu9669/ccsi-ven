<script type="text/javascript">
    var _columns = $.parseJSON('<?php echo $columns; ?>');
    _columns[0]['template'] = function(order){  return grid_number = grid_number + 1; };

    console.log(_columns);

    var url = "<?php echo base_url();?>group/report";
    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 20,
            columns: _columns,
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
                    <input type="text" id="startdate" name="startdate" class="form-control date-picker" placeholder="Ngày bắt đầu" value="<?php echo date('Y-m-d');?>">
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <a href="javascript:void(0);" target="_blank" class="" id="exportData">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Xuất dữ liệu
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    function onLoadGrid(){
        var _param = '?dm=1';

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
        $(".date-picker").kendoDatePicker({
            format : 'yyyy-MM-dd',
        });

        $('#startdate').change(function(){
            onLoadGrid();
        });

        $('#exportData').click(function(){
            var _param = '?dm=1';
            var startdate = $('#startdate').val();
            _param += '&startdate='+startdate;
            var _url = '<?php echo base_url()."group/report/exstatistics";?>' + _param;
            $(this).attr('href', _url);
        });
    });
</script>