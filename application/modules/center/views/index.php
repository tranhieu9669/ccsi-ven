<style type="text/css">
    .line-height-search{
        font-weight: bold;
        color: red;
    }
</style>
<script type="text/javascript">
    var url = "<?php echo base_url();?>center";
    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 1000,
            'columns': [
                { field: "order", title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "fullname", title: 'FullName', sortable: false,},
                { field: "mobile", title: 'Mobile', sortable: false, width: column_properties._wth_mobile,},
                { field: "ext", title: 'Ext', sortable: false, width: column_properties._wth_ext,},
                {
                    title: "Call Out",
                    columns: [ 
                    	{ field: "total_out", title: 'Total', sortable: false, width: column_properties._wth_total,},
                        { field: "total_ans", title: 'Ans', sortable: false, width: column_properties._wth_total,},
                		{ field: "bill_out", title: 'Bill', sortable: false, width: column_properties._wth_bill,},
                    ]
                },
                {
                    title: "Call In",
                    columns: [ 
                		{ field: "total_in", title: 'Total', sortable: false, width: column_properties._wth_total,},
                		{ field: "bill_in", title: 'Bill', sortable: false, width: column_properties._wth_bill,},
                	]
                },
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
                    <input type="text" id="startdate" name="startdate" class="form-control date-picker" placeholder="Ngày bắt đầu" value="<?php echo date('Y-m-d');?>">
                </li>
                <li>
                    <input type="text" id="enddate" name="enddate" class="form-control date-picker" placeholder="Ngày bắt đầu" value="<?php echo date('Y-m-d');?>">
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

        var enddate = $('#enddate').val();
        _param += '&enddate='+enddate;

        var _url = url + _param;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function(){
        $("#startdate").kendoDatePicker({
            format : 'yyyy-MM-dd',
            change : function(){
                onLoadGrid();
            }
        });

        $("#enddate").kendoDatePicker({
            format : 'yyyy-MM-dd',
            change : function(){
                onLoadGrid();
            }
        });

        $('#exportData').click(function(){
            var _param = '?dm=1';
	        var startdate = $('#startdate').val();
	        _param += '&startdate='+startdate;
	        var enddate = $('#enddate').val();
	        _param += '&enddate='+enddate;

            var _url = '<?php echo base_url()."center/excalltime";?>' + _param;
            $(this).attr('href', _url);
        });
    });
</script>