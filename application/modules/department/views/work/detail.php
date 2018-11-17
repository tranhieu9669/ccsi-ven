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
      padding-top: 8px;
  	}

  	.form-horizontal .form-group{
    	margin-left: 0px;
    	margin-right: 0px;
  	}

  	.form-horizontal .control-label{
    	padding-right: 8px;
    	margin-left: -10px;
  	}

  	form#formSubmit{
    	padding: 15px 10px;  
  	}

  	input[type="checkbox"], input[type="radio"]{
    	margin: 6px 6px 0px 5px
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
      <label class="control-label col-sm-2 dialog-control" for="pwd">Từ ngày </label>
      <div class="col-sm-4 dialog-control">
        <input type="text" id="startdate_dialog" name="startdate" class="form-control date-picker" placeholder="Chọn ngày" value="<?php echo date('Y-m-d');?>">
      </div>

      <label class="control-label col-sm-2 dialog-control" for="pwd">Đến Ngày </label>
      <div class="col-sm-4 dialog-control">
        <input type="text" id="enddate_dialog" name="enddate" class="form-control date-picker" placeholder="Chọn ngày" value="<?php echo date('Y-m-d');?>">
      </div>
    </div>

    <?php
      if(isset($listAgent) AND !empty($listAgent)){
        echo '<div style="height: 450px;overflow-y: scroll;">';
        $i = 0;
        foreach ($listAgent as $key => $value) {
          $id = $value['id'];
          $full_name = $value['full_name'];
          $ext = $value['ext'];

          if($i%2 == 0){
            if($i < 1){
              echo '<div class="form-group">';
            }else{
              echo '</div>';
              echo '<div class="form-group">';
            }
          }

          echo '<label class="control-label col-sm-4 dialog-control" for="pwd">'.$full_name.'</label>';
          echo '<div class="col-sm-2 dialog-control">';
          echo '<label class="span2">';
          if(in_array('pg_'.$id.'_'.$ext, $detail)){
            echo '<input type="checkbox" name="pg_'.$id.'_'.$ext.'" style="float: left;" value="1" checked /> PG';
          }else{
            echo '<input type="checkbox" name="pg_'.$id.'_'.$ext.'" style="float: left;" value="1" /> PG';
          }
          echo '</label>';
          echo '<label class="span2">';
          if(in_array('mkt_'.$id.'_'.$ext, $detail)){
            echo '<input type="checkbox" name="mkt_'.$id.'_'.$ext.'" style="float: left;" value="1" checked /> MKT';
          }else{
            echo '<input type="checkbox" name="mkt_'.$id.'_'.$ext.'" style="float: left;" value="1" /> MKT';
          }
          echo '</label>';
          echo '</div>';

          $i++;
        }

        if($i > 0){
          echo '</div>';
        }

        echo '</div>';
      }
    ?>

  	<div class="form-group">
    	<div class="col-sm-offset-3 col-sm-9 dialog-control">
        	<button type="submit" id="dataaction" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
    	</div>
  	</div>
</form>

<script type="text/javascript">
	$('#id_dt_categories').select2({
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