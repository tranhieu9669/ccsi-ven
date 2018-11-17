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

<link rel="stylesheet" href="<?php echo base_url();?>assets/selectcheckbox/docs/css/bootstrap-example.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/selectcheckbox/docs/css/prettify.css" type="text/css">
<script type="text/javascript" src="<?php echo base_url();?>assets/selectcheckbox/docs/js/prettify.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/selectcheckbox/dist/css/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="<?php echo base_url();?>assets/selectcheckbox/dist/js/bootstrap-multiselect.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        window.prettyPrint() && prettyPrint();
    });
</script>

<div class="form-horizontal">
    <div class="form-group">
        <div class="col-sm-3 dialog-control">
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#id_agent').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Chọn Agent',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>
            <select id="id_agent" name="id_agent" multiple="multiple" class="form-control-select">
                <?php
                if( isset($agent) AND !empty($agent) ){
                    foreach ($agent as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['full_name'].'('.$value['ext'].')</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="col-sm-3 dialog-control">
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#id_source').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Chọn Nguồn',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>
            <select id="id_source" name="id_source" multiple="multiple" class="form-control-select">
                <?php
                if( isset($source) AND !empty($source) ){
                    foreach ($source as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="col-sm-3 dialog-control">
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#id_fileup').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Chọn File',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>
            <select id="id_fileup" name="id_fileup" multiple="multiple" class="form-control-select"></select>
        </div>

        <div class="col-sm-3 dialog-control">
            <button type="button" class="btn btn-primary btnSearch" style="height: 31px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Bỏ phân bổ</button>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <div id="grid"></div>
</div>

<script type="text/javascript">
    var url = "<?php echo base_url();?>customer/unassign";
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
            'limit': 20,
            'columns': [
                { field: "order", title: "<input id='checkAll' type='checkbox' class='check-box' />", sortable: false, width: column_properties._wth_order, template: function(data){
                    return "<input type='checkbox' value='" + data.id + "' name='idselected[]' class='check-box-item' />";
                }},
                { field: "fullname", title: 'Khách hàng', sortable: false, },/*locked: true,*/
                { field: "mobile", title: 'Số ĐT', sortable: false, width: column_properties._wth_mobile, template: '#=ColorTextSearch(mobile)#'},
                { field: "source", title: 'source', width: column_properties._wth_source, sortable: false,},
                { field: "created_at", title: 'created_at', width: column_properties._wth_time, sortable: false,},
                { field: "end_ext", title: 'Ext', width: column_properties._wth_ext, sortable: false,},
                { field: "edit", title: 'edit', width: column_properties._wth_action, sortable: false, template: function(data){
                    var html = '<a _modal="dialog_infor_detail" _title="Sửa thông tin tài khoản Khách hàng" href="<?php echo base_url();?>customer/detail/' + data.id + '" class="sys_modal btn btn-primary btn_edit btn-default">';
                    html = html + 'Sửa';
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

                <li></li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <a _modal="dialog_infor_detail" _title="Thêm thông tin Khách hàng" href="<?php echo base_url()?>customer/detail" class="sys_modal"><i class="fa fa-plus-square-o"></i>Thêm thông tin</a>
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    // Load du lieu grid
    function onLoadGrid(){
        $('#checkAll').prop('checked', false);

        var id_agent    = $('#id_agent').val();
        if( id_agent != null && id_source != '' ){
            id_agent = id_agent.join();
        }else{
            id_agent = 0;
        }

        var id_source   = $("#id_source").val();
        if( id_source != null && id_source != '' ){
            id_source = id_source.join();
        }else{
            id_source = 0;
        }

        var id_fileup = $("#id_fileup").val();
        if( id_fileup != null && id_source != '' ){
            id_fileup = id_fileup.join();
        }else{
            id_fileup = 0;
        }

        var inputsearch = $("#text_search").val();
        inputsearch     = inputsearch.replace(/ /g, '');

        var _url = url;
        _url = _url + '?id_agent='+id_agent+'&id_source='+id_source+'&id_fileup='+id_fileup+'&inputsearch='+inputsearch;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }
    // Thay doi thong tin load du lieu
    $(function(){
        $('#id_agent').change(function(){
            onLoadGrid();
        });

        $('#id_source').change(function(){
            onLoadGrid();
        });

        $('#id_fileup').change(function(){
            onLoadGrid();
        });

        $("#text_search").keypress(function(e){
            if(e.keyCode == 13){
                onLoadGrid();
            }
        });
    });
    // check all checkbox
    $(function(){
        $('#checkAll').change(function () {
            if( $(this).is(':checked') ) {
                $('.check-box-item').prop('checked', true);
            } else {
                $('.check-box-item').prop('checked', false);
            }
        });
    });

    $(function(){
        $('.btnSearch').click(function(){
            var hdidselected = '';
            $('input[name="idselected[]"]').each(function(){
                if($(this).is(':checked')){
                    if( hdidselected != '' ){
                        hdidselected = hdidselected + ',' + $(this).val();
                    }else{
                        hdidselected = $(this).val();
                    }
                }
            });

            var id_agent    = $('#id_agent').val();
            if( id_agent != null && id_source != '' ){
                id_agent = id_agent.join();
            }else{
                id_agent = 0;
            }

            var id_source   = $("#id_source").val();
            if( id_source != null && id_source != '' ){
                id_source = id_source.join();
            }else{
                id_source = 0;
            }

            var id_fileup = $("#id_fileup").val();
            if( id_fileup != null && id_source != '' ){
                id_fileup = id_fileup.join();
            }else{
                id_fileup = 0;
            }

            var inputsearch = $("#text_search").val();
            inputsearch     = inputsearch.replace(/ /g, '');

            // customer/acassign
            $.ajax({
                url     : '<?php echo base_url()?>customer/acunassign',
                type    : "POST",
                data    : { 'hdidselected':hdidselected, 'id_agent':id_agent, 'id_source':id_source, 'id_fileup':id_fileup, 'inputsearch':inputsearch },
                success : function( data ) {
                    switch(data){
                        case 'error':
                            alert('Lỗi lấy lại khách hàng 1.');
                            break;
                        case 'fail':
                            alert('Lỗi lấy lại khách hàng 2.');
                            break;
                        default:
                            alert('Lấy lại khách hàng thành công.');
                            onLoadGrid();
                    }
                },
                error   : function(msg){
                    
                }
            });
        });

        $('#id_source').change(function(){
            var id_source = $(this).val();
            if( id_source != null && id_source != '' ){
                id_source = id_source.join();
            }else{
                id_source = 0;
            }

            var option_fileup = $("#id_fileup");
            option_fileup.empty();

            $.ajax({
                url     : '<?php echo base_url()?>ajax/fileupbysource',
                type    : "GET",
                data    : { 'id_source':id_source },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var fileup = obj_decode;
                  
                    for(var key in fileup){
                        var detail  = fileup[key];
                        var id      = detail['id'];
                        var title   = detail['title'];
                        option_fileup.append($("<option />").val(id).text(' ' + title));
                    }
                    option_fileup.multiselect('rebuild');
                },

                error   : function(msg){
                    option_fileup.multiselect('rebuild');
                }
            });
        });
    });
</script>