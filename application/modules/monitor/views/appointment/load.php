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