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
                    $('#id_department').multiselect({
                        buttonWidth: '100%',
                        nonSelectedText: 'Chọn Phòng',
                        enableFiltering: true,
                        includeSelectAllOption: true,
                        selectAllJustVisible: false
                    });
                });
            </script>
            <select id="id_department" name="id_department" multiple="multiple" class="form-control-select">
                <?php
                if( isset($department) AND !empty($department) ){
                    foreach ($department as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="col-sm-2 dialog-control">
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
                <option value="999999" > Data giới thiệu</option>
            </select>
        </div>

        <?php if( (strpos($this->_uid, 'linhnh') !== false OR strpos($this->_uid, 'center') !== false) AND ($this->_role == 'center') ){ ?>
        <div class="col-sm-2 dialog-control">
            <input id='checkPG' name="checkPG" type='checkbox' class='check-box form-check-input' /> <label class="form-check-label" for="checkPG">Dữ liệu PG</label>
        </div>
        <div class="col-sm-2 dialog-control">
            <input id='checkMKT' name="checkMKT" type='checkbox' class='check-box form-check-input' /> <label class="form-check-label" for="checkMKT">Dữ liệu MKT</label>
        </div>
        <div class="col-sm-2 dialog-control">
            <input id='checkdemo' name="checkdemo" type='checkbox' class='check-box form-check-input' /> <label class="form-check-label" for="checkdemo">Demo 6T</label>
        </div>
        <?php } ?>
    </div>
</div>

<div class="col-sm-12">
    <div id="grid"></div>
</div>

<script type="text/javascript">
    var url = "<?php echo base_url();?>center/customer/assign";
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
                { field: "mobile", title: 'Số ĐT', sortable: false, width: column_properties._wth_mobile, },
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
                <li>
                    <input id="text_search" class="k-header-input-search" placeholder="Số điện thoại" value="" type="search">
                </li>
                <li>
                    <input type="number" name="assign_num" id="assign_num" class="form-control" placeholder="Số khách hàng" value="20" min="1" max="250" step="1" />
                </li>
                <li style="margin-left: 15px;">
                    <button type="button" class="btn btn-primary btnSearch" style="height: 26px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Phân bổ</button>
                </li>
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
    // Load du lieu grid
    function onLoadGrid(){
        $('#checkAll').prop('checked', false);

        var id_city = $('#id_city').val();
        if( id_city != null && id_city != '' ){
            id_city = id_city.join();
        }else{
            id_city = 0;
        }

        var id_source   = $("#id_source").val();
        if( id_source != null && id_source != '' ){
            id_source = id_source.join();
        }else{
            id_source = 0;
        }

        var id_fileup = $('#id_fileup').val();
        if( id_fileup != null && id_fileup != '' ){
            id_fileup = id_fileup.join();
        }else{
            id_fileup = 0;
        }

        var pg = 0;
        if($("#checkPG").is(":checked")){
            pg = 1;
        }

        var mkt = 0;
        if($("#checkMKT").is(":checked")){
            mkt = 1;
        }

        var demo6t = 0;
        if($("#checkdemo").is(":checked")){
            demo6t = 1;
        }

        var inputsearch = $("#text_search").val();
        inputsearch     = inputsearch.replace(/ /g, '');

        var _url = url;
        _url = _url + '?id_city='+id_city;
        _url = _url + '&id_source='+id_source;
        _url = _url + '&id_fileup='+id_fileup;
        _url = _url + '&inputsearch='+inputsearch;
        _url = _url + '&pg='+pg;
        _url = _url + '&mkt='+mkt;
        _url = _url + '&demo6t='+demo6t;

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

        $("#checkPG").change(function(e){
            onLoadGrid();
        });

        $("#checkMKT").change(function(e){
            onLoadGrid();
        });

        $("#checkdemo").change(function(e){
            onLoadGrid();
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

            var id_city     = $('#id_city').val();
            if( id_city != null && id_city != '' ){
                id_city = id_city.join();
            }else{
                id_city = 0;
            }

            var id_department = $('#id_department').val();
            if( id_department != null && id_department != '' ){
                id_department = id_department.join();
            }else{
                id_department = 0;
            }

            var assign_num = $('#assign_num').val();
            if( isNaN( parseInt(assign_num) ) ){
                alert('Số khách hàng phải là số');
                return false;
            }else if(parseInt(assign_num) > 1500){
                alert('Số khách hàng phân <= 1500');
                return false;
            }

            var id_source   = $("#id_source").val();
            if( id_source != null && id_source != '' ){
                id_source = id_source.join();
            }else{
                id_source = 0;
            }

            var id_fileup = $('#id_fileup').val();
            if( id_fileup != null && id_fileup != '' ){
                id_fileup = id_fileup.join();
            }else{
                id_fileup = 0;
            }

            var pg = 0;
            if($("#checkPG").is(":checked")){
                pg = 1;
            }

            var mkt = 0;
            if($("#checkMKT").is(":checked")){
                mkt = 1;
            }

            var demo6t = 0;
            if($("#checkdemo").is(":checked")){
                demo6t = 1;
            }

            var inputsearch = $("#text_search").val();
            inputsearch     = inputsearch.replace(/ /g, '');

            $.ajax({
                url     : '<?php echo base_url()?>center/customer/acassign',
                type    : "POST",
                data    : { 
                            'hdidselected':hdidselected,
                            'id_city':id_city,
                            'id_department':id_department,
                            'id_source':id_source,
                            'id_fileup':id_fileup,
                            'assign_num':assign_num,
                            'pg':pg,
                            'mkt':mkt,
                            'demo6t':demo6t,
                            'inputsearch':inputsearch,
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