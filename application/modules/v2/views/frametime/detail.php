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
    <!-- <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Trung tâm </label>
      <div class="col-sm-10 dialog-control">
        <select id="id_center" name="id_center" class="form-control-select">
          <option value="" > Chọn Trung tâm</option>
          <?php
          if( isset($center) AND !empty($center) ){
            foreach ($center as $key => $value) {
              if( isset($detail['id_center']) AND $detail['id_center'] == $value['id'] ){
                echo '<option value="' . $value['id'] . '" selected="selected" > ' . $value['name'] . '</option>';
              }else{
                echo '<option value="' . $value['id'] . '" > ' . $value['name'] . '</option>';
              }
            }
          }
          ?>
        </select>
        <span class="error"><?php echo form_error('id_center'); ?></span>
      </div>
    </div> -->

    <div id="group_frame">
    	<div class="form-group">
      	<label class="control-label col-sm-2 dialog-control" for="pwd">Tên ca </label>
      	<div class="col-sm-2 dialog-control">
        	<input type="text" name="name[]" class="form-control" placeholder="Tên ca" value="">
      	</div>

        <label class="control-label col-sm-2 dialog-control" for="pwd">T/G Từ </label>
        <div class="col-sm-2 dialog-control">
          <select name="starttime[]" class="form-control-select">
            <option value="08:00"> 08:00</option>
            <option value="08:30"> 08:30</option>
            <option value="09:00"> 09:00</option>
            <option value="09:30"> 09:30</option>
            <option value="10:00"> 10:00</option>
            <option value="10:30"> 10:30</option>
            <option value="11:00"> 11:00</option>
            <option value="11:30"> 11:30</option>
            <option value="12:00"> 12:00</option>
            <option value="12:30"> 12:30</option>
            <option value="13:00"> 13:00</option>
            <option value="13:30"> 13:30</option>
            <option value="14:00"> 14:00</option>
            <option value="14:30"> 14:30</option>
            <option value="15:00"> 15:00</option>
            <option value="15:30"> 15:30</option>
            <option value="16:00"> 16:00</option>
            <option value="16:30"> 16:30</option>
            <option value="17:00"> 17:00</option>
            <option value="17:30"> 17:30</option>
            <option value="18:00"> 18:00</option>
            <option value="18:30"> 18:30</option>
            <option value="19:00"> 19:00</option>
            <option value="19:30"> 19:30</option>
            <option value="20:00"> 20:00</option>
            <option value="20:30"> 20:30</option>
            <option value="21:00"> 21:00</option>
            <option value="21:30"> 21:30</option>
            <option value="22:00"> 22:00</option>
          </select>
          <!-- <span class="error"><?php echo form_error('starttime[]'); ?></span> -->
        </div>

        <label class="control-label col-sm-2 dialog-control" for="pwd">Đến hết</label>
        <div class="col-sm-2 dialog-control">
          <select name="endtime[]" class="form-control-select">
            <option value="08:00"> 08:00</option>
            <option value="08:30"> 08:30</option>
            <option value="09:00"> 09:00</option>
            <option value="09:30"> 09:30</option>
            <option value="10:00"> 10:00</option>
            <option value="10:30"> 10:30</option>
            <option value="11:00"> 11:00</option>
            <option value="11:30"> 11:30</option>
            <option value="12:00"> 12:00</option>
            <option value="12:30"> 12:30</option>
            <option value="13:00"> 13:00</option>
            <option value="13:30"> 13:30</option>
            <option value="14:00"> 14:00</option>
            <option value="14:30"> 14:30</option>
            <option value="15:00"> 15:00</option>
            <option value="15:30"> 15:30</option>
            <option value="16:00"> 16:00</option>
            <option value="16:30"> 16:30</option>
            <option value="17:00"> 17:00</option>
            <option value="17:30"> 17:30</option>
            <option value="18:00"> 18:00</option>
            <option value="18:30"> 18:30</option>
            <option value="19:00"> 19:00</option>
            <option value="19:30"> 19:30</option>
            <option value="20:00"> 20:00</option>
            <option value="20:30"> 20:30</option>
            <option value="21:00"> 21:00</option>
            <option value="21:30"> 21:30</option>
            <option value="22:00"> 22:00</option>
          </select>
          <!-- <span class="error"><?php echo form_error('endtime[]'); ?></span> -->
        </div>
    	</div>
    </div>

  	<div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10 dialog-control">
        	<button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 26px;">
            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
          </button>

          <?php if( isset($flag) AND ! $flag ){ ?>
          <button type="button" id="add_frame" class="btn btn-primary" style="height: 26px;">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add
          </button>
          <?php } ?>
    	</div>
  	</div>
</form>

