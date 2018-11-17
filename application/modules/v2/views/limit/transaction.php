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

    .k-grid tbody .k-button, .k-ie8 .k-grid tbody button.k-button{
        min-width: 33px;
    }
    .k-button-icontext .k-icon, .k-button-icontext .k-image{
        margin: 0px;
    }
</style>

<div id="grid"></div>
<script id="toolbar_template" type="text/x-kendo-template">
    <div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
                
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <a _modal="dialog_infor_detail" _title="Đăng ký trao đổi lịch" href="<?php echo base_url()?>limit/transaction/add" class="sys_modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Thêm thông tin
                    </a>
                </li>
            </ul>
        </span>
    </div>
</script>
<script type="text/javascript">
    var url = "<?php echo base_url();?>limit/transaction";

    function gridLoad(){
        var param_url = '?l=1';
        var id_center_call = $('#id_center_call').val();
        if(id_center_call != '' && parseInt(id_center_call) > 0){
            param_url += '&id_center_call='+id_center_call;
        }

        var _url = url + param_url;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var dataSource = new kendo.data.DataSource({
            url : url,
            transport: {
                read:function (options){
                    $.ajax({
                        type : "GET",
                        data : options.data,
                        url  : this.dataSource.options.url,
                        success: function(result) {
                            var objresult =  eval('(' + result + ')');
                            options.success( objresult );
                        },
                    });
                    cache: true
                },
                update: function (options){
                    var appointment = options.data.models[0]['appointment'];
                    var limited = options.data.models[0]['limited'];

                    if( parseInt(limited) < parseInt(appointment) ){
                        alert('Giới hạn không nhỏ hơn số lịch đã có');
                        options.success();
                    }else{
                        $.ajax({
                            type : "POST",
                            data : options.data.models[0],
                            url  : url + "/update",
                            //dataType: "jsonp",
                            success: function(result) {
                                alert('Cập nhật dữ liệu thành công');
                                options.success();
                            },
                        });
                    }
                },
                parameterMap: function(options, operation) {
                    if (operation !== "read" && options.models) {
                        return {models: kendo.stringify(options.models)};
                    }
                }
            },
            batch: true,
            pageSize: 25,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true, },
                        cenamecall: { type: "string", editable: false, },
                        cenamespa: { type: "string", editable: false, },
                        frname: { type: "string", editable: false, },
                        dename: { type: "string", editable: false, },
                        date: { type: "string", editable: false, },
                        limited: { type: "number", validation: { required: true, min: 0, max:50} },
                        appointment: { type: "number", editable: false, },
                    }
                },
                data: function(data) {
                    return data.data;
                },
                total :function(data) {
                    return data.total;
                },
            }
        });

        $("#grid").kendoGrid({
            dataSource: dataSource,
            navigatable: true,
            pageable: true,
            //height: 550,
            //toolbar: ["create"],
            toolbar: kendo.template($("#toolbar_template").html()),
            columns: [
                { field: "cename", title: 'Trung tâm', sortable: false, },
                { field: "cename", title: 'Ngày', sortable: false, width: 180 },
                { field: "frname", title: "Ca", sortable: false, width: column_properties._wth_frame, },
                { field: "defname", title: "Phòng 1", sortable: false, width: column_properties._wth_dep, },
                { field: "detname", title: 'Phòng 2', sortable: false, width: column_properties._wth_dep, },
                { field: "val_from", title: "Giá trị 1", sortable: false, width: column_properties._wth_val, },
                { field: "val_to", title: "Giá trị 2", sortable: false, width: column_properties._wth_val, },
                { field: "status", title: "T.Thái", sortable: false, width: column_properties._wth_status, },
            ],
            //editable: true,
            editable: "inline"
        });
    });

    $(function(){
        
    });
</script>