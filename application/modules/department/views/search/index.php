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

<div class="form-horizontal">
    <div class="form-group">
        <div class="col-sm-6 dialog-control">
            <input type="text" id="txt_search" name="txt_search" class="form-control" placeholder="Điện thoại Or CMTND" value="" style="width: 200px;float: left;margin-right: 5px;">
            <button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 30px;">Tìm kiếm</button>
        </div>

        <div class="col-sm-6 dialog-control">
            <button type="button" class="btn btn-primary" style="height: 30px;" value="5">Về lễ tân</button>
            <button type="button" class="btn btn-primary" style="height: 30px;" value="6">Về CLS</button>
            <button type="button" class="btn btn-primary" style="height: 30px;" value="9">Về Soi da</button>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <div id="gridccsi"></div>
</div>
<script id="toolbar_template_ccsi" type="text/x-kendo-template">
    <div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
                <li> Thông tin CCSI</li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li></li>
            </ul>
        </span>
    </div>
</script>

<div class="col-sm-12" style="margin-top: 10px;">
    <div id="gridcrm"></div>
</div>
<script id="toolbar_template_crm" type="text/x-kendo-template">
    <div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
                <li> Thông tin CRM</li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li></li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    var url_crm = "<?php echo base_url();?>department/search/loadcrm";
    var url_ccsi = "<?php echo base_url();?>department/search/loadccsi";

    function searchLoad(){
        var txtSearch = $('#txt_search').val();
        //CCSI
        var _url_ccsi = url_ccsi + "?txtsearch=" + txtSearch;
        var _grid_ccsi = $("#gridccsi").data("kendoGrid");
        _grid_ccsi.dataSource.options.url = _url_ccsi;
        _grid_ccsi.dataSource.read();
        _grid_ccsi.refresh();
        //CRM
        var _url_crm = url_crm + "?txtsearch=" + txtSearch;
        var _grid_crm = $("#gridcrm").data("kendoGrid");
        _grid_crm.dataSource.options.url = _url_crm;
        _grid_crm.dataSource.read();
        _grid_crm.refresh();
        return false;
    }

    $(function() {
        var grid_config_crm = {
            'target': '#gridcrm',
            'url': url_crm,
            'toolbar_template': 'toolbar_template_crm',
            'limit': 25,
            'columns': [
                { field: "order", title: 'No', sortable: false, width: 50, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "nv_Hoten_vn", title: 'Họ tên', sortable: false,},
                { field: "i_SoCMT", title: 'CMTND', sortable: false, width: 120},
                { field: "d_Ngayden", title: 'TG Đầu', sortable: false, width: 150, type:"date", template:'#= kendo.toString(d_Ngayden, "dd-MM-yyyy h:mm:ss")#'},
                { field: "d_Giotrangthai", title: 'TG Cuối', sortable: false, width: 150, type:"date", template:'#= kendo.toString(d_Giotrangthai, "dd-MM-yyyy h:mm:ss")#'},
                { field: "TL", title: 'Tele', sortable: false, width: 200},
                { field: "BC", title: 'BC', sortable: false, width: 65, template: function(data){
                    if(data.BC != null){
                        return 'Có';
                    }else{
                        return 'Không';
                    }
                }},
                { field: "CS", title: 'CS', sortable: false, width: 65, template: function(data){
                    if(data.CS != null){
                        return 'Có';
                    }else{
                        return 'Không';
                    }
                }}
            ]
        };
        var grid_crm = create_grid(grid_config_crm);

        var grid_config_ccsi = {
            'target': '#gridccsi',
            'url': url_ccsi,
            'toolbar_template': 'toolbar_template_ccsi',
            'limit': 25,
            'columns': [
                { field: "order", title: 'No', sortable: false, width: 50, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "nv_Hoten_vn", title: 'Họ tên', sortable: false, template: function(data){
                    return data.cus_first_name + ' ' + data.cus_last_name;
                }},
                { field: "app_datetime", title: 'TG hẹn', sortable: false, width: 150, type:"date", template:'#= kendo.toString(app_datetime, "dd-MM-yyyy h:mm:ss")#'},
                { field: "app_created_at", title: 'TG tạo', sortable: false, width: 150, type:"date", template:'#= kendo.toString(app_created_at, "dd-MM-yyyy h:mm:ss")#'},
                { field: "app_status", title: 'TT Lịch', sortable: false, width: 120},
                { field: "agent_ext", title: 'Ext', sortable: false, width: 120},
                { field: "crm_app_content", title: 'Trạng thái', sortable: false, width: 180},
            ]
        };
        var grid_ccsi = create_grid(grid_config_ccsi);

        $('button[type="submit"]').click(function(){
            searchLoad();
        });

        $('button[type="button"]').click(function(){
            var crm_app_status = $(this).val();
            var txtSearch = $('#txt_search').val();
            
            $.ajax({
                url     : "<?php echo base_url();?>department/search/updateapp",
                type    : "GET",
                data    : {app_status: crm_app_status, txtsearch: txtSearch},
                success : function(response, status, jqXHR){
                    searchLoad();
                },
                error   : function(jqXHR, status, err){
                },
                complete: function(jqXHR, status){
                }
            });
        });
    });
</script>