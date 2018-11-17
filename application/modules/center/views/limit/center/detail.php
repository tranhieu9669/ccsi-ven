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
    	margin: 2px 3px 0px 0px;
  	}

    strong{
      margin-right: 15px;
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
    <label class="control-label col-sm-2 dialog-control" for="pwd">Từ ngày </label>
    <div class="col-sm-4 dialog-control">
      <input type="text" id="startdate_dialog" name="startdate" class="form-control date-picker" placeholder="Chọn ngày" value="<?php echo date('Y-m-d');?>">
      <span class="error"><?php echo form_error('startdate'); ?></span>
    </div>

    <label class="control-label col-sm-2 dialog-control" for="pwd">Đến Ngày </label>
    <div class="col-sm-4 dialog-control">
      <input type="text" id="enddate_dialog" name="enddate" class="form-control date-picker" placeholder="Chọn ngày" value="<?php echo date('Y-m-d');?>">
      <span class="error"><?php echo form_error('enddate'); ?></span>
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-sm-2 dialog-control" for="pwd">Trung tâm </label>
    <div class="col-sm-10 dialog-control">
      <select id="id_center_dialog" name="id_center" class="form-control-select">
        <option value="0" > Chọn Trung tâm</option>
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
  </div>

  <div class="form-group">
    <label class="control-label col-sm-2 dialog-control" for="pwd">Ca </label>
    <div class="col-sm-10 dialog-control" style="padding-top: 8px;">
      <input name="id_frametime[]" type="checkbox" value="1" checked="checked" /><strong>Ca 1</strong>
      <input name="id_frametime[]" type="checkbox" value="2" checked="checked" /><strong>Ca 2</strong>
      <input name="id_frametime[]" type="checkbox" value="3" checked="checked" /><strong>Ca 3</strong>
      <input name="id_frametime[]" type="checkbox" value="4" checked="checked" /><strong>Ca 4</strong>
      <input name="id_frametime[]" type="checkbox" value="5" checked="checked" /><strong>Ca 5</strong>
      <input name="id_frametime[]" type="checkbox" value="6" checked="checked" /><strong>Ca 6</strong>
      <input name="id_frametime[]" type="checkbox" value="7" checked="checked" /><strong>Ca 7</strong>
      <input name="id_frametime[]" type="checkbox" value="8" checked="checked" /><strong>Ca 8</strong>
      <span class="error"><?php echo form_error('id_frametime'); ?></span>
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-sm-2 dialog-control" for="pwd">Giới hạn ca </label>
    <div class="col-sm-10 dialog-control">
      <input type="number" name="limited" class="form-control" placeholder="Giới hạn ca" value="30" min="1" max="300" step="1" onkeypress="return isNumberKey(event);">
      <span class="error"><?php echo form_error('limited'); ?></span>
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10 dialog-control">
        <button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
    </div>
  </div>
</form>

<script type="text/javascript">
  $('#id_center_dialog').select2({
    closeOnSelect: true
  });

  $('#id_frametime_dialog').select2({
    closeOnSelect: true
  });

  $('#status').select2({
    closeOnSelect: true
  });

  $("#startdate_dialog").kendoDatePicker({
    format : 'yyyy-MM-dd',
    min : '<?php echo date('Y-m-d');?>',
  });
  $("#enddate_dialog").kendoDatePicker({
    format : 'yyyy-MM-dd',
    min : '<?php echo date('Y-m-d');?>',
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