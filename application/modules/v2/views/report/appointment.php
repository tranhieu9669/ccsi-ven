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

        $('#id_frametime').multiselect({
            buttonWidth: '100%',
            nonSelectedText: 'Trạng Ca',
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
    <div class="form-group">
        <div class="col-sm-3 dialog-control">
            <script type="text/javascript">
                $(function(){
                    $('#id_center').change(function(){
                        gridLoad();
                    });
                });
            </script>
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
                    $('#id_frametime').change(function(){
                        gridLoad();
                    });
                });
            </script>
            <select id="id_frametime" name="id_frametime" multiple="multiple" class="form-control-select">
                <?php
                if(isset($frametime) AND !empty($frametime)){
                    foreach ($frametime as $key => $value) {
                        echo '<option value="'.$value['id'].'" > '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>

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
            <input id="text_search" class="k-header-input-search" placeholder="Số điện thoại" style="width:100%" value="" type="search">
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
    var url = "<?php echo base_url();?>listappointment";
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
            'limit': 25,
            'columns': [
            	{ field: "order"        , title: 'No', sortable: false, width: column_properties._wth_order, locked: true, template: function(order){
                    return grid_number = grid_number + 1;
                }},
            	{ field: "fullname", title: 'Khách hàng', width: column_properties._wth_fullname, sortable: false, locked: true,},
                { field: "cus_mobile", title: 'Số ĐT', width: column_properties._wth_mobile, sortable: false, template: '#=ColorTextSearch(cus_mobile)#'},
                { field: "agent_ext", title: 'Ext', width: column_properties._wth_ext, sortable: false,},
                { field: "cname", title: 'Tỉnh/T.Phố', width: column_properties._wth_city, sortable: false,},
                { field: "cename", title: 'Trung tâm', width: column_properties._wth_center, sortable: false,},
                { field: "fname", title: 'Ca', width: column_properties._wth_frame, sortable: false,},
                { field: "app_datetime", title: 'TG Hẹn', width: column_properties._wth_time, sortable: false,},
                { field: "app_content", title: 'Nội dung', width: column_properties._wth_content, sortable: false,},
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
                    
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
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

        var id_frametime = $('#id_frametime').val();
        if( id_frametime != null && id_frametime != '' ){
            id_frametime = id_frametime.join();
            param_url += '&id_frametime='+id_frametime;
        }else{
            id_frametime = 0;
        }

        var id_agent = $('#id_agent').val();
        if( id_agent != null && id_agent != '' ){
            id_agent = id_agent.join();
            param_url += '&id_agent='+id_agent;
        }else{
            id_agent = 0;
        }
        
        var id_source   = $("#id_source").val();
        if( id_source != null && id_source != '' ){
            id_source = id_source.join();
            param_url += '&id_source='+id_source;
        }else{
            id_source = 0;
        }

        var id_fileup = $('#id_fileup').val();
        if( id_fileup != null && id_fileup != '' ){
            id_fileup = id_fileup.join();
            param_url += '&id_fileup='+id_fileup;
        }else{
            id_fileup = 0;
        }

        var startdate = $('#startdate').val();
        if(startdate != ''){
            param_url += '&startdate='+startdate;
        }

        var enddate = $('#enddate').val();
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
</script>