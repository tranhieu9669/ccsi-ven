<style type="text/css">
    .line-height-search{
        font-weight: bold;
        color: red;
    }
</style>
<script type="text/javascript">
    var url = "<?php echo base_url();?>department/work";
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
                { field: "date", title: 'Ngày', width: 150, sortable: false,},
                { field: "fullname", title: 'Họ tên', sortable: false,},
                { field: "agent_ext", title: 'Agent', width: 55, sortable: false,},
                { field: "mkt", title: 'MKT', width: 50, sortable: false,},
                { field: "pg", title: 'PG', width: 50, sortable: false,},
                { field: "number", title: 'Lead', width: 50, sortable: false,},
                { field: "status", title: 'T.Thái', width: 75, sortable: false,},
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
                <li></li>
                <li></li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <a _modal="dialog_infor_detail" _title="Khai báo nhân viên làm việc" href="<?php echo base_url() . 'department/work/detail';?>" class="sys_modal" >
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Khai báo nhân viên
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
</script>