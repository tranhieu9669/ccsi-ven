<div id="form-dialog">
<?php
    if(isset($content) AND !empty($content)){
      $this->load->view($content);
    }
?>
</div>