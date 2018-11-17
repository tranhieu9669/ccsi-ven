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

    /*dropdown-menu*/
    .multiselect-container{
        height: 250px;
        overflow-y: scroll;
    }

    #uploading{
        position: absolute;
        top: 0px;
        left: 0px;
        background: #000;
        opacity: .6;
        z-index: 99999999999;
        display:none;
    }

    .loadCenter {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 25%;
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

        $('html').append('<div id="uploading"><img class="loadCenter" src="<?php echo base_url();?>/assets/img/loading.gif"></div>');
        var docHeight = $(document).height();
        var docWidth = $(document).width();
        $('#uploading').css('width', docWidth + 'px');
        $('#uploading').css('height', docHeight + 'px');
    });
</script>

<div class="form-horizontal">
    <div class="form-group">
        <div class="col-sm-2 dialog-control">
            <select id="id_center" name="id_center" class="form-control-select">
                <option value=""> Chọn Trung tâm</option>
                <?php
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

        <div class="col-sm-1 dialog-control">
            <input id='checkPG' name="checkPG" type='checkbox' class='check-box form-check-input' /><label class="form-check-label" for="checkPG">PG</label>
        </div>

        <div class="col-sm-1 dialog-control">
            <input id='checkMKT' name="checkMKT" type='checkbox' class='check-box form-check-input' /><label class="form-check-label" for="checkMKT">LEAD</label>
        </div>

        <div class="col-sm-1 dialog-control">
            <input id='demo6' name="demo6" type='checkbox' class='check-box form-check-input' /><label class="form-check-label" for="demo6">DEMO</label>
        </div>

        <div class="col-sm-1 dialog-control">
            <input id='newData' name="newData" type='checkbox' class='check-box form-check-input' /><label class="form-check-label" for="newData">New</label>
        </div>

        <div class="col-sm-1 dialog-control">
            <input type="number" name="assign_num" id="assign_num" class="form-control" placeholder="Số khách hàng" value="20" min="1" max="2500" step="1" />
        </div>
        <div class="col-sm-1 dialog-control">
            <button type="button" class="btn btn-primary btnSearch" style="height: 30px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Assign</button>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <div id="grid"></div>
</div>

<script type="text/javascript">
    var url = "<?php echo base_url();?>data/assignAuto";
    $(function() {
        $('#uploading').fadeIn();

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
                { field: "mobile", title: 'Số ĐT', sortable: false, width: 120, },
                { field: "souName", title: 'source', width: 220, sortable: false, }
            ]
        };
        var grid = create_grid(grid_config);

        setTimeout(function () {
            $('#uploading').fadeOut("slow");
        }, 1000);
    });
</script>

<script id="toolbar_template" type="text/x-kendo-template">
    <div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
                <li>
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

        var id_center = $('#id_center').val();
        if(typeof id_center == 'undefined' || id_center == '' ){
            id_center = 0;
        }

        var id_source   = $("#id_source").val();
        if(  id_source != null && id_source != '' ){
            id_source = id_source.join();
        }else{
            id_source = 0;
        }

        var pg = 0;
        if($("#checkPG").is(":checked")){
            pg = 1;
        }

        var mkt = 0;
        if($("#checkMKT").is(":checked")){
            mkt = 1;
        }

        var demo6 = 0;
        if($("#demo6").is(":checked")){
            demo6 = 1;
        }

        var newData = 0;
        if($("#newData").is(":checked")){
            newData = 1;
        }

        var _url = url;
        _url = _url + '?id_center='+id_center+'&id_source='+id_source+'&pg='+pg+'&mkt='+mkt+'&newData='+newData+"&demo6="+demo6;

        $('#uploading').fadeIn();
        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();

        setTimeout(function () {
            $('#uploading').fadeOut("slow");
        }, 1000);
        return false;
    }

    $("#id_center").select2({
        closeOnSelect: true
    });
    // Thay doi thong tin load du lieu
    $(function(){
        $('#id_center').change(function(){
            onLoadGrid();
        });

        $('#id_source').change(function(){
            onLoadGrid();
        });

        $("#checkPG").change(function(){
            console.log('PG');
            onLoadGrid();
        });

        $("#checkMKT").change(function(){
            console.log('MKT');
            onLoadGrid();
        });

        $("#demo6").change(function(){
            console.log('Demo');
            onLoadGrid();
        });

        $("#newData").change(function(){
            console.log('Data');
            onLoadGrid();
        });
    });

    // check all checkbox
    /*$(function(){
        $('#checkAll').change(function () {
            if( $(this).is(':checked') ) {
                $('.check-box-item').prop('checked', true);
            } else {
                $('.check-box-item').prop('checked', false);
            }
        });
    });*/

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

            var id_center = $('#id_center').val();
            if(typeof id_center == 'undefined' || id_center == '' ){
                alert('Chọn trung tâm');
                return false;
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

            var pg = 0;
            if($("#checkPG").is(":checked")){
                pg = 1;
            }

            var mkt = 0;
            if($("#checkMKT").is(":checked")){
                mkt = 1;
            }

            var demo6 = 0;
            if($("#demo6").is(":checked")){
                demo6 = 1;
            }

            var newData = 0;
            if($("#newData").is(":checked")){
                newData = 1;
            }

            $('#uploading').fadeIn();
            $.ajax({
                url: '<?php echo base_url()?>data/assignAuto/acassign',
                type: "POST",
                async: false,
                data: { 
                            'id_center':id_center,
                            'id_source':id_source,
                            'assign_num':assign_num,
                            'pg':pg,
                            'mkt':mkt,
                            'newData': newData,
                            'demo6': demo6,
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

            setTimeout(function () {
                $('#uploading').fadeOut("slow");
            }, 100);
        });
    });
</script>