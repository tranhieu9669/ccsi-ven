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

        <div class="col-sm-3 dialog-control">
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
            <select id="id_group" name="id_group" multiple="multiple" class="form-control-select">
                <?php
                if( isset($group) AND !empty($group) ){
                    foreach ($group as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>

        <?php if( in_array($this->_id_department, array(6)) AND ($this->_role == 'department') ){ ?>
        <div class="col-sm-2 dialog-control">
            <input id='checkdemo' name="checkdemo" type='checkbox' class='check-box form-check-input' /> <label class="form-check-label" for="checkdemo">Demo 6T</label>
        </div>
        <?php } ?>

        <div class="col-sm-1 dialog-control">
            <input type="number" name="assign_num" id="assign_num" class="form-control" placeholder="Số khách hàng" value="20" min="1" max="250" step="1" />
        </div>

        <div class="col-sm-2 dialog-control">
            <button type="button" class="btn btn-primary btnSearch" style="height: 31px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Phân bổ</button>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <div id="grid"></div>
</div>

<script type="text/javascript">
    var url = "<?php echo base_url();?>department/customer/assign";
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
                /*{ field: "mobile", title: 'Số ĐT', sortable: false, width: column_properties._wth_mobile, template: '#=ColorTextSearch(mobile)#'},*/
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
                <li style="width:200px;">
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
        $('#checkAll').prop('checked', false);

        var id_city = $('#id_city').val();
        if( id_city != null && id_city != '' ){
            id_city = id_city.join();
        }else{
            id_city = 0;
        }

        var id_source = $('#id_source').val();

        var demo6t = 0;
        if($("#checkdemo").is(":checked")){
            demo6t = 1;
        }

        var _url = url;
        _url = _url + '?id_city='+id_city;
        _url = _url + '&id_source='+id_source;
        _url = _url + '&demo6t='+demo6t;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }
    // Thay doi thong tin load du lieu
    $(function(){
        $("#id_source").select2({
            closeOnSelect: true
        });

        $('#id_city').change(function(){
            onLoadGrid();
        });

        $('#id_source').change(function(){
            onLoadGrid();
        });

        $("#checkdemo").change(function(){
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
            var _param = '1=1';

            var id_city     = $('#id_city').val();
            if( id_city != null && id_city != '' ){
                id_city = id_city.join();
            }else{
                id_city = 0;
            }

            var id_group = $('#id_group').val();
            if( id_group != null && id_group != '' ){
                id_group = id_group.join();
            }else{
                id_group = 0;
            }

            var id_source = $('#id_source').val();

            var demo6t = 0;
            if($("#checkdemo").is(":checked")){
                demo6t = 1;
            }

            var assign_num = $('#assign_num').val();
            if( isNaN( parseInt(assign_num) ) ){
                alert('Số khách hàng phải là số');
                return false;
            }else if(parseInt(assign_num) > 750){
                alert('Số khách hàng phân <= 750');
                return false;
            }

            $.ajax({
                url     : '<?php echo base_url()?>department/customer/acassign',
                type    : "POST",
                data    : { 
                    'id_city':id_city,
                    'id_group':id_group,
                    'id_source':id_source,
                    'assign_num':assign_num,
                    'demo6t':demo6t
                },
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

        /*$('#id_source').change(function(){
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
        });*/
    });
</script>