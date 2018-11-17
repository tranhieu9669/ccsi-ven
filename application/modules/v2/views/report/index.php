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

    $(document).ready(function() {
        $('#id_center').multiselect({
            buttonWidth: '100%',
            nonSelectedText: 'Chọn Trung tâm',
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false
        });

        $('#id_department').multiselect({
            buttonWidth: '100%',
            nonSelectedText: 'Trạng Phòng',
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false
        });

        $('#id_group').multiselect({
            buttonWidth: '100%',
            nonSelectedText: 'Trạng Nhóm',
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false
        });

        $('#id_agent').multiselect({
            buttonWidth: '100%',
            nonSelectedText: 'Chọn Nhân viên',
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false
        });

        $('#id_source').multiselect({
            buttonWidth: '100%',
            nonSelectedText: 'Chọn Nguồn',
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false
        });

        $('#id_fileup').multiselect({
            buttonWidth: '100%',
            nonSelectedText: 'Chọn File',
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false
        });
    });
</script>

<div class="form-horizontal">
    <script type="text/javascript">
        $(function(){
            $('#id_center').change(function(){
                var id_center = $(this).val();
                if( id_center != null && id_center != '' ){
                    id_center = id_center.join();
                }else{
                    id_center = 0;
                }
                
                // Phong
                var option_department = $("#id_department");
                option_department.empty();
                $.ajax({
                    url     : '<?php echo base_url()?>ajax/departmentbycenter',
                    type    : "GET",
                    data    : {'id_center':id_center},
                    success : function( data ) {
                        var obj_decode = $.parseJSON(data);
                        var department = obj_decode;
                      
                        for(var key in department){
                            var detail  = department[key];
                            var id      = detail['id'];
                            var name    = detail['name'];
                            option_department.append($("<option />").val(id).text(' ' + name));
                        }

                        option_department.multiselect('rebuild');
                    },

                    error   : function(msg){
                        option_department.multiselect('rebuild');
                    }
                });
    
                // Nhom
                var option_group = $("#id_group");
                option_group.empty();
                $.ajax({
                    url     : '<?php echo base_url()?>ajax2/groupbycenter',
                    type    : "GET",
                    data    : {'id_center':id_center},
                    success : function( data ) {
                        var obj_decode = $.parseJSON(data);
                        var department = obj_decode;
                      
                        for(var key in department){
                            var detail  = department[key];
                            var id      = detail['id'];
                            var name    = detail['name'];
                            option_group.append($("<option />").val(id).text(' ' + name));
                        }

                        option_group.multiselect('rebuild');
                    },

                    error   : function(msg){
                        option_group.multiselect('rebuild');
                    }
                });
                
                // Nhan vien 
                var option_agent = $("#id_agent");
                option_agent.empty();
                $.ajax({
                    url     : '<?php echo base_url()?>ajax2/agentbycenter',
                    type    : "GET",
                    data    : {'id_center':id_center},
                    success : function( data ) {
                        var obj_decode = $.parseJSON(data);
                        var department = obj_decode;
                      
                        for(var key in department){
                            var detail = department[key];
                            var id = detail['id'];
                            var full_name = detail['full_name'];
                            var ext = detail['ext'];
                            option_agent.append($("<option />").val(id).text(' '+full_name+'('+ext+')'));
                        }

                        option_agent.multiselect('rebuild');
                    },

                    error   : function(msg){
                        option_agent.multiselect('rebuild');
                    }
                });

                gridLoad();
            });
        });
    </script>
    <div class="form-group">
        <div class="col-sm-3 dialog-control">
            <select id="id_center" name="id_center" multiple="multiple" class="form-control-select">
                <?php
                if(isset($center) AND !empty($center)){
                    foreach ($center as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="col-sm-3 dialog-control">
            <script type="text/javascript">
                $(function(){
                    $('#id_department').change(function(){
                        var id_department = $(this).val();
                        if( id_department != null && id_department != '' ){
                            id_department = id_department.join();
                        }else{
                            id_department = 0;
                        }
                        
                        // Nhom
                        var option_group = $("#id_group");
                        option_group.empty();
                        $.ajax({
                            url     : '<?php echo base_url()?>ajax2/groupbydepartment',
                            type    : "GET",
                            data    : {'id_department':id_department},
                            success : function( data ) {
                                var obj_decode = $.parseJSON(data);
                                var department = obj_decode;
                              
                                for(var key in department){
                                    var detail  = department[key];
                                    var id      = detail['id'];
                                    var name    = detail['name'];
                                    option_group.append($("<option />").val(id).text(' ' + name));
                                }

                                option_group.multiselect('rebuild');
                            },

                            error   : function(msg){
                                option_group.multiselect('rebuild');
                            }
                        });
                        
                        // Nhan vien
                        var option_agent = $("#id_agent");
                        option_agent.empty();
                        $.ajax({
                            url     : '<?php echo base_url()?>ajax2/agentbydepartment',
                            type    : "GET",
                            data    : {'id_department':id_department},
                            success : function( data ) {
                                var obj_decode = $.parseJSON(data);
                                var department = obj_decode;
                              
                                for(var key in department){
                                    var detail = department[key];
                                    var id = detail['id'];
                                    var full_name = detail['full_name'];
                                    var ext = detail['ext'];
                                    option_agent.append($("<option />").val(id).text(' '+full_name+'('+ext+')'));
                                }

                                option_agent.multiselect('rebuild');
                            },

                            error   : function(msg){
                                option_agent.multiselect('rebuild');
                            }
                        });

                        gridLoad();
                    });
                });
            </script>
            <select id="id_department" name="id_department" multiple="multiple" class="form-control-select"></select>
        </div>

        <div class="col-sm-3 dialog-control">
            <script type="text/javascript">
                $(function(){
                    $('#id_group').change(function(){
                        var id_group = $(this).val();
                        if( id_group != null && id_group != '' ){
                            id_group = id_group.join();
                        }else{
                            id_group = 0;
                        }
                        // Nhan vien
                        var option_agent = $("#id_agent");
                        option_agent.empty();
                        $.ajax({
                            url     : '<?php echo base_url()?>ajax2/agentbygroup',
                            type    : "GET",
                            data    : {'id_group':id_group},
                            success : function( data ) {
                                var obj_decode = $.parseJSON(data);
                                var department = obj_decode;
                              
                                for(var key in department){
                                    var detail = department[key];
                                    var id = detail['id'];
                                    var full_name = detail['full_name'];
                                    var ext = detail['ext'];
                                    option_agent.append($("<option />").val(id).text(' '+full_name+'('+ext+')'));
                                }

                                option_agent.multiselect('rebuild');
                            },

                            error   : function(msg){
                                option_agent.multiselect('rebuild');
                            }
                        });

                        gridLoad();
                    });
                });
            </script>
            <select id="id_group" name="id_group" multiple="multiple" class="form-control-select"></select>
        </div>

        <div class="col-sm-3 dialog-control">
            <script type="text/javascript">
                $(function(){
                    $('#startdate').change(function(){
                        gridLoad();
                    });
                });
            </script>
            <input type="text" id="startdate" name="startdate" class="form-control date-picker" placeholder="Ngày bắt đầu">
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-3 dialog-control">
            <script type="text/javascript">
                $(function(){
                    $('#id_agent').change(function(){
                        gridLoad();
                    });
                });
            </script>
            <select id="id_agent" name="id_agent" multiple="multiple" class="form-control-select">
                <?php
                if(isset($agent) AND !empty($agent)){
                    foreach ($agent as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['full_name'].'('.$value['ext'].')</option>';
                    }
                }
                ?>
            </select>
        </div>

        <script type="text/javascript">
            $(function(){
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

                    gridLoad();
                });
            });
        </script>
        <div class="col-sm-3 dialog-control">
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
                $(function(){
                    $('#id_fileup').change(function(){
                        gridLoad();
                    });
                });
            </script>
            <select id="id_fileup" name="id_fileup" multiple="multiple" class="form-control-select"></select>
        </div>

        <div class="col-sm-3 dialog-control">
            <script type="text/javascript">
                $(function(){
                    $('#enddate').change(function(){
                        gridLoad();
                    });
                });
            </script>
            <input type="text" id="enddate" name="enddate" class="form-control date-picker" placeholder="Ngày kết thúc">
        </div>
    </div>
