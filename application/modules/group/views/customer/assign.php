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
                    $('#id_city').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Chọn Tỉnh/T.Phố',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>
            <select id="id_city" name="id_city" multiple="multiple" class="form-control-select">
                <?php
                if( isset($city) AND !empty($city) ){
                    foreach ($city as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
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
            <select id="id_agent" name="id_agent" multiple="multiple" class="form-control-select">
                <?php
                if( isset($agent) AND !empty($agent) ){
                    foreach ($agent as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['ext'].'-'.$value['full_name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>

        <?php if( $this->_id_group==54 AND ($this->_role == 'group') ){ ?>
        <div class="col-sm-2 dialog-control">
            <input id='exception' name="exception" type='checkbox' class='check-box form-check-input' /> <label class="form-check-label" for="exception">Dữ liệu TKU</label>
        </div>
        <!-- <div class="col-sm-2 dialog-control">
            <input id='checkPG' name="checkPG" type='checkbox' class='check-box form-check-input' /> <label class="form-check-label" for="checkPG">Dữ liệu PG</label>
        </div>
        <div class="col-sm-2 dialog-control">
            <input id='checkMKT' name="checkMKT" type='checkbox' class='check-box form-check-input' /> <label class="form-check-label" for="checkMKT">Dữ liệu MKT</label>
        </div> -->
        <?php } ?>

        <div class="col-sm-1 dialog-control">
            <input type="number" name="assign_num" id="assign_num" class="form-control" placeholder="Số khách hàng" value="20" min="1" max="250" step="1" />
        </div>

        <div class="col-sm-3 dialog-control">
            <button type="button" class="btn btn-primary btnSearch" style="height: 31px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Phân bổ</button>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <div id="grid"></div>
</div>

<script type="text/javascript">
    var url = "<?php echo base_url();?>group/customer/assign";
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
                { field: "fullname", title: 'Khách hàng', sortable: false, },
                { field: "mobile", title: 'Số ĐT', sortable: false, width: column_properties._wth_mobile,},
                { field: "source", title: 'source', width: column_properties._wth_source, sortable: false,},
                { field: "created_at", title: 'created_at', width: column_properties._wth_time, sortable: false,},
            ]
        };
        var grid = create_grid(grid_config);
    });
</script>

<script id="toolbar_template" type="text/x-kendo-template">
    <div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
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
        $('#checkAll').prop('checked', false);

        var id_city = $('#id_city').val();
        if( id_city != null && id_city != '' ){
            id_city = id_city.join();
        }else{
            id_city = 0;
        }

        /*var pg = 0;
        if($("#checkPG").is(":checked")){
            pg = 1;
        }

        var mkt = 0;
        if($("#checkMKT").is(":checked")){
            mkt = 1;
        }*/

        var exception = 0;
        if($("#exception").is(":checked")){
            exception = 1;
        }

        var _url = url;
        //_url = _url + '?id_city='+id_city+'&pg='+pg+'&mkt='+mkt;
        _url = _url + '?id_city='+id_city+'&exception='+exception;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }
    // Thay doi thong tin load du lieu
    $(function(){
        $('#id_city').change(function(){
            onLoadGrid();
        });

        $("#exception").change(function(e){
            onLoadGrid();
        });

        /*$("#checkPG").change(function(e){
            onLoadGrid();
        });

        $("#checkMKT").change(function(e){
            onLoadGrid();
        });*/
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
            var _param = '1=1';

            var id_city     = $('#id_city').val();
            if( id_city != null && id_city != '' ){
                id_city = id_city.join();
            }else{
                id_city = 0;
            }

            var id_agent = $('#id_agent').val();
            if( id_agent != null && id_agent != '' ){
                id_agent = id_agent.join();
            }else{
                id_agent = 0;
            }

            var exception = 0;
            if($("#exception").is(":checked")){
                exception = 1;
            }

            var assign_num = $('#assign_num').val();
            if( isNaN( parseInt(assign_num) ) ){
                alert('Số khách hàng phải là số');
                return false;
            }else if(parseInt(assign_num) > 500){
                alert('Số khách hàng phân <= 500');
                return false;
            }

            $.ajax({
                url     : '<?php echo base_url()?>group/customer/acassign',
                type    : "POST",
                data    : { 'id_city':id_city, 'id_agent':id_agent, 'assign_num':assign_num, 'exception':exception },
                success : function( data ) {
                    switch(data){
                        case 'error':
                            alert('Lỗi phân bổ khách hàng 1.')
                            break;
                        case 'fail':
                            alert('Lỗi phân bổ khách hàng 2.')
                            break;
                        default:
                            onLoadGrid();
                            alert('Phân bổ khách hàng thành công.')
                    }
                },
                error   : function(msg){
                    
                }
            });
        });

        $('#id_center').change(function(){
            var id_center = $(this).val();
            if( id_center != null && id_center != '' ){
                id_center     = id_center.join();
            }else{
                id_center = 0;
            }

            var option_department = $("#id_department");
            option_department.empty();
            // Phong
            $.ajax({
                url     : '<?php echo base_url()?>center/ajax/debyce',
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
                    option_group.multiselect('rebuild');
                },

                error   : function(msg){
                    option_department.multiselect('rebuild');
                    option_group.multiselect('rebuild');
                }
            });

            var option_group = $("#id_group");
            option_group.empty();
            option_group.multiselect('rebuild');

            var option_agent = $("#id_agent");
            option_agent.empty();
            option_agent.multiselect('rebuild');
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
            $.ajax({
                url     : '<?php echo base_url()?>center/ajax/grbyde',
                type    : "GET",
                data    : {'id_department':id_department},
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var group = obj_decode;
          
                    for(var key in group){
                        var detail  = group[key];
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

            var option_agent = $("#id_agent");
            option_agent.empty();
            option_agent.multiselect('rebuild');
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
                url     : '<?php echo base_url()?>center/ajax/agbygr',
                type    : "GET",
                data    : { 'id_group':id_group },
                success : function( data ) {
                    var obj_decode = $.parseJSON(data);
                    var agent = obj_decode;
                  
                    for(var key in agent){
                        var detail  = agent[key];
                        var id      = detail['id'];
                        var full_name    = detail['full_name'];
                        var ext    = detail['ext'];
                        option_agent.append($("<option />").val(id).text(ext+'-'+full_name));
                    }
                    option_agent.multiselect('rebuild');
                },

                error   : function(msg){
                    option_agent.multiselect('rebuild');
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
                url     : '<?php echo base_url()?>center/ajax/fibyso',
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