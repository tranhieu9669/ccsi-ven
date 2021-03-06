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

  	form#formSubmit{
    	padding: 15px 10px;  
  	}

  	input[type="checkbox"], input[type="radio"]{
    	margin: 2px 20px 0px 5px;
  	}
</style>

<form id="formSubmit" class="form-horizontal" role="form" method="POST" action="<?php echo $_SERVER['REQUEST_URI'];?>" autocomplete="off">
  	<?php
  	if( isset($success) AND !empty($success) ){
    	echo '<div class="form-group" id="msg_alert">';
      		echo '<label class="control-label col-sm-3 dialog-control"></label>';
      		echo '<div class="col-sm-9 dialog-control">';
        		echo '<div class="alert alert-success" role="alert">';
          			echo '<strong>Success</strong> ' . $success;
        		echo '</div>';
      		echo '</div>';
    	echo '</div>';

    	echo '<script type="text/javascript">';
    		echo 'setTimeout(function(){';
    			echo 'close_modal("#dialog_infor_detail");';
    			echo 'refresh_grid("#grid_introduced");';
    		echo '}, 2000)';
    	echo '</script>';
  	}

  	if( isset($error) AND !empty($error) ){
    	echo '<div class="form-group">';
      		echo '<label class="control-label col-sm-3 dialog-control"></label>';
      		echo '<div class="col-sm-9 dialog-control">';
        		echo '<div class="alert alert-warning" role="alert">';
          			echo '<strong>Warning</strong> ' . $error;
        		echo '</div>';
      		echo '</div>';
    	echo '</div>';
  	}
  	?>
  
  	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Tên Khách hàng </label>
    	<div class="col-sm-9 dialog-control">
      		<input type="text" id="full_name" name="full_name" class="form-control" placeholder="Tên Khách hàng" value="">
      		<span class="error"><?php echo form_error('full_name'); ?></span>
    	</div>
  	</div>

  	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Số điện thoại </label>
    	<div class="col-sm-9 dialog-control">
      		<input type="text" name="mobile" class="form-control" placeholder="Số điện thoại" value="">
      		<span class="error"><?php echo form_error('mobile'); ?></span>
    	</div>
  	</div>

	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Số tuổi </label>
    	<div class="col-sm-3 dialog-control">
      		<input type="text" name="age" class="form-control" placeholder="Số tuổi" value="">
    	</div>

    	<label class="control-label col-sm-2 dialog-control" for="pwd">Giới tính </label>
  		<div class="col-sm-4 box-row">
	    	<ul class="fieldlist" style="margin:0px;">
	            <li>
                  	<input type="radio" name="gender" id="status_1" value="Male" class="k-radio">
                  	<label class="k-radio-label" for="status_1">Nam</label>
              	</li>
             	<li style="margin-left: 25px;">
                  	<input type="radio" name="gender" id="status_2" value="Female" class="k-radio" checked="checked">
                  	<label class="k-radio-label" for="status_2">Nữ</label>
              	</li>
	        </ul>
	    </div>
  	</div>  	

  	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Đỉa chỉ </label>
    	<div class="col-sm-9 dialog-control">
      		<input type="text" name="address" class="form-control" placeholder="Đỉa chỉ khách hàng" value="">
    	</div>
  	</div>

  	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Mối quan hệ </label>
    	<div class="col-sm-9 dialog-control">
    		<select id="id_relationship" name="id_relationship" class="form-control-select">
    			<option value="0" selected="selected"> Chọn quan hệ</option>
    			<option value="1"> Người thân</option>
    			<option value="2"> Bạn bè</option>
    			<option value="3"> Đồng nghiệp</option>
    		</select>
    	</div>
  	</div>

  	<div class="form-group">
    	<div class="col-sm-offset-3 col-sm-9 dialog-control">
        	<button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
    	</div>
  	</div>
</form>

<script type="text/javascript">
	$("#id_relationship").select2({
      	closeOnSelect: true
  	});

	$('form#formSubmit').on('submit', function (e) {
    	var _data   = $(this).serializeArray();
    	var _action = $(this).attr("action");
    	var _method = $(this).attr("method").toUpperCase();
	    $.ajax({
	        url     : _action,
	        dataType: 'html',
	        type    : _method,
	        data    : _data,  
	        success : function(response, status, jqXHR){
	            $('#dialog_infor_detail').html(response);
	        },
	        error   : function(jqXHR, status, err){
	        },
	        complete: function(jqXHR, status){
	        }
	    });
	    e.preventDefault();
  	});
</script>