</div>

<div class="col-sm-12">
    <div id="grid"></div>
</div>

<script type="text/javascript">
    var url = "<?php echo base_url();?>listcall";
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
            'limit': 50,
            'columns': [
            	{ field: "order", title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
            	{ field: "fullname", title: 'Họ tên', width: column_properties._wth_fullname, sortable: false},
                { field: "mobile", title: 'Số ĐT', width: column_properties._wth_mobile, sortable: false, template: '#=ColorTextSearch(mobile)#'},
                { field: "source", title: 'Nguồn', width: column_properties._wth_source, sortable: false,},
                { field: "agent_ext", title: 'Ext', width: column_properties._wth_ext, sortable: false,},
                { field: "sname", title: 'Trạng thái', width: column_properties._wth_status, sortable: false,},
                { field: "created_at", title: 'TG Gọi', width: column_properties._wth_time, sortable: false,},
                { field: "content_call", title: 'Nội dung', sortable: false,},
                { field: "call", title: 'Call', sortable: false, width: 60, template: function(data){
                    var html = '<a href="<?php echo base_url();?>callback/'+data.id_cus+'/callback" title="Gọi lại"><span class="glyphicon glyphicon-headphones" aria-hidden="true"></span></a>';
                    html += '<a href="<?php echo base_url();?>callback/'+data.id_cus+'/view" title="Chi tiết"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></a>';
                    return html;
                }},
            ]
        };
        var grid = create_grid(grid_config);
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#id_status').multiselect({
            buttonWidth: '100%',
            nonSelectedText: 'Chọn Trạng thái',
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false
        });
    });
