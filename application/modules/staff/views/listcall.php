<style type="text/css">
    .line-height-search{
        font-weight: bold;
        color: red;
    }
</style>
<script type="text/javascript">
    var url = "<?php echo base_url();?>staff/list?startdate=<?php echo $_SESSION['startdate'];?>";
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
                { field: "fullname", title: 'Hộ tên Khách hàng', sortable: false,},
                { field: "mobile", title: 'Số ĐT', width: column_properties._wth_mobile, sortable: false, template: '#=ColorTextSearch(mobile)#'},
                { field: "gender", title: 'G/Tính', width: column_properties._wth_gender, sortable: false,},
                { field: "start_date", title: 'start_date', width: column_properties._wth_date, sortable: false,},
                { field: "call", title: '#', width: column_properties._wth_action, sortable: false,template: function(data){
                    var html = '<a href="<?php echo base_url();?>staff/newcall/' + data.id + '" class="btn btn-primary btn_edit btn-default">';
                    html = html + '<span class="glyphicon glyphicon-headphones" aria-hidden="true"></span>';
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
                    <input id="text_search" class="k-header-input-search" placeholder="Điện thoại hoặc tên" type="search" style="line-height: 26px;">
                </li>
                <li>
                    <input type="text" id="startdate" name="startdate" class="form-control date-picker" placeholder="Ngày Assign" value="<?php echo $_SESSION['startdate'];?>">
                </li>
                <li>
                    <button type="button" class="btn btn-primary btnSearch" style="height: 24px;">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Tất cả
                    </button>
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

        var _url = url + _param;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function(){
        $("#id_status").select2({
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

        $('.btnSearch').click(function(){
            $('#startdate').val('');
            $("#text_search").val('');
            onLoadGrid();
        });
    });
</script>