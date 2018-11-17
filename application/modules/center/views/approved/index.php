<style type="text/css">
    .line-height-search{
        font-weight: bold;
        color: red;
    }
</style>

<script type="text/javascript">
    function Approved(id){
        $.ajax({
            url: '<?php echo base_url()?>center/approved/action',
            type: "POST",
            data: { 
                'id':id,
                'status':'approved'
            },
            success : function( data ) {
                
            },
            error   : function(msg){
                
            }
        });
        onLoadGrid();
    }

    function nonApproved(id){
        $.ajax({
            url: '<?php echo base_url()?>center/approved/action',
            type: "POST",
            data: { 
                'id':id,
                'status':'cancel'
            },
            success : function( data ) {
                
            },
            error   : function(msg){
                
            }
        });
        onLoadGrid();
    }
</script>

<script type="text/javascript">
    var url = "<?php echo base_url();?>center/approved";
    $(function() {
        var grid_config = {
            'target': '#grid',
            'url': url,
            'toolbar_template': 'toolbar_template',
            'limit': 20,
            'columns': [
            	{ field: "order", title: 'No', sortable: false, width: 50, template: function(order){
                    return grid_number = grid_number + 1;
                }},
                { field: "mobile", title: 'Điện thoại', width: 120, sortable: false,},
                { field: "request_at", title: 'T/G tạo', width: 180, sortable: false,},
                { field: "name", title: 'Danh mục', sortable: false,},
                { field: "approved_at", title: 'T/G duyệt', width: 180, sortable: false,},
                { field: "approved", title: 'Duyệt', width: 60, sortable: false,template: function(data){
                    if(data.approved_status != null){
                        if(data.approved_status == 'approved'){
                            return "<input type='checkbox' value='" + data.id + "' name='Approved' checked class='check-box-item' disabled />";
                        }else{
                            return "<input type='checkbox' value='" + data.id + "' name='Approved' class='check-box-item' disabled />";
                        }
                    }else{
                        return "<input type='checkbox' value='" + data.id + "' name='Approved' onclick='Approved("+data.id+");' class='check-box-item' />";
                    }
                }},
                { field: "approved", title: 'Hủy', width: 60, sortable: false,template: function(data){
                    if(data.approved_status != null){
                        if(data.approved_status == 'cancel'){
                            return "<input type='checkbox' value='" + data.id + "' name='nonApproved' checked class='check-box-item' disabled/>";
                        }else{
                            return "<input type='checkbox' value='" + data.id + "' name='nonApproved' class='check-box-item' disabled/>";
                        }
                    }else{
                        return "<input type='checkbox' value='" + data.id + "' name='nonApproved' onclick='nonApproved("+data.id+");' class='check-box-item' />";
                    }
                }},
            ]
        };
        var grid = create_grid(grid_config);
    });
</script>

<div id="grid"></div>

<script id="toolbar_template" type="text/x-kendo-template">
	<div>
        <span class="pull-left">
            <ul class="fieldlist" style="float:left;">
                <li>
                    <input id="text_search" class="k-header-input-search" placeholder="Điện thoại hoặc tên" type="search" style="line-height: 26px;">
                </li>
                <li style="width:220px;">
                    <select id="id_categories" name="id_categories" class="form-control-select">
                        <option value="0" > Danh mục yêu cầu</option>
                        <?php
                        if( isset($categories) AND !empty($categories) ){
                            foreach ($categories as $key => $value) {
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
                    
                </li>
            </ul>
        </span>
    </div>
</script>

<script type="text/javascript">
    function onLoadGrid(){
        var _param = '?dm=1';
        var inputsearch = $('#text_search').val();
        inputsearch = inputsearch.replace(/ /g, '');
        _param += '&inputsearch='+inputsearch;

        var id_categories = $('#id_categories').val();
        _param += '&id_categories='+id_categories;

        var _url = url + _param;

        var _grid = $("#grid").data("kendoGrid");
        _grid.dataSource.options.url = _url;
        _grid.dataSource.read();
        _grid.refresh();
        return false;
    }

    $(function(){
        $("#id_categories").select2({
            closeOnSelect: true
        });

        $("#text_search").keypress(function(e){
            if(e.keyCode == 13){
                onLoadGrid();
            }
        });

        $('#id_categories').change(function(){
            onLoadGrid();
        });

        $('input[name="Approved"]').click(function(){
            alert(1);
            if($(this).is(':checked')){
                alert(1);
            }else{
                alert(2)
            }
        });

        $('input[name="nonApproved"]').change(function(){
            if($(this).is(':checked')){
                alert(3);
            }else{
                alert(4)
            }
        });
    });
</script>