<style type="text/css">
    .line-height-search{
        font-weight: bold;
        color: red;
    }

    .grid_info{
        font-weight: bold;
        color: red;
    }
</style>
<script type="text/javascript">
    var calltype = '<?php echo $calltype;?>';
    var url = "<?php echo base_url();?>operator/listcallout";
    if(calltype == 'in'){
        url = "<?php echo base_url();?>operator/listcallin";
    }

    function getinfor($type){
        var _param = '?calltype=' + $type;

        var startdate = $('#startdate').val();
        _param += '&startdate='+startdate;

        var enddate = $('#enddate').val();
        _param += '&enddate='+enddate;

        var inputsearch = $('#text_search').val();
        inputsearch = inputsearch.replace(/ /g, '');
        _param += '&inputsearch='+inputsearch;

        var _url = '<?php echo base_url();?>operator/infordetail' + _param;

        $.ajax({
            type: "GET",
            url: _url,
            data: {},
            success: function(data){
                if(data !== 'SUCCESS'){
                    console.log(data);
                    var obj = $.parseJSON(data);
                    $('#total_call').html(obj.total);
                    var sc = obj.bill%60;
                    if(sc < 1){
                        $('#total_bill').html((obj.bill/60) + ':' + '00');
                    }else{
                        $('#total_bill').html(((obj.bill-sc)/60) + ':' + sc);
                    }
                }else{
                    $('#total_call').html('erro');
                    $('#total_bill').html('erro');
                }
            }
        });
    }

    function ColorTextSearch(inputsearch){
        var txtsearch = $("#text_search").val();
        txtsearch     = txtsearch.replace(/ /g, '');
        if(inputsearch.search(txtsearch) >= 0)
            inputsearch = inputsearch.replace(new RegExp(txtsearch,'g'), "<font class=\"line-height-search\">"+txtsearch+"</font>");
        return inputsearch;
    }

    $(function() {
        var _columns = $.parseJSON('<?php echo $columns; ?>');
        _columns[0]['template'] = function(order){  return grid_number = grid_number + 1; };
        _columns[8]['template'] = function(data){ 
            if(data.disposition == 'ANSWERED'){
                var html = '<a title="File ghi âm" href="http://192.168.1.17/rf.php?uniqid='+data.uniqueid+'" target="_blank" class="btn btn-primary btn_edit btn-default">';
                html += '<span class="glyphicon glyphicon-play" aria-hidden="true"></span>';
                html += '</a>';
                return html;
            }else{
                return '';
            }
        };

        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 50,
            columns: _columns,
        };
        var grid = create_grid(grid_config);
        getinfor(calltype);
    });
</script>

<div id="grid"></div>

<script id="toolbar_template" type="text/x-kendo-template">
    <div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <input id="text_search" class="k-header-input-search" placeholder="Số gọi" type="search" style="line-height: 26px;">
                </li>
                <li>
                    <input type="text" id="startdate" name="startdate" class="form-control date-picker" placeholder="Ngày bắt đầu" value="<?php echo date('Y-m-d');?>">
                </li>
                <li>
                    <input type="text" id="enddate" name="enddate" class="form-control date-picker" placeholder="Ngày kết thúc" value="<?php echo date('Y-m-d');?>">
                </li>
                <li>
                    <button type="button" class="btn btn-primary btnSearch" style="height: 25px;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Tìm kiếm</button>
                </li>
            </ul>
        </span>
        
        <span class="pull-right">
            <ul class="fieldlist" style="float:left;">
                <li>
                    Tổng số cuộc gọi: <span class="grid_info" id="total_call">0</span>
                </li>
                <li>
                     - Thời gian thoại: <span class="grid_info" id="total_bill">0</span>
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    function onLoadGrid(){
        var _param = '?dm=1';
        var startdate = $('#startdate').val();
        _param += '&startdate='+startdate;

        var enddate = $('#enddate').val();
        _param += '&enddate='+enddate;

        var inputsearch = $('#text_search').val();
        inputsearch = inputsearch.replace(/ /g, '');
        _param += '&inputsearch='+inputsearch;

        var _url = url + _param;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();

        getinfor(calltype);
        return false;
    }

    $(function(){
        $("#startdate").kendoDatePicker({
            format : 'yyyy-MM-dd',
            /*change : function(){
                onLoadGrid();
            }*/
        });

        $("#enddate").kendoDatePicker({
            format : 'yyyy-MM-dd',
            /*change : function(){
                onLoadGrid();
            }*/
        });

        $("#text_search").keypress(function(e){
            /*if(e.keyCode == 13){
                onLoadGrid();
            }*/
        });

        $(".btnSearch").click(function(){
            onLoadGrid();
        });

        /*$('#exportData').click(function(){
            var _param = '?dm=1';
            var calltype = $('#calltype').val();
            _param += '&calltype='+calltype;
            var startdate = $('#startdate').val();
            _param += '&startdate='+startdate;
            var inputsearch = $('#text_search').val();
            inputsearch = inputsearch.replace(/ /g, '');
            _param += '&inputsearch='+inputsearch;

            var _url = '<?php echo base_url()."group/call/exlist";?>' + _param;
            $(this).attr('href', _url);
        });*/
    });
</script>