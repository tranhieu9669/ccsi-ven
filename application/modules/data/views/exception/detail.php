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

    .date-picker{
        width: 100%;
    }

    #uploading{
        position: absolute;
        top: 0px;
        left: 0px;
        background: rgba(0,0,0,0.4);
        opacity: .4;
        z-index: 999999999;
    }
</style>
<form id="formSubmit" class="form-horizontal form-top" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST" enctype="multipart/form-data">
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
                //echo 'close_modal("#dialog_infor_detail");';
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
        <label class="control-label col-sm-2 dialog-control" for="text1">Title</label>
        <div class="col-sm-10 dialog-control">
            <input type="text" name="title" class="form-control" placeholder="Tiêu đề file" >
            <span class="error"><?php echo form_error('title'); ?></span>
        </div>
    </div>

	<div class="form-group">
        <label class="control-label col-sm-2 dialog-control" for="text1">File</label>
        <div class="col-sm-10 dialog-control">
            <input type="file" id="uploadedFiles" name="uploadedFiles" accept=".xls,.xlsx" style="padding-top: 7px; display:none" />
            <input type="text" id="fileSelect" name="fileSelect" class="form-control" placeholder="Chọn file" readonly>
            <span class="error"><?php echo form_error('fileSelect'); ?></span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-2 dialog-control" for="text1">File</label>
        <div class="col-sm-10 dialog-control">
            <select id="id_source" name="id_source" class="form-control-select">
                <option value=""> Chọn Nguồn</option>
                <?php
                if( isset($source) AND !empty($source) ){
                    foreach ($source as $key => $value) {
                        echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
            <span class="error"><?php echo form_error('id_source'); ?></span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-2 dialog-control" for="text1">Center</label>
        <div class="col-sm-10 dialog-control">
            <select id="id_center" name="id_center" class="form-control-select">
                <option value=""> Chọn Trung tâm</option>
                <?php
                if( isset($center) AND !empty($center) ){
                    foreach ($center as $key => $value) {
                        echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
                    }
                }
                ?>
            </select>
            <span class="error"><?php echo form_error('id_center'); ?></span>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10 dialog-control">
            <button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Upload File</button>

            <?php
            if(isset($result_file) AND !empty($result_file)){
                echo '<a href="'.base_url().'application/uploads/v2/result/customer/'.$result_file.'"><span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> File kêt quả</a>';
            }
            ?>
        </div>
    </div>

    <script type="text/javascript">
        $(function(){
            $('#fileSelect').click(function() {
                $('#uploadedFiles').click();
            });

            $('#uploadedFiles').change(function(){
                $('#fileSelect').val($('#uploadedFiles').val());
            });

            $("#id_source").select2({
                closeOnSelect: true
            });

            $("#id_center").select2({
                closeOnSelect: true
            });
        });

        $('form#formSubmit').on('submit', function (e) {
            var docHeight = $(document).height();
            var docWidth = $(document).width();
            $('html').append('<div id="uploading"></div>');
            $('#uploading').css('width', docWidth + 'px');
            $('#uploading').css('height', docHeight + 'px');
            //var _data   = $(this).serializeArray();
            var _data = new FormData($(this)[0]);

            var _action = $(this).attr("action");
            var _method = $(this).attr("method").toUpperCase();
            $.ajax({
                url     : _action,
                dataType: 'html',
                type    : _method,
                data    : _data,
                processData: false,
                contentType: false,
                success : function(response, status, jqXHR){
                    $('#uploading').fadeOut( "slow" );
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
</form>