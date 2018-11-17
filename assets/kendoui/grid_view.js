var limit_page  = 20;
var grid_number = 0;
var interval_loading;

function refresh_grid(target) {
    $(target).data("kendoGrid").dataSource.read();
    $(target).data('kendoGrid').refresh();
}

function create_grid(grid_config){
    if (grid_config.hasOwnProperty('target')) {
        var grid_target = grid_config.target;
    } else {
        return false;
    }

    if (grid_config.hasOwnProperty('url')) {
        var grid_url = grid_config.url;
    } else {
        return false;
    }

    if (grid_config.hasOwnProperty('columns')) {
        var grid_columns = grid_config.columns;
    } else {
        return false;
    }

    if (grid_config.hasOwnProperty('height')) {
        var grid_height = grid_config.height;
    } else {
        var grid_height = '';
    }

    if (grid_config.hasOwnProperty('limit')) {
        var grid_limit = grid_config.limit;
    } else {
        var grid_limit = '';
    }

    if (grid_config.hasOwnProperty('toolbar_template')) {
        var toolbar_template = grid_config.toolbar_template;
        var grid_instance = $(grid_target).kendoGrid({
            dataSource: {
                url : grid_url,
                transport: {
                    read:function (options){
                        $.ajax({
                            type : "GET",
                            data : options.data,
                            url  : this.dataSource.options.url,
                            beforeSend: function() {
                                /*interval_loading = setInterval(function(){
                                    kendo.ui.progress($(grid_target), true);
                                }, 100);*/
                                
                            },
                            success: function(result) {
                                var objresult =  eval('(' + result + ')');
                                options.success( objresult );
                            },
                            complete: function() {
                                //clearInterval(interval_loading);
                            }
                        });
                        cache: false
                    }
                    /*{
                        dataType : "JSON",
                        type : "GET",
                        url : grid_url
                    }*/
                },
                schema: {
                    data: function(data) {
                        return data.data;
                    },
                    total :function(data) {
                        return data.total;
                    }
                    /*data: "data",
                    total: "total"*/
                },
                pageSize: grid_limit,
                serverPaging: true,
                serverFiltering: false,
                serverSorting: false
            },
            columns: grid_columns,
            //toolbar:[“excel”]
            //toolbar: kendo.template($("#toolbar_template").html()),
            toolbar: kendo.template($("#" + toolbar_template).html()),
            selectable: "row",
            filterable: false,
            groupable: false, // Kendo grid grouping message
            scrollable: true,
            reorderable: true,
            columnMenu: false,
            sortable: {
                mode: "multiple",
                allowUnsort: true
            },
            navigatable: true,
            height: grid_height,
            pageable: {
                pageSize    : grid_limit,
                //pageSizes   : [15, 50, 100],
                refresh     : true,
                messages    : {
                    display : "Hiện thị {0} - {1} Trong số {2}",
                    empty   : "Không có dữ liệu hiện thị",
                    page    : "Trang",
                    of      : "of {0}", //{0} is total amount of pages
                    itemsPerPage: "Bản ghi trên trang",
                    first   : "Trang đầu tiên",
                    previous: "Trang trước",
                    next    : "Trang tiếp theo",
                    last    : "Trang cuối cùng",
                    refresh : "Làm mới"
                }
            },
            dataBinding: function(e) {
                var grid     = $(grid_target).data("kendoGrid");
                _grid_limit  = grid.dataSource.pageSize();
                current_page = grid.dataSource.page();
                grid_number  = (current_page - 1) * _grid_limit;
            }
        });

        return grid_instance;
    } else {
        var grid_instance = $(grid_target).kendoGrid({
            dataSource: {
                url : grid_url,
                transport: {
                    read:function (options){
                        $.ajax({
                            type : "GET",
                            data : options.data,
                            url  : this.dataSource.options.url,
                            beforeSend: function() {
                                /*interval_loading = setInterval(function(){
                                    kendo.ui.progress($(grid_target), true);
                                }, 100);*/
                                
                            },
                            success: function(result) {
                                var objresult =  eval('(' + result + ')');
                                options.success( objresult );
                            },
                            complete: function() {
                                //clearInterval(interval_loading);
                            }
                        });
                        cache: false
                    }
                    /*{
                        dataType : "JSON",
                        type : "GET",
                        url : grid_url
                    }*/
                },
                schema: {
                    data: function(data) {
                        return data.data;
                    },
                    total :function(data) {
                        return data.total;
                    }
                    /*data: "data",
                    total: "total"*/
                },
                pageSize: grid_limit,
                serverPaging: true,
                serverFiltering: false,
                serverSorting: false
            },
            columns: grid_columns,
            selectable: "row",
            filterable: false,
            groupable: false, // Kendo grid grouping message
            scrollable: true,
            reorderable: true,
            columnMenu: true,
            sortable: {
                mode: "multiple",
                allowUnsort: true
            },
            navigatable: true,
            height: grid_height,
            pageable: {
                pageSize    : grid_limit,
                //pageSizes   : [15, 50, 100],
                refresh     : true,
                messages    : {
                    display : "Hiện thị {0} - {1} Trong số {2}",
                    empty   : "Không có dữ liệu hiện thị",
                    page    : "Trang",
                    of      : "of {0}", //{0} is total amount of pages
                    itemsPerPage: "Bản ghi trên trang",
                    first   : "Trang đầu tiên",
                    previous: "Trang trước",
                    next    : "Trang tiếp theo",
                    last    : "Trang cuối cùng",
                    refresh : "Làm mới"
                }
            },
            dataBinding: function(e) {
                var grid     = $(grid_target).data("kendoGrid");
                _grid_limit  = grid.dataSource.pageSize();
                current_page = grid.dataSource.page();
                grid_number  = (current_page - 1) * _grid_limit;
            }
        });

        return grid_instance;
    }

    
}