<script type="text/javascript">
	$('select').select2({
    closeOnSelect: true
  });

  $('#add_frame').click(function(){
    var html_item = '';
    html_item = html_item + '<div class="form-group">';
        html_item = html_item + '<label class="control-label col-sm-2 dialog-control" for="pwd">Tên ca </label>';
        html_item = html_item + '<div class="col-sm-2 dialog-control">';
          html_item = html_item + '<input type="text" name="name[]" class="form-control" placeholder="Tên ca" value="">';
        html_item = html_item + '</div>';

        html_item = html_item + '<label class="control-label col-sm-2 dialog-control" for="pwd">T/G Từ </label>';
        html_item = html_item + '<div class="col-sm-2 dialog-control">';
          html_item = html_item + '<select name="starttime[]" class="form-control-select">';
            html_item = html_item + '<option value="08:00"> 08:00</option>';
            html_item = html_item + '<option value="08:30"> 08:30</option>';
            html_item = html_item + '<option value="09:00"> 09:00</option>';
            html_item = html_item + '<option value="09:30"> 09:30</option>';
            html_item = html_item + '<option value="10:00"> 10:00</option>';
            html_item = html_item + '<option value="10:30"> 10:30</option>';
            html_item = html_item + '<option value="11:00"> 11:00</option>';
            html_item = html_item + '<option value="11:30"> 11:30</option>';
            html_item = html_item + '<option value="12:00"> 12:00</option>';
            html_item = html_item + '<option value="12:30"> 12:30</option>';
            html_item = html_item + '<option value="13:00"> 13:00</option>';
            html_item = html_item + '<option value="13:30"> 13:30</option>';
            html_item = html_item + '<option value="14:00"> 14:00</option>';
            html_item = html_item + '<option value="14:30"> 14:30</option>';
            html_item = html_item + '<option value="15:00"> 15:00</option>';
            html_item = html_item + '<option value="15:30"> 15:30</option>';
            html_item = html_item + '<option value="16:00"> 16:00</option>';
            html_item = html_item + '<option value="16:30"> 16:30</option>';
            html_item = html_item + '<option value="17:00"> 17:00</option>';
            html_item = html_item + '<option value="17:30"> 17:30</option>';
            html_item = html_item + '<option value="18:00"> 18:00</option>';
            html_item = html_item + '<option value="18:30"> 18:30</option>';
            html_item = html_item + '<option value="19:00"> 19:00</option>';
            html_item = html_item + '<option value="19:30"> 19:30</option>';
            html_item = html_item + '<option value="20:00"> 20:00</option>';
            html_item = html_item + '<option value="20:30"> 20:30</option>';
            html_item = html_item + '<option value="21:00"> 21:00</option>';
            html_item = html_item + '<option value="21:30"> 21:30</option>';
            html_item = html_item + '<option value="22:00"> 22:00</option>';
          html_item = html_item + '</select>';
        html_item = html_item + '</div>';

        html_item = html_item + '<label class="control-label col-sm-2 dialog-control" for="pwd">Đến hết</label>';
        html_item = html_item + '<div class="col-sm-2 dialog-control">';
          html_item = html_item + '<select name="endtime[]" class="form-control-select">';
            html_item = html_item + '<option value="08:00"> 08:00</option>';
            html_item = html_item + '<option value="08:30"> 08:30</option>';
            html_item = html_item + '<option value="09:00"> 09:00</option>';
            html_item = html_item + '<option value="09:30"> 09:30</option>';
            html_item = html_item + '<option value="10:00"> 10:00</option>';
            html_item = html_item + '<option value="10:30"> 10:30</option>';
            html_item = html_item + '<option value="11:00"> 11:00</option>';
            html_item = html_item + '<option value="11:30"> 11:30</option>';
            html_item = html_item + '<option value="12:00"> 12:00</option>';
            html_item = html_item + '<option value="12:30"> 12:30</option>';
            html_item = html_item + '<option value="13:00"> 13:00</option>';
            html_item = html_item + '<option value="13:30"> 13:30</option>';
            html_item = html_item + '<option value="14:00"> 14:00</option>';
            html_item = html_item + '<option value="14:30"> 14:30</option>';
            html_item = html_item + '<option value="15:00"> 15:00</option>';
            html_item = html_item + '<option value="15:30"> 15:30</option>';
            html_item = html_item + '<option value="16:00"> 16:00</option>';
            html_item = html_item + '<option value="16:30"> 16:30</option>';
            html_item = html_item + '<option value="17:00"> 17:00</option>';
            html_item = html_item + '<option value="17:30"> 17:30</option>';
            html_item = html_item + '<option value="18:00"> 18:00</option>';
            html_item = html_item + '<option value="18:30"> 18:30</option>';
            html_item = html_item + '<option value="19:00"> 19:00</option>';
            html_item = html_item + '<option value="19:30"> 19:30</option>';
            html_item = html_item + '<option value="20:00"> 20:00</option>';
            html_item = html_item + '<option value="20:30"> 20:30</option>';
            html_item = html_item + '<option value="21:00"> 21:00</option>';
            html_item = html_item + '<option value="21:30"> 21:30</option>';
            html_item = html_item + '<option value="22:00"> 22:00</option>';
          html_item = html_item + '</select>';
        html_item = html_item + '</div>';
      html_item = html_item + '</div>';
    $('#group_frame').append(html_item);

    $('select').select2({
      closeOnSelect: true
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