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
    			echo 'refresh_grid("#grid");';
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
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Mã trung tâm </label>
    	<div class="col-sm-9 dialog-control">
        <input type="hidden" name="hdcode" value="<?php echo set_value_input('code', $detail);?>">
      	<input type="text" name="code" class="form-control" placeholder="Mã trung tâm" value="<?php echo set_value_input('code', $detail);?>">
      	<span class="error"><?php echo form_error('code'); ?></span>
    	</div>
  	</div>

  	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Tên trung tâm </label>
    	<div class="col-sm-9 dialog-control">
      		<input type="text" name="name" class="form-control" placeholder="Tên trung tâm" value="<?php echo set_value_input('name', $detail);?>">
      		<span class="error"><?php echo form_error('name'); ?></span>
    	</div>
  	</div>

  	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Đỉa chỉ </label>
    	<div class="col-sm-9 dialog-control">
      		<input type="text" name="address" class="form-control" placeholder="Đỉa chỉ trung tâm" value="<?php echo set_value_input('address', $detail);?>">
          <span class="error"><?php echo form_error('address'); ?></span>
    	</div>
  	</div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Đỉa chỉ SMS </label>
      <div class="col-sm-9 dialog-control">
          <input type="text" name="addresssms" class="form-control" placeholder="Đỉa chỉ SMS" value="<?php echo set_value_input('addresssms', $detail);?>">
          <span class="error"><?php echo form_error('addresssms'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Đầu số </label>
      <div class="col-sm-9 dialog-control">
        <input type="hidden" name="hdfirst_ext" value="<?php echo set_value_input('first_ext', $detail);?>">
        <input type="number" name="first_ext" class="form-control" placeholder="Đầu số" value="<?php echo set_value_input('first_ext', $detail);?>" min="1" max="9" step="1" />
        <span class="error"><?php echo form_error('first_ext'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Vị trí </label>
      <div class="col-sm-9 dialog-control">
          <input type="number" name="position" class="form-control" placeholder="Vị trí" value="<?php echo set_value_input('position', $detail);?>" min="1" max="100" step="1" />
          <span class="error"><?php echo form_error('position'); ?></span>
      </div>
    </div>

    <?php
    $status = 'off';
    if( isset($detail['status']) AND !empty($detail['status']) ){
      $status = $detail['status'];
    }
    ?>
  	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Trạng thái </label>
    	<div class="col-sm-9 dialog-control">
    		<select id="status" name="status" class="form-control-select">
    			<option value="on" <?php if( $status == 'on' ){ echo 'selected="selected"'; } ?>> Hoạt động</option>
    			<option value="off" <?php if( $status == 'off' ){ echo 'selected="selected"'; } ?>> Không hoạt động</option>
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
	$('#status').select2({
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