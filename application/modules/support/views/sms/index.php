<script type="text/javascript">
    var url = "<?php echo base_url();?>support/sms";
    $(function() {
        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 20,
            'columns': [
            	{ field: "order", title: 'No', sortable: false, width: 50, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "name", title: 'Name', width: 150, sortable: false,},
                { field: "client_id", title: 'ClientID', sortable: false,},
                { field: "secret", title: 'Secret', sortable: false,},
                { field: "status", title: 'Status', width: 65, sortable: false, template: function(data){
                    var html = '<label>';
                    if(data.status == 'on'){
                        var html = html + '<input OnChange="statuschange('+data.id+');" id="sms-'+data.id+'" type="checkbox" checked  disabled>';
                    }else{
                        var html = html + '<input OnChange="statuschange('+data.id+');" id="sms-'+data.id+'" type="checkbox" >';
                    }
                    var html = html + '</label>';
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
            <ul class="fieldlist" style="float:left; margin-left:0px;">
                <li></li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <a _modal="dialog_infor_detail" _title="Thêm cấu hình sms" href="<?php echo base_url();?>support/sms/detail" class="sys_modal">
                        <span class="glyphicon glyphicon-link" aria-hidden="true"></span> Thêm danh sách
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    function statuschange(id){
        console.log(id);
        // Post
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>support/sms/onoff',
            data: {
                'id':id,
            },
            success: function(data){
                console.log(data);
                onLoadGrid();
            }
        });
    }

    function onLoadGrid(){
        var _url = url;
        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function(){
    });
</script>