</script>

<script id="toolbar_template" type="text/x-kendo-template">
	<div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <input id="text_search" class="k-header-input-search" placeholder="Điện thoại hoặc tên" type="search" style="line-height: 26px;">
                </li>
                <li>
                    <input id="text_search" class="k-header-input-search" placeholder="Số điện thoại" value="" type="search">
                </li>
                <li>
                    <select id="id_status" name="id_status" multiple="multiple" class="form-control-select">
                        <?php
                        if( isset($call_status) AND !empty($call_status) ){
                            foreach ($call_status as $key => $value) {
                                echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <a id="exportdata" href="javascript:void(0);" target="blank"><span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span></a>
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    $(".date-picker").kendoDatePicker({
        format  : 'yyyy-MM-dd',
    });

    $(function(){
        $("#text_search").keypress(function(e){
            if(e.keyCode == 13){
                gridLoad();
            }
        });
    });

    function gridLoad(){
        var inputsearch = $("#text_search").val();
        inputsearch     = inputsearch.replace(/ /g, '');
        var param_url = '?inputsearch='+inputsearch;

        var id_center = $('#id_center').val();
        if( id_center != null && id_center != '' ){
            id_center = id_center.join();
            param_url += '&id_center='+id_center;
        }else{
            id_center = 0;
        }

        var id_department = $('#id_department').val();
        if( id_department != null && id_department != '' ){
            id_department = id_department.join();
            param_url += '&id_department='+id_department;
        }else{
            id_department = 0;
        }

        var id_group = $('#id_group').val();
        if( id_group != null && id_group != '' ){
            id_group = id_group.join();
            param_url += '&id_group='+id_group;
        }else{
            id_group = 0;
        }

        var id_agent = $('#id_agent').val();
        if( id_agent != null && id_agent != '' ){
            id_agent = id_agent.join();
            param_url += '&id_agent='+id_agent;
        }else{
            id_agent = 0;
        }

        var id_source = $("#id_source").val();
        if( id_source != null && id_source != '' ){
            id_source = id_source.join();
            param_url += '&id_source='+id_source;
        }else{
            id_source = 0;
        }

        var id_fileup = $("#id_fileup").val();
        if( id_fileup != null && id_fileup != '' ){
            id_fileup = id_fileup.join();
            param_url += '&id_fileup='+id_fileup;
        }else{
            id_fileup = 0;
        }

        var id_status   = $("#id_status").val();
        if( id_status != null && id_status != '' ){
            id_status = id_status.join();
            param_url += '&id_status='+id_status;
        }else{
            id_status = 0;
        }

        var startdate = $("#startdate").val();
        if(startdate != ''){
            param_url += '&startdate='+startdate;
        }

        var enddate = $("#enddate").val();
        if(enddate != ''){
            param_url += '&enddate='+enddate;
        }

        var _url = url + param_url;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function(){
        $('#id_status').change(function(){
            gridLoad();
        });

        $('#exportdata').click(function(){
            alert(1);
        });
    });
</script>