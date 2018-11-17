<style type="text/css">
    .alert{
        padding: 8px;
        margin-bottom: 0px;
        border-radius: 0px
    }

    .form-control{
        padding: 0px 0px 0px 3px;
    }

    .dialog-control{
        padding-left: 0px;
        padding-right: 0px;
    }

    .form-horizontal .form-group{
        margin-left: 0px;
        margin-right: 0px;
    }

    .form-horizontal .control-label{
        padding-right: 8px;
        margin-left: -10px;
    }

    .question-box{
        border: 1px solid #ccc;
        min-height: 100px;
        max-height: 175px;
        overflow-y: scroll;
    }

    .checkbox{
        float: left;
        margin-right: 20px;
    }

    .question-box > .checkbox{
        float: left;
        clear: both;
        margin-left: 2px;
    }

    #formSubmit{
        padding: 15px 10px;  
    }

    input[type="checkbox"], input[type="radio"]{
        margin: 2px 20px 0px 5px;
    }
</style>

<form id="formSubmit" class="form-horizontal form-top" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
	<!-- <div class="form-group">
        <label class="control-label col-sm-3 dialog-control" for="text1">Extract file</label>
        <div class="col-sm-9 dialog-control">
        	<progress id="progressor" max="100" value="0" class="form-control"></progress>
        </div>
    </div> -->
    <div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="text1">Extract file</label>
        <div class="col-sm-9 dialog-control">
		    <!-- Progress bar holder -->
			<div id="progress" class="form-control" style="padding:0px;"></div>
			<!-- Progress information -->
			<div id="information"></div>
		</div>
	</div>

	<div class="form-group">
        <div class="col-sm-offset-3 col-sm-9 dialog-control">
            <button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Upload File</button>
        </div>
    </div>
</form>

<script type="text/javascript">
	var maxrow=0;
    $('form#formSubmit').on('submit', function (e) {
        var _data   = $(this).serializeArray();

        var _action = $(this).attr("action");
        var _method = $(this).attr("method").toUpperCase();
        $.ajax({
        	/*xhr: function() {
		        var xhr = new window.XMLHttpRequest();
		        // Upload progress
		        xhr.upload.addEventListener("progress", function(evt){
		            if (evt.lengthComputable) {
		                var percentComplete = evt.loaded / evt.total;
		                //Do something with upload progress
		                console.log(percentComplete);
		            }
		       	}, false);

		       // Download progress
		       	xhr.addEventListener("progress", function(evt){
		       		console.log(maxrow);
		           	if (evt.lengthComputable) {
		               var percentComplete = evt.loaded / evt.total;
		               // Do something with download progress
		               console.log(percentComplete);
		           	}
		       	}, false);

		      	return xhr;
		    },*/
            url     : _action,
            dataType: 'html',
            type    : _method,
            data    : _data,
            success : function(response, status, jqXHR){
                $('#dialog_infor_detail').html(response);
            },
            error   : function(jqXHR, status, err){
            },
            //beforeSend : function(),
            complete: function(jqXHR, status){
            }
        });

        e.preventDefault();
    });
</script>