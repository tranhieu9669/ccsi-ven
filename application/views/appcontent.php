<table summary="This table shows how to create responsive tables using Bootstrap's default functionality" class="table table-bordered table-hover">
	<thead>
		<tr>
			<th width="165px">Ca</th>
			<?php
			if(isset($center) AND !empty($center)){
				foreach ($center as $key => $value) {
					echo '<th>'.$value['name'].'</th>';
				}
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($framenow as $key => $value) {
			$id_frame = $value['id'];
			$name_frame = $value['name'];
			$start_frame = $value['start'];
			$end_frame = $value['end'];
			echo '<tr>';
			echo '<td><b>'.$name_frame.'</b></td>';
			
			foreach ($center as $_key => $_value) {
				$id_center = $_value['id'];
				$name_center = $_value['name'];

				$app = 0;
				$limit = 0;
				$round = 0;

				if( isset($dataResult[$id_center.'-'.$id_frame]) AND !empty($dataResult[$id_center.'-'.$id_frame]) ){
					$total = $dataResult[$id_center.'-'.$id_frame]['total'];
					$cancel = $dataResult[$id_center.'-'.$id_frame]['cancel'];
					$limit = $dataResult[$id_center.'-'.$id_frame]['limit'];
					$round = $dataResult[$id_center.'-'.$id_frame]['round'];

					if( ($total - $cancel) < 10 ){
						$app = '0'.($total - $cancel);
					}else{
						$app = ($total - $cancel);
					}
				}
				if($round < 61){
					echo '<td class="valapp low"><strong class="totalapp">'.$app.' </strong><strong class="limited">'.$limit.'</strong></td>';
				}elseif($round < 81){
					echo '<td class="valapp medium"><strong class="totalapp">'.$app.' </strong><strong class="limited">'.$limit.'</strong></td>';
				}elseif($round < 96){
					echo '<td class="valapp high"><strong class="totalapp">'.$app.' </strong><strong class="limited">'.$limit.'</strong></td>';
				}else{
					echo '<td class="valapp over"><strong class="totalapp">'.$app.' </strong><strong class="limited">'.$limit.'</strong></td>';
				}
			}
			echo '</tr>';
		}
		foreach ($framenext as $key => $value) {
			$id_frame = $value['id'];
			$name_frame = $value['name'];
			$start_frame = $value['start'];
			$end_frame = $value['end'];
			echo '<tr>';
			echo '<td><b>'.$name_frame.'</b></td>';
			
			foreach ($center as $_key => $_value) {
				$id_center = $_value['id'];
				$name_center = $_value['name'];

				$app = 0;
				$limit = 0;
				$round = 0;

				if( isset($dataResult[$id_center.'-'.$id_frame]) AND !empty($dataResult[$id_center.'-'.$id_frame]) ){
					$total = $dataResult[$id_center.'-'.$id_frame]['total'];
					$cancel = $dataResult[$id_center.'-'.$id_frame]['cancel'];
					$limit = $dataResult[$id_center.'-'.$id_frame]['limit'];
					$round = $dataResult[$id_center.'-'.$id_frame]['round'];

					if( ($total - $cancel) < 10 ){
						$app = '0'.($total - $cancel);
					}else{
						$app = ($total - $cancel);
					}
				}
				if($round < 61){
					echo '<td class="valapp low"><strong class="totalapp">'.$app.' </strong><strong class="limited">'.$limit.'</strong></td>';
				}elseif($round < 81){
					echo '<td class="valapp medium"><strong class="totalapp">'.$app.' </strong><strong class="limited">'.$limit.'</strong></td>';
				}elseif($round < 96){
					echo '<td class="valapp high"><strong class="totalapp">'.$app.' </strong><strong class="limited">'.$limit.'</strong></td>';
				}else{
					echo '<td class="valapp over"><strong class="totalapp">'.$app.' </strong><strong class="limited">'.$limit.'</strong></td>';
				}
			}
			echo '</tr>';
		}
		?>
	</tbody>
	<tfoot>
		<tr>
				
		</tr>
	</tfoot>
</table>