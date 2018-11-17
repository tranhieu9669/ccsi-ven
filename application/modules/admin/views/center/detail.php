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
      		echo '<label class="control-label col-sm-2 dialog-control"></label>';
      		echo '<div class="col-sm-10 dialog-control">';
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
      		echo '<label class="control-label col-sm-2 dialog-control"></label>';
      		echo '<div class="col-sm-10 dialog-control">';
        		echo '<div class="alert alert-warning" role="alert">';
          			echo '<strong>Warning</strong> ' . $error;
        		echo '</div>';
      		echo '</div>';
    	echo '</div>';
  	}
  	?>
  
  	<div class="form-group">
    	<label class="control-label col-sm-2 dialog-control" for="pwd">Mã trung tâm </label>
    	<div class="col-sm-10 dialog-control">
        <input type="hidden" name="hdcode" value="<?php echo set_value_input('code', $detail);?>">
      	<input type="text" name="code" class="form-control" placeholder="Mã trung tâm" value="<?php echo set_value_input('code', $detail);?>">
      	<span class="error"><?php echo form_error('code'); ?></span>
    	</div>
  	</div>

  	<div class="form-group">
    	<label class="control-label col-sm-2 dialog-control" for="pwd">Tên trung tâm </label>
    	<div class="col-sm-10 dialog-control">
      		<input type="text" name="name" class="form-control" placeholder="Tên trung tâm" value="<?php echo set_value_input('name', $detail);?>">
      		<span class="error"><?php echo form_error('name'); ?></span>
    	</div>
  	</div>

  	<div class="form-group">
    	<label class="control-label col-sm-2 dialog-control" for="pwd">Đỉa chỉ </label>
    	<div class="col-sm-10 dialog-control">
      		<input type="text" name="address" class="form-control" placeholder="Đỉa chỉ trung tâm" value="<?php echo set_value_input('address', $detail);?>">
          <span class="error"><?php echo form_error('address'); ?></span>
    	</div>
  	</div>

    <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Đỉa chỉ SMS </label>
      <div class="col-sm-10 dialog-control">
          <input type="text" name="addresssms" class="form-control" placeholder="Đỉa chỉ SMS" value="<?php echo set_value_input('addresssms', $detail);?>">
          <span class="error"><?php echo form_error('addresssms'); ?></span>
      </div>
    </div>

    <?php
    $type = 'call';
    if( isset($detail['type']) AND !empty($detail['type']) ){
      $type = $detail['type'];
    }
    ?>

    <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Tỉnh/T.Phố </label>
      <div class="col-sm-4 dialog-control">
        <select id="id_city" name="id_city" class="form-control-select">
          <option value="">Chọn Tỉnh/T.Phố</option>
          <?php
          if( isset($city) AND !empty($city) ){
            foreach ($city as $key => $value) {
              if( isset($detail['id_city']) AND $detail['id_city'] == $value['id'] ){
                echo '<option value="'.$value['id'].'" selected="selected"> '.$value['name'].'</option>';
              }else{
                echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
              }
            }
          }
          ?>
        </select>
        <span class="error"><?php echo form_error('id_city'); ?></span>
      </div>

      <label class="control-label col-sm-2 dialog-control" for="pwd">Loại trung tâm </label>
      <div class="col-sm-4 dialog-control">
        <select id="type" name="type" class="form-control-select">
          <option value="call" <?php if( $type == 'call' ){ echo 'selected="selected"'; } ?>> Trung tâm Call</option>
          <option value="spa" <?php if( $type == 'spa' ){ echo 'selected="selected"'; } ?>> Trung tâm Spa</option>
        </select>
      </div>
    </div>

    <div id="centerinfo">
      <div class="form-group">
        <label class="control-label col-sm-2 dialog-control" for="pwd">Đầu số </label>
        <div class="col-sm-4 dialog-control">
          <input type="hidden" name="hdfirst_ext" value="<?php echo set_value_input('first_ext', $detail);?>">
          <input type="number" name="first_ext" class="form-control" placeholder="Đầu số" value="<?php echo set_value_input('first_ext', $detail);?>" min="1" max="9" step="1" />
          <span class="error"><?php echo form_error('first_ext'); ?></span>
        </div>

        <label class="control-label col-sm-2 dialog-control" for="pwd">Port </label>
        <div class="col-sm-4 dialog-control">
            <input type="text" name="port" class="form-control" placeholder="Vị trí" value="<?php echo set_value_input('port', $detail);?>" min="1" max="100" step="1" />
            <span class="error"><?php echo form_error('port'); ?></span>
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-sm-2 dialog-control" for="pwd">Host </label>
        <div class="col-sm-4 dialog-control">
          <input type="text" name="host" class="form-control" placeholder="Host" value="<?php echo set_value_input('host', $detail);?>"/>
          <span class="error"><?php echo form_error('host'); ?></span>
        </div>

        <label class="control-label col-sm-2 dialog-control" for="pwd">DB Name </label>
        <div class="col-sm-4 dialog-control">
            <input type="text" name="dbname" class="form-control" placeholder="DB Name" value="<?php echo set_value_input('dbname', $detail);?>"/>
            <span class="error"><?php echo form_error('dbname'); ?></span>
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-sm-2 dialog-control" for="pwd">Username </label>
        <div class="col-sm-4 dialog-control">
          <input type="text" name="user" class="form-control" placeholder="Username" value="<?php echo set_value_input('user', $detail);?>"/>
          <span class="error"><?php echo form_error('user'); ?></span>
        </div>

        <label class="control-label col-sm-2 dialog-control" for="pwd">Password </label>
        <div class="col-sm-4 dialog-control">
            <input type="text" name="pass" class="form-control" placeholder="Password" value="<?php echo set_value_input('pass', $detail);?>"/>
            <span class="error"><?php echo form_error('pass'); ?></span>
        </div>
      </div>
    </div>

  	<div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10 dialog-control">
        	<button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
    	</div>
  	</div>
</form>

<script type="text/javascript">
	$('#status').select2({
    closeOnSelect: true
  });
  $('#type').select2({
    closeOnSelect: true
  });
  $('#id_city').select2({
    closeOnSelect: true
  });

  $(function(){
    var type = $('#type').val();
    if(type == 'call'){
      $('#centerinfo').css('display', 'block');
    }else{
      $('#centerinfo').css('display', 'none');
    }

    $('#type').change(function(){
      var val = $(this).val();
      if(val == 'call'){
        $('#centerinfo').css('display', 'block');
      }else{
        $('#centerinfo').css('display', 'none');
      }
    });
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