<style type="text/css">
    .line-height-search{
        font-weight: bold;
        color: red;
    }
</style>
<script type="text/javascript">
    var url = "<?php echo base_url();?>group/inout";
    function ColorTextSearch(inputsearch){
        var txtsearch = $("#text_search").val();
        txtsearch     = txtsearch.replace(/ /g, '');
        if(inputsearch.search(txtsearch) >= 0)
            inputsearch = inputsearch.replace(new RegExp(txtsearch,'g'), "<font class=\"line-height-search\">"+txtsearch+"</font>");
        return inputsearch;
    }
    $(function() {
        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 20,
            'columns': [
                { field: "order", title: 'No', sortable: false, width: 60, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "full_name", title: 'Tên đầy đủ', sortable: false,},
                { field: "username", title: 'Tài khoản', width: 150, sortable: false, template: '#=ColorTextSearch(username)#'},
                { field: "type", title: 'Loại', width: 80, sortable: false,},
                { field: "datetime", title: 'Thời gian', width: 175, sortable: false,},
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
                    <input type="text" id="startdate" name="startdate" class="form-control date-picker" placeholder="Ngày bắt đầu" value="<?php echo date('Y-m-d');?>">
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
        $("#startdate").kendoDatePicker({
            format : 'yyyy-MM-dd',
            change : function(){
                onLoadGrid();
            }
        });

        $("#text_search").keypress(function(e){
            if(e.keyCode == 13){
                onLoadGrid();
            }
        });
    });
</script>