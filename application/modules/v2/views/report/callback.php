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
    <!-- <label class="control-label col-sm-2 dialog-control" for="pwd">Ca </label> -->
    <div class="form-group">
        <div class="col-sm-4 dialog-control">
            <select id="id_city" name="id_city" class="form-control-select">
                <option value="" > Chọn Tỉnh/T.Phố</option>
                <?php
                if( isset($city) AND !empty($city) ){
                    foreach ($city as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="col-sm-4 dialog-control">
            <select id="id_center" name="id_center" class="form-control-select">
                <option value="" > Chọn Chi nhánh</option>
            </select>
        </div>

        <div class="col-sm-4 dialog-control">
            <select id="id_frame" name="id_frame" class="form-control-select">
                <option value="" > Chọn Ca hẹn</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-4 dialog-control">
            <select id="id_department" name="id_department" class="form-control-select">
                <option value="" > Chọn Phòng</option>
            </select>
        </div>

        <div class="col-sm-4 dialog-control">
            <select id="id_group" name="id_group" class="form-control-select">
                <option value="" > Chọn Nhóm</option>
            </select>
        </div>

        <div class="col-sm-4 dialog-control">
            <select id="id_agent" name="id_agent" class="form-control-select">
                <option value="" > Chọn Nhân viên</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-4 dialog-control">
            <input type="text" id="starttime" name="birthday" class="form-control date-picker" placeholder="Ngày bắt đầu">
        </div>

        <div class="col-sm-4 dialog-control">
            <input type="text" id="endtime" name="birthday" class="form-control date-picker" placeholder="Ngày kết thúc">
        </div>

        <div class="col-sm-4 dialog-control">
            <button type="button" class="btn btn-primary btnSearch" style="height: 28px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Tra cứu thông tin</button>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <div id="grid"></div>
</div>

<script type="text/javascript">
    var url_introduced = "<?php echo base_url();?>listcallback";
    $(function() {
        var column_properties = $.parseJSON('<?php echo $column_properties; ?>');
        var grid_config = {
            'target': '#grid',
            'url': url_introduced,
            'toolbar_template': 'toolbar_template',
            'limit': 15,
            'columns': [
            	{ field: "order"        , title: 'No', sortable: false, width: column_properties._wth_order, locked: true, template: function(order){
                    return grid_number = grid_number + 1;
                }},
            	{ field: "fullname", title: 'Họ tên', width: column_properties._wth_fullname, sortable: false, locked: true,},
                { field: "mobile", title: 'Số ĐT', width: column_properties._wth_mobile, sortable: false,},
                { field: "source", title: 'Nguồn', width: column_properties._wth_source, sortable: false,},
                { field: "agent_ext", title: 'Ext', width: column_properties._wth_ext, sortable: false,},
                { field: "sname", title: 'Trạng thái', width: column_properties._wth_status, sortable: false,},
                { field: "created_at", title: 'TG Gọi', width: column_properties._wth_time, sortable: false,},
                { field: "created_at", title: 'TG Gọi lại', width: column_properties._wth_time, sortable: false,},
                { field: "content_call", title: 'Nội dung', width: column_properties._wth_content, sortable: false,},
            ]
        };
        var grid = create_grid(grid_config);
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#id_source').multiselect({
            buttonWidth: '100%',
            nonSelectedText: 'Chọn Nguồn dữ liệu',
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
                    <input id="text_search" class="k-header-input-search" placeholder="Số điện thoại" value="" type="search">
                </li>

                <li>
                    <select id="id_source" name="id_source" multiple="multiple" class="form-control-select">
                        <?php
                        if( isset($source) AND !empty($source) ){
                            foreach ($source as $key => $value) {
                                echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
        </span>
    </div>
</script>

<script type="text/javascript">
    if($('select')){
        $("select").select2({
            closeOnSelect: true
        });
    }

    $('#id_frame').select2("enable", false);

    $(".date-picker").kendoDatePicker({
        //value   : '<?php echo date("Y-m-d"); ?>',
        format  : 'yyyy-MM-dd',
    });

    $(function(){
        $('.btnSearch').click(function(){
            var inputsearch = $("#text_search").val();
            inputsearch     = inputsearch.replace(/ /g, '');

            var id_source   = $("#id_source").val();
            if( id_source != null && id_source != '' ){
                id_source = id_source.join();
            }else{
                id_source = 0;
            }
            
            var id_city     = $('#id_city').val();
            var id_center   = $('#id_center').val();
            var id_frame    = $('#id_frame').val();

            var id_department = $('#id_department').val();
            var id_group    = $('#id_group').val();
            var id_agent    = $('#id_agent').val();

            var starttime   = $('#starttime').val();
            var endtime     = $('#endtime').val();

            var _url = url;
            _url = _url+'?id_city='+id_city+'&id_center='+id_center+'&id_frame='+id_frame+'&id_department='+id_department+'&id_group='+id_group+'&id_agent='+id_agent+'&starttime='+starttime+'&endtime='+endtime;

            var _grid = $("#grid").data("kendoGrid");
            _grid.dataSource.options.url = _url;
            _grid.dataSource.read();
            _grid.refresh();
            return false;
        });

        $('#id_city').change(function(){
            var id_city = $(this).val();

            // Trung tam
            $.ajax({
                url     : 'ajax/centerbycity',
                type    : "GET",
                data    : { 'id_city':id_city },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var center = obj_decode;
                  
                    var html_center = '<option value="" selected="selected"> Chọn Chi nhánh</option>';
                    for(var key in center){
                        var detail  = center[key];
                        var id      = detail['id'];
                        var code    = detail['code'];
                        var name    = detail['name'];
                        html_center += '<option value="' + id + '">' + name + '</option>';
                    }
                    $('#id_center').html(html_center);
                    $("#id_center").select2("val", "");
                },

                error   : function(msg){
                    var html_center = '<option value="" selected="selected"> Chọn Chi nhánh</option>';
                    $('#id_center').html(html_center);
                    $("#id_center").select2("val", "");
                }
            });
        });

        $('#id_center').change(function(){
            var id_center = $(this).val();
            // Ca hen
            $.ajax({
                url     : 'ajax/frametimebycenter',
                type    : "GET",
                data    : { 'id_center':id_center },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var center = obj_decode;
                  
                    var html_frametime = '<option value="" selected="selected"> Chọn Ca hẹn</option>';
                    for(var key in center){
                        var detail  = center[key];
                        var id      = detail['id'];
                        var name    = detail['name'];
                        var start   = detail['start'];
                        var end     = detail['end'];
                        html_frametime += '<option value="' + id + '"> ' + name + ' (' + start + '-' + end + ')</option>';
                    }
                    $('#id_frame').html(html_frametime);
                    $("#id_frame").select2("val", "");
                },

                error   : function(msg){
                    var html_frametime = '<option value="" selected="selected"> Chọn Ca hẹn</option>';
                    $('#id_frame').html(html_frametime);
                    $("#id_frame").select2("val", "");
                }
            });

            // Phong
            $.ajax({
                url     : 'ajax/departmentbycenter',
                type    : "GET",
                data    : {'id_center':id_center},
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var department = obj_decode;
                  
                    var html_department = '<option value="" selected="selected"> Chọn Phòng</option>';
                    for(var key in department){
                        var detail  = department[key];
                        var id      = detail['id'];
                        var name    = detail['name'];
                        html_department += '<option value="' + id + '">' + name + '</option>';
                    }
                    $('#id_department').html(html_department);
                    $("#id_department").select2("val", "");

                    var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
                    $('#id_group').html(html_group);
                    $("#id_group").select2("val", "");
                },

                error   : function(msg){
                    var html_department = '<option value="" selected="selected"> Chọn Phòng</option>';
                    $('#id_department').html(html_department);
                    $("#id_department").select2("val", "");

                    var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
                    $('#id_group').html(html_group);
                    $("#id_group").select2("val", "");
                }
            });
        });

        $('#id_department').change(function(){
            var id_department = $(this).val();

            $.ajax({
                url     : 'ajax/groupbydepartment',
                type    : "GET",
                data    : {'id_department':id_department},
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var group = obj_decode;
          
                    var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
                    for(var key in group){
                        var detail  = group[key];
                        var id      = detail['id'];
                        var name    = detail['name'];
                        html_group += '<option value="' + id + '">' + name + '</option>';
                    }
                    $('#id_group').html(html_group);
                    $("#id_group").select2("val", "");
                },

                error   : function(msg){
                    var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
                    $('#id_group').html(html_group);
                    $("#id_group").select2("val", "");
                }
            });
        });

        $('#id_group').change(function(){
            var id_group = $(this).val();

            $.ajax({
                url     : 'ajax/agentbygroup',
                type    : "GET",
                data    : { 'id_group':id_group },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var agent = obj_decode;
                  
                    var html_agent = '<option value="" selected="selected"> Chọn Nhân viên</option>';
                    for(var key in agent){
                        var detail  = agent[key];
                        var id      = detail['id'];
                        var full_name    = detail['full_name'];
                        var username    = detail['username'];
                        html_agent += '<option value="' + id + '">' + full_name + ' (' + username + ')</option>';
                    }
                    $('#id_agent').html(html_agent);
                    $("#id_agent").select2("val", "");
                },

                error   : function(msg){
                    var html_agent = '<option value="" selected="selected"> Chọn Nhân viên</option>';
                    $('#id_agent').html(html_agent);
                    $("#id_agent").select2("val", "");
                }
            });
        });
    });
</script>