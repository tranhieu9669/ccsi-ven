<style type="text/css">
	.k-grid tbody .k-button, .k-ie8 .k-grid tbody button.k-button{
		min-width: 33px;
	}
	.k-button-icontext .k-icon, .k-button-icontext .k-image{
		margin: 0px;
	}
</style>
<div id="example">
    <div id="grid"></div>
</div>
<script>
    $(document).ready(function () {
        var crudServiceBaseUrl = "<?php echo base_url();?>v2/test";
        var dataSource = new kendo.data.DataSource({
        		url : crudServiceBaseUrl,
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
                    },
                    update: function (options){
                    	$.ajax({
                            type : "POST",
                            data : options.data.models[0],
                            url  : crudServiceBaseUrl + "/update",
                            //dataType: "jsonp",
                            success: function(result) {
                            	options.success();
                            },
                        });
                    },
                    destroy: {
                        url: crudServiceBaseUrl + "/destroy",
                        dataType: "jsonp"
                    },
                    create: {
                        url: crudServiceBaseUrl + "/create",
                        dataType: "jsonp"
                    },
                    parameterMap: function(options, operation) {
                        if (operation !== "read" && options.models) {
                            return {models: kendo.stringify(options.models)};
                        }
                    }
                },
                batch: true,
                pageSize: 20,
                schema: {
                    model: {
                        id: "ProductID",
                        fields: {
                            ProductID: { editable: false, nullable: true },
                            ProductName: { validation: { required: true } },
                            UnitPrice: { type: "number", validation: { required: true, min: 1} },
                            Discontinued: { type: "boolean" },
                            UnitsInStock: { type: "number", validation: { min: 0, required: true } }
                        }
                    },
                    data: function(data) {
                        return data.data;
                    },
                    total :function(data) {
                        return data.total;
                    },
                }
            });

        $("#grid").kendoGrid({
            dataSource: dataSource,
            navigatable: true,
            pageable: true,
            height: 550,
            toolbar: ["create"],
            columns: [
                "ProductName",
                { field: "UnitPrice", title: "Unit Price", format: "{0:c}", width: 120 },
                { field: "UnitsInStock", title: "Units In Stock", width: 120 },
                { field: "Discontinued", width: 120 },
                { command: [ 
                	{ name:"edit", text:{edit: "", update: "", cancel: ""} },
                	{ name:"destroy", text:"" } 
                	], title: "action", width: "100px" }
            ],
            //editable: true,
            editable: "inline"
        });
    });
</script>