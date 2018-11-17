<style type="text/css">
    .line-height-search{
        font-weight: bold;
        color: red;
    }
</style>
<script type="text/javascript">
    var url = "<?php echo base_url();?>department/request";
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
                { field: "approved_status", title: 'Phê duyệt', width: 120, sortable: false,},
                { field: "approved_at", title: 'T/G duyệt', width: 180, sortable: false,},
                { field: "approved_by", title: 'Người duyệt', width: 150, sortable: false,},
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
                    <a _modal="dialog_infor_detail" _title="Tạo mới yêu cầu hỗ trợ" href="<?php echo base_url();?>department/request/detail" class="sys_modal">
                        <span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Thêm yêu cầu
                    </a>
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
    });

    $( document ).ready(function() {
    });
</script>