/*
 Open Dialog
*/

function open_modal(target, content, title) {
    type     = target.charAt(0);
    if( type != '.' && type != '#' ){
        target = "#" + target;
    }

    var modal = $(target).data('kendoWindow');

    if (typeof (content) != 'undefined') {
        modal.content(content);
    }

    if (typeof (title) != 'undefined') {
        modal.setOptions({
            title: title,
        });
    }
    modal.center();
    modal.open();
}

/*
 Close Dialog
*/

function close_modal(target) {
    var modal = $(target).data('kendoWindow');
    modal.close();
}

/*
 Tao div dialog
*/

function init_modal(target, modal_title, modal_width) {
    selector = target;
    selector = selector.replace('#', '');
    selector = selector.replace('.', '');
    type     = target.charAt(0);
    append   = '';
    if (type == '.') {
        append = '<div class="'+selector+'" style="display: none; left:0px;"></div>';
    } else {
        if (type != '#') {
            target = "#" + target
        }
        append = '<div id="'+selector+'" style="display: none; left:0px;"></div>';
    }

    if ($(target).length == 0) {
        $('body').append(append);
    }

    if ($(target).length > 0) {
        var modal = $(target).kendoWindow({
            actions: ["Close"],
            draggable: false,
            modal: true,
            pinned: false,
            resizable: false,
            width: modal_width,
            title: modal_title ,
            left: '0px',
        }).data("kendoWindow");
        return modal;
    } else {
        console.log('Init modal '+target+' fail');
    }
    return false;
}

/* DOM Ready */
$(function(){
    $('body').on('click', '.sys_modal', function(e){
        e.preventDefault();
        i = $(this);
        $.ajax({
            type: 'GET',
            url: i.attr('href'),
            async: false,
            success: function(data){
                open_modal(i.attr('_modal'), data, i.attr('_title'));
            },
            error: function(err){
                alert(err);
            }
        });
    });
});

/* Delete*/
$(function(){
	$('body').on('click', '.delete-confirm', function(e){
		e.preventDefault();
        i = $(this);
        var id = i.attr('_id');
        var del_url = _url + '/delete/' + id;
        $.ajax({
            type: 'GET',
            url: del_url,
            success: function(data){
            	setTimeout(
        	        function(){
        	            close_modal('#dialog_detail');
        	            refresh_grid('#grid');
        	        },
        	        timeout_dialog
        	    );
            },
            error: function(err){
                alert(err);
            }
        });
    });
});