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
      <label class="control-label col-sm-3 dialog-control" for="pwd">Name </label>
      <div class="col-sm-9 dialog-control">
        <input type="text" name="name" class="form-control" placeholder="Nhà mạng" value="<?php echo set_value_input('name', $detail);?>">
        <span class="error"><?php echo form_error('name'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">ClientID </label>
      <div class="col-sm-9 dialog-control">
        <input type="text" name="client_id" class="form-control" placeholder="ClientID" value="<?php echo set_value_input('client_id', $detail);?>">
        <span class="error"><?php echo form_error('client_id'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Secret </label>
      <div class="col-sm-9 dialog-control">
        <input type="text" name="secret" class="form-control" placeholder="Secret" value="<?php echo set_value_input('secret', $detail);?>">
        <span class="error"><?php echo form_error('secret'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <input type="hidden" id="hdaction" name="hdaction" value="dataaction">
      <div class="col-sm-offset-3 col-sm-9 dialog-control">
          <button type="submit" id="dataaction" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
      </div>
    </div>
  <script type="text/javascript">
    $(function(){
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
    });
  </script>
</form>