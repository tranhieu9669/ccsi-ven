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
    <label class="control-label col-sm-2 dialog-control" for="pwd">Trung tâm Spa </label>
    <div class="col-sm-10 dialog-control">
      <select id="id_center_spa" name="id_center_spa" class="form-control-select">
        <option value="" > Chọn Trung tâm Spa</option>
        <?php
        if( isset($centerspa) AND !empty($centerspa) ){
          foreach ($centerspa as $key => $value) {
            if( isset($detail['id_center']) AND $detail['id_center'] == $value['id'] ){
              echo '<option value="' . $value['id'] . '" selected="selected" > ' . $value['name'] . '</option>';
            }else{
              echo '<option value="' . $value['id'] . '" > ' . $value['name'] . '</option>';
            }
          }
        }
        ?>
      </select>
      <span class="error"><?php echo form_error('id_center_spa'); ?></span>
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-sm-2 dialog-control" for="pwd">Trung tâm Call </label>
    <div class="col-sm-10 dialog-control">
      <select id="id_center_call" name="id_center_call" class="form-control-select">
        <option value="" > Chọn Trung tâm Call</option>
        <?php
        if( isset($centercall) AND !empty($centercall) ){
          foreach ($centercall as $key => $value) {
            if( isset($detail['id_center']) AND $detail['id_center'] == $value['id'] ){
              echo '<option value="' . $value['id'] . '" selected="selected" > ' . $value['name'] . '</option>';
            }else{
              echo '<option value="' . $value['id'] . '" > ' . $value['name'] . '</option>';
            }
          }
        }
        ?>
      </select>
      <span class="error"><?php echo form_error('id_center_call'); ?></span>
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-sm-2 dialog-control" for="pwd">Phòng </label>
    <div class="col-sm-4 dialog-control">
      <select id="id_department" name="id_department" class="form-control-select">
        <option value="" > Chọn Phòng</option>
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
      <span class="error"><?php echo form_error('id_department'); ?></span>
    </div>

    <label class="control-label col-sm-2 dialog-control" for="pwd">Ca làm việc </label>
    <div class="col-sm-4 dialog-control">
      <select id="id_frametime" name="id_frametime" class="form-control-select">
        <option value="" > Chọn Ca</option>
        <?php
        if( isset($frametime) AND !empty($frametime) ){
          foreach ($frametime as $key => $value) {
            if( isset($detail['id_frametime']) AND $detail['id_frametime'] == $value['id'] ){
              echo '<option value="'.$value['id'].'" selected="selected"> '.$value['name'].'( '.$value['start'].'-'.$value['end'].')</option>';
            }else{
              echo '<option value="'.$value['id'].'"> '.$value['name'].' ('.$value['start'].'-'.$value['end'].')</option>';
            }
          }
        }
        ?>
      </select>
      <span class="error"><?php echo form_error('id_frametime'); ?></span>
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-sm-2 dialog-control" for="pwd">Ngày hẹn </label>
    <div class="col-sm-4 dialog-control">
      <input type="text" id="date" name="date" class="form-control date-picker" placeholder="Chọn ngày" value="<?php echo date('Y-m-d');?>">
      <span class="error"><?php echo form_error('date'); ?></span>
    </div>

    <label class="control-label col-sm-2 dialog-control" for="pwd">Số hẹn </label>
    <div class="col-sm-4 dialog-control">
      <select id="val_from" name="val_from" class="form-control-select">
        <option value="1"> 01 hẹn</option>
        <option value="2"> 02 hẹn</option>
        <option value="3"> 03 hẹn</option>
        <option value="4"> 04 hẹn</option>
        <option value="5"> 05 hẹn</option>
      </select>
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

  $('#id_center_spa').select2({
    closeOnSelect: true
  });

  $('#id_center_call').select2({
    closeOnSelect: true
  });

  $('#id_department').select2({
    closeOnSelect: true
  });

  $('#id_frametime').select2({
    closeOnSelect: true
  });

  $('#val_from').select2({
    closeOnSelect: true
  });

  $("#date").kendoDatePicker({
    format  : 'yyyy-MM-dd',
  });

  $('#id_center_call').change(function(){
    var id_center = $(this).val();
    $.ajax({
      url     : '<?php echo base_url();?>ajax/departmentbycenter',
      type    : "GET",
      data    : {'id_center':id_center},
      success : function( data ) {
        var obj_decode = $.parseJSON(data);
        var department = obj_decode;
          
        var html_department = '<option value="" selected="selected"> Chọn Phòng</option>';
        for(var key in department){
          var detail  = department[key];
          var id      = detail['id'];
          var name    = detail['name'];
          html_department += '<option value="' + id + '">' + name + '</option>';
        }
        $('#id_department').html(html_department);
        $("#id_department").select2("val", "");
      },

      error   : function(msg){
        var html_department = '<option value="" selected="selected"> Chọn Phòng</option>';
        $('#id_department').html(html_department);
        $("#id_department").select2("val", "");
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