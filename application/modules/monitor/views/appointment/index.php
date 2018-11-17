<style type="text/css">
	.media .date{
		background: #ccc;
		width: 45px;
		margin-right: 10px;
		border-radius: 10px;
		padding: 8px 5px;
	}

	h2{
		font-size: 14px;
	}

	.x_title{
		overflow: hidden;
		text-align: center;
	}
</style>

<div id="content-app">
<?php
if(isset($dataReturn) AND !empty($dataReturn)){
	$i=0;
	foreach ($dataReturn as $key => $value) {
		$name = $value['name'];
		$data = $value['data'];

		if($i%6 == 0){
	      	if($i > 0){
	        	echo '</div>';
	      	}
	      	echo '<div class="row">';
	    }

	    echo '<div class="col-md-2">';
	        echo '<div class="x_panel">';
	          	echo '<div class="x_title">';
	            	echo '<h2>'.str_replace('DEAURA','', $name).'</h2>';#<small>Sessions</small>
	            	echo '<div class="clearfix"></div>';
	          	echo '</div>';
	          	echo '<div class="x_content">';
	          	##################
	          	if(isset($data) AND !empty($data)){
	          		foreach ($data as $_key => $_value) {
	          			$frametime = $_value['frametime'];
						$total = $_value['total'];
						$cancel = $_value['cancel'];

						echo '<article class="media event">';
			              	echo '<a class="pull-left date">';
			                	echo '<p class="month"> '.$frametime.' </p>';
			              	echo '</a>';
			              	echo '<div class="media-body">';
			                	echo '<a class="title" href="#">Số hẹn: '.$total.'</a>';
			                	echo '<p>Số hủy: '.$cancel.'</p>';
			              	echo '</div>';
			            echo '</article>';
	          		}
	          	}
	            #################
	          echo '</div>';
	        echo '</div>';
	  	echo '</div>';

	  	$i++;
	}
	if($i > 0){
	    echo '</div>';
  	}
}
?>
</div>
<script type="text/javascript">
  function load_content(){
    $.ajax({
      url     : '<?php echo base_url();?>monitor/appointment/load',
      dataType: 'html',
      type    : 'GET',
      data    : {},
      success : function(response, status, jqXHR){
          $('#content-app').html(response);
      },
      error   : function(jqXHR, status, err){
      },
      complete: function(jqXHR, status){
      }
    });
  }

  $( document ).ready(function() {
    setInterval(function(){load_content()},1000 * 10);
  });
</script>