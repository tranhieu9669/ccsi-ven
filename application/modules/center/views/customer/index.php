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

<div class="col-sm-12">
    <div id="grid"></div>
</div>

<script type="text/javascript">
    var url = "<?php echo base_url();?>center/customer";
    function ColorTextSearch(fullname){
        var txtsearch = $("#text_search").val();
        txtsearch     = txtsearch.replace(/ /g, '');

        if(fullname.search(txtsearch) >= 0)
            fullname = fullname.replace(new RegExp(txtsearch,'g'), "<font class=\"line-height-search\">"+txtsearch+"</font>");
        return fullname;
    }

    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 15,
            'columns': [
                { field: "order" , title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "fullname", title: 'Fullname', sortable: false, },
                { field: "mobile", title: 'Mobile', sortable: false, width: column_properties._wth_mobile, template: '#=ColorTextSearch(mobile)#'},
                { field: "source", title: 'Source', width: column_properties._wth_source, sortable: false,},
                { field: "callval", title: 'Val', width: column_properties._wth_val, sortable: false,},
                { field: "end_ext", title: 'Ext', width: column_properties._wth_ext, sortable: false,},
                { field: "status", title: 'Status', width: column_properties._wth_status, sortable: false,},
                { field: "start_date", title: 'Start', width: column_properties._wth_date, sortable: false,},
                { field: "close_date", title: 'Close', width: column_properties._wth_date, sortable: false,},
                { field: "edit", title: '#', width: 65, sortable: false, template: function(data){
                    var html = '<a _modal="dialog_infor_detail" _title="Chuyển thông tin khác hàng" href="<?php echo base_url();?>center/customer/transaction/'+data.id+'/'+data.id_agent+'" class="sys_modal btn btn-primary btn_edit btn-default">';
                    html = html + ' ==> ';
                    html = html + '</a>';
                    return html;
                }},
            ]
        };
        var grid = create_grid(grid_config);
    });
</script>

<script id="toolbar_template" type="text/x-kendo-template">
    <div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <input id="text_search" class="k-header-input-search" placeholder="Số điện thoại" value="" type="search">
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    function onLoadGrid(){
        var inputsearch = $("#text_search").val();
        inputsearch     = inputsearch.replace(/ /g, '');

        var _url = url;
        _url = _url + '?inputsearch='+inputsearch;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }
    // Thay doi thong tin load du lieu
    $(function(){
        $("#text_search").keypress(function(e){
            if(e.keyCode == 13){
                onLoadGrid();
            }
        });
    });
</script>