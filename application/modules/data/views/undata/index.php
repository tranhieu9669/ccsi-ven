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
        <div class="col-sm-2 dialog-control">
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#id_center').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Trung tâm',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>
            <select id="id_center" name="id_center" class="form-control-select">
                <?php
                echo '<option value="0" > Trung Tâm</option>';
                if( isset($center) AND !empty($center) ){
                    foreach ($center as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-sm-2 dialog-control">
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#id_department').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Chọn Phòng',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>
            <select id="id_department" name="id_department" multiple="multiple" class="form-control-select"></select>
        </div>
        <div class="col-sm-2 dialog-control">
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#id_group').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Chọn Nhóm',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>
            <select id="id_group" name="id_group" multiple="multiple" class="form-control-select"></select>
        </div>
        <div class="col-sm-2 dialog-control">
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
            <select id="id_agent" name="id_agent" multiple="multiple" class="form-control-select"></select>
        </div>
		<div class="col-sm-2 dialog-control">
            <input type="text" id="followdate" class="form-control date-picker" placeholder="Thời gian gọi lại" >
        </div>
		<div class="col-sm-2 dialog-control">
            <input type="text" id="followdate2" class="form-control date-picker" placeholder="Thời gian gọi lại" >
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-2 dialog-control">
            <script type="text/javascript">
                $(document).ready(function() {
                     $('#id_call_status').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Trạng thái chính',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });

                });
            </script>

            <select id="id_call_status" name="id_call_status" multiple="multiple" class="form-control-select">
                <?php
                if( isset($call_status) AND !empty($call_status) ){
                    foreach ($call_status as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-sm-2 dialog-control">
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#id_call_status_c1').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Trạng thái 1',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>

            <select id="id_call_status_c1" name="id_call_status_c1" multiple="multiple" class="form-control-select"></select>
        </div>
        <div class="col-sm-2 dialog-control">
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#id_call_status_c2').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Trạng thái 2',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>

            <select id="id_call_status_c2" name="id_call_status_c2" multiple="multiple" class="form-control-select"></select>
        </div>
        <div class="col-sm-2 dialog-control">
            <input type="text" id="closedate" class="form-control date-picker" placeholder="Thời gian đóng từ" >
        </div>
		<div class="col-sm-2 dialog-control">
            <input type="text" id="closedate2" class="form-control date-picker" placeholder="Thời gian đóng đến" >
        </div>
		<div class="col-sm-2 dialog-control">
            <input type="number" name="text_search" id="text_search" class="form-control" placeholder="Số thu hồi" value="" min="1" max="5000" step="1" />
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-2 dialog-control">
            <select id="id_source" name="id_source" class="form-control-select">
                <option value="0" > Nguồn dữ liệu</option>
                <?php
                if( isset($source) AND !empty($source) ){
                    foreach ($source as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-sm-2 dialog-control">
            <button type="button" class="btn btn-primary btnSearch" style="height: 32px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Lấy Khách hàng</button>
        </div>
    </div>
</div>
<input type="hidden" id="hdstatus_c1" value="0" />
<input type="hidden" id="hdstatus_c2" value="0" />
<div class="col-sm-12">
    <div id="grid"></div>
</div>

<script type="text/javascript">
    var url = "<?php echo base_url();?>data/undata";
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
                { field: "order" , title: 'No', sortable: false, width: column_properties._wth_order, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "fullname", title: 'Tên khách hàng', sortable: false, },
                { field: "mobile", title: 'Số ĐT', sortable: false, width: column_properties._wth_mobile, template: '#=ColorTextSearch(mobile)#'},
                { field: "source", title: 'Nguồn', width: 125, sortable: false,},
                { field: "end_ext", title: 'Máy lẻ', width: column_properties._wth_ext, sortable: false,},
                { field: "start_date", title: 'Ngày mở', width: column_properties._wth_date, sortable: false,},
                { field: "close_date", title: 'Ngày đóng', width: column_properties._wth_date, sortable: false,},
                { field: "callback", title: 'Gọi lại', width: column_properties._wth_date + 50, sortable: false,},
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
                    <i class="halflings-icon list"></i>
                    <span class="title-template-grid">Danh sách Khách hàng</span>
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
    // Load du lieu grid
    function onLoadGrid(){
        var _param = '?dm=1';

        var id_center = $('#id_center').val();
        if( id_center == '' ){
            id_center = 0;
        }
        if(id_center != 0){
            _param += '&id_center='+id_center;
        }

        var id_department = $('#id_department').val();
        if( id_department != null && id_department != '' ){
            id_department = id_department.join();
        }else{
            id_department = 0;
        }
        if(id_department != 0){
            _param += '&id_department='+id_department;
        }

        var id_group = $('#id_group').val();
        if( id_group != null && id_group != '' ){
            id_group = id_group.join();
        }else{
            id_group = 0;
        }
        if(id_group != 0){
            _param += '&id_group='+id_group;
        }

        var id_agent = $('#id_agent').val();
        if( id_agent != null && id_agent != '' ){
            id_agent = id_agent.join();
        }else{
            id_agent = 0;
        }
        if(id_agent != 0){
            _param += '&id_agent='+id_agent;
        }

        var id_call_status = $('#id_call_status').val();
        if( id_call_status != null && id_call_status != '' ){
            id_call_status = id_call_status.join();
        }else{
            id_call_status = 0;
        }
        if(id_call_status != 0){
            _param += '&id_call_status='+id_call_status;
        }

        var id_call_status_c1 = $('#id_call_status_c1').val();
        if( id_call_status_c1 != null && id_call_status_c1 != '' ){
            id_call_status_c1 = id_call_status_c1.join();
        }else{
            id_call_status_c1 = 0;
        }
        if(id_call_status_c1 != 0){
            _param += '&id_call_status_c1='+id_call_status_c1;
        }

        var id_call_status_c2 = $('#id_call_status_c2').val();
        if( id_call_status_c2 != null && id_call_status_c2 != '' ){
            id_call_status_c2 = id_call_status_c2.join();
        }else{
            id_call_status_c2 = 0;
        }
        if(id_call_status_c2 != 0){
            _param += '&id_call_status_c2='+id_call_status_c2;
        }

        var id_source = $('#id_source').val();
        _param += '&id_source='+id_source;

        var closedate = $('#closedate').val();
        if(closedate != ''){
            _param += '&closedate='+closedate;
        }

        var closedate2 = $('#closedate2').val();
        if(closedate2 != ''){
            _param += '&closedate2='+closedate2;
        }

        var followdate = $('#followdate').val();
        if(followdate != ''){
            _param += '&followdate='+followdate;
        }

        var followdate2 = $('#followdate2').val();
        if(followdate != ''){
            _param += '&followdate2='+followdate2;
        }

        var inputsearch = $("#text_search").val();
        inputsearch     = inputsearch.replace(/ /g, '');
        if(inputsearch != ''){
            _param += '&inputsearch='+inputsearch;
        }

        var _url = url + _param;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function(){
        $("#closedate").kendoDatePicker({
            format: "yyyy-MM-dd",
        });
		
		$("#closedate2").kendoDatePicker({
            format: "yyyy-MM-dd",
        });

        $("#followdate").kendoDatePicker({
            format: "yyyy-MM-dd",
        });
		
		$("#followdate2").kendoDatePicker({
            format: "yyyy-MM-dd",
        });

        $("#id_source").select2({
            closeOnSelect: true
        });

        $('.btnSearch').on('click', function(){
            var id_center = $('#id_center').val();
            if(id_center == 0){
                alert('Bạn phải chọn trung tâm');
                return false;
            }

            var id_department = $('#id_department').val();
            if( id_department != null && id_department != '' ){
                id_department = id_department.join();
            }else{
                id_department = 0;
            }
            if(id_department == 0){
                alert('Bạn phải chọn phòng');
                return false;
            }

            var id_group = $('#id_group').val();
            if( id_group != null && id_group != '' ){
                id_group = id_group.join();
            }else{
                id_group = 0;
            }

            var id_agent = $('#id_agent').val();
            if( id_agent != null && id_agent != '' ){
                id_agent = id_agent.join();
            }else{
                id_agent = 0;
            }

            var id_call_status = $('#id_call_status').val();
            if( id_call_status != null && id_call_status != '' ){
                id_call_status = id_call_status.join();
            }else{
                id_call_status = 0;
            }
            if(id_call_status == 0){
                alert('Bạn phải chọn trạng thái chính');
                return false;
            }

            var id_call_status_c1 = $('#id_call_status_c1').val();
            if( id_call_status_c1 != null && id_call_status_c1 != '' ){
                id_call_status_c1 = id_call_status_c1.join();
            }else{
                id_call_status_c1 = 0;
            }
            var id_source = $('#id_source').val();

            var hdstatus_c1 = $('#hdstatus_c1').val();
            if(parseInt(hdstatus_c1) > 0){
                if(id_call_status_c1 == 0){
                    alert('Bạn phải chọn trạng thái 1');
                    return false;
                }
            }

            var id_call_status_c2 = $('#id_call_status_c2').val();
            if( id_call_status_c2 != null && id_call_status_c2 != '' ){
                id_call_status_c2 = id_call_status_c2.join();
            }else{
                id_call_status_c2 = 0;
            }
            var hdstatus_c2 = $('#hdstatus_c2').val();
            if(parseInt(hdstatus_c2) > 0){
                if(id_call_status_c2 == 0){
                    alert('Bạn phải chọn trạng thái 2');
                    return false;
                }
            }

            var closedate = $('#closedate').val();
            var closedate2 = $('#closedate2').val();
            var followdate = $('#followdate').val();
            var followdate2 = $('#followdate2').val();

            var inputsearch = $("#text_search").val();

            if ( ! Number.isInteger( parseInt(inputsearch) ) ) {
                alert('Sô lượng thu hồi phải là số');
                return false;
            }
            //inputsearch     = inputsearch.replace(/ /g, '');

            $.ajax({
                url     : '<?php echo base_url()?>data/undata/acundata',
                type    : "POST",
                async   : false,
                data    : {
                    'id_center' : id_center,
                    'id_department' : id_department,
                    'id_group' : id_group,
                    'id_agent' : id_agent,
                    'id_call_status' : id_call_status,
                    'id_call_status_c1' : id_call_status_c1,
                    'id_call_status_c2' : id_call_status_c2,
                    'id_source' : id_source,
                    'closedate' : closedate,
                    'closedate2' : closedate2,
                    'followdate' : followdate,
                    'followdate2' : followdate2,
                    'inputsearch' : inputsearch,
                },
                success : function( data ) {
                    console.log(data);
                    if(data == 'success'){
                        alert('Thành công');
                        onLoadGrid();
                    }else{
                        alert('Lỗi 1');
                    }
                },

                error   : function(msg){
                    alert('Lỗi 2');
                }
            });
        });
    });

    $(function(){
        $('#id_center').change(function(){
            var id_center = $(this).val();

            var option_department = $("#id_department");
            option_department.empty();

            var option_group = $("#id_group");
            option_group.empty();

            var option_agent = $("#id_agent");
            option_agent.empty();

            $.ajax({
                url     : '<?php echo base_url()?>data/ajax/get_department',
                type    : "GET",
                //async   : false,
                data    : { 'id_center':id_center },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var department = obj_decode;
                  
                    for(var key in department){
                        var detail  = department[key];
                        var id = detail['id'];
                        var name = detail['name'];
                        option_department.append($("<option />").val(id).text(name));
                    }
                    option_department.multiselect('rebuild');
                },

                error   : function(msg){
                    option_department.multiselect('rebuild');
                }
            });

            onLoadGrid();
        });

        $('#id_department').change(function(){
            var id_department = $(this).val();
            if( id_department != null && id_department != '' ){
                id_department = id_department.join();
            }else{
                id_department = 0;
            }

            var option_group = $("#id_group");
            option_group.empty();

            var option_agent = $("#id_agent");
            option_agent.empty();
            
            $.ajax({
                url     : '<?php echo base_url()?>data/ajax/get_group_agent',
                type    : "GET",
                data    : { 'id_department':id_department },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var group = obj_decode['group'];
                    var agent = obj_decode['agent'];
                  
                    for(var key in group){
                        var detail  = group[key];
                        var id = detail['id'];
                        var name = detail['name'];
                        option_group.append($("<option />").val(id).text(name));
                    }
                    option_group.multiselect('rebuild');

                    option_agent.append($("<option />").val('99999').text('Không phân Agent'));
                    for(var key in agent){
                        var detail  = agent[key];
                        var id = detail['id'];
                        var full_name = detail['full_name'];
                        var ext = detail['ext'];
                        option_agent.append($("<option />").val(id).text(full_name+'('+ext+')'));
                    }
                    option_agent.multiselect('rebuild');
                },

                error   : function(msg){
                    option_group.multiselect('rebuild');
                    option_agent.multiselect('rebuild');
                }
            });

            onLoadGrid();
        });

        $('#id_group').change(function(){
            var id_group = $(this).val();
            if( id_group != null && id_group != '' ){
                id_group = id_group.join();
            }else{
                id_group = 0;
            }

            var option_agent = $("#id_agent");
            option_agent.empty();

            $.ajax({
                url     : '<?php echo base_url()?>data/ajax/get_agent',
                type    : "GET",
                data    : { 'id_group':id_group },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var agent = obj_decode;

                    option_agent.append($("<option />").val('99999').text('Không phân Agent'));
                  
                    for(var key in agent){
                        var detail  = agent[key];
                        var id = detail['id'];
                        var full_name = detail['full_name'];
                        var ext = detail['ext'];
                        option_agent.append($("<option />").val(id).text(full_name+'('+ext+')'));
                    }
                    option_agent.multiselect('rebuild');
                },

                error   : function(msg){
                    option_agent.multiselect('rebuild');
                }
            });

            onLoadGrid();
        });

        $('#id_agent').change(function(){
            onLoadGrid();
        });

        $('#id_call_status').change(function(){
            var id_call_status = $(this).val();
            if( id_call_status != null && id_call_status != '' ){
                id_call_status = id_call_status.join();
            }else{
                id_call_status = 0;
            }

            var option_status_c1 = $("#id_call_status_c1");
            option_status_c1.empty();

            var option_status_c2 = $("#id_call_status_c2");
            option_status_c2.empty();

            $.ajax({
                url     : '<?php echo base_url()?>data/ajax/get_status_c1',
                type    : "GET",
                data    : { 'id_call_status':id_call_status },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var status_c1 = obj_decode;
                    
                    var checkval = 0;
                    for(var key in status_c1){
                        checkval++;
                        var detail  = status_c1[key];
                        var id = detail['id'];
                        var name = detail['name'];
                        option_status_c1.append($("<option />").val(id).text(name));
                    }
                    option_status_c1.multiselect('rebuild');
                    if(checkval > 0){
                        $('#hdstatus_c1').val(1);
                    }else{
                        $('#hdstatus_c1').val(0);
                    }
                },

                error   : function(msg){
                    option_status_c1.multiselect('rebuild');
                    $('#hdstatus_c1').val(0);
                }
            });

            onLoadGrid();
        });

        $('#id_call_status_c1').change(function(){
            var id_call_status_c1 = $(this).val();
            if( id_call_status_c1 != null && id_call_status_c1 != '' ){
                id_call_status_c1 = id_call_status_c1.join();
            }else{
                id_call_status_c1 = 0;
            }

            var option_status_c2 = $("#id_call_status_c2");
            option_status_c2.empty();

            $.ajax({
                url     : '<?php echo base_url()?>data/ajax/get_status_c2',
                type    : "GET",
                data    : { 'id_call_status_c1':id_call_status_c1 },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var status_c2 = obj_decode;
                    
                    var checkval = 0;
                    for(var key in status_c2){
                        checkval++;
                        var detail  = status_c2[key];
                        var id = detail['id'];
                        var name = detail['name'];
                        option_status_c2.append($("<option />").val(id).text(name));
                    }
                    option_status_c2.multiselect('rebuild');
                    if(checkval > 0){
                        $('#hdstatus_c2').val(1);
                    }else{
                        $('#hdstatus_c2').val(0);
                    }
                },

                error   : function(msg){
                    option_status_c2.multiselect('rebuild');
                    $('#hdstatus_c2').val(0);
                }
            });

            onLoadGrid();
        });

        $('#id_call_status_c2').change(function(){
            onLoadGrid();
        });

        $('#closedate').change(function(){
            onLoadGrid();
        });

        $('#closedate2').change(function(){
            onLoadGrid();
        });

        $('#followdate').change(function(){
            onLoadGrid();
        });

        $('#followdate2').change(function(){
            onLoadGrid();
        });

        $('#id_source').change(function(){
            onLoadGrid();
        });
    });
</script>