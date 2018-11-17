<div class="row top_tiles" style="margin: 10px 0;">
  <div class="col-md-2 col-sm-4 col-xs-6 tile">
    <span>Tổng số Agent</span>
    <h2><?php echo (isset($result_rp['total']) ? $result_rp['total'] : 0);?></h2>
  </div>
  <div class="col-md-2 col-sm-4 col-xs-6 tile">
    <span>Số Agent Online</span>
    <h2><?php echo (isset($result_rp['online']) ? $result_rp['online'] : 0);?></h2>
  </div>
  <div class="col-md-2 col-sm-4 col-xs-6 tile">
    <span>Số lượng cuộc gọi</span>
    <h2>###</h2>
  </div>
  <div class="col-md-2 col-sm-4 col-xs-6 tile">
    <span>Số lượng kết nối</span>
    <h2><?php echo (isset($result_rp['connected']) ? $result_rp['connected'] : 0);?></h2>
  </div>
  <div class="col-md-2 col-sm-4 col-xs-6 tile">
    <span>Tỉ lệ Online</span>
    <h2>
      <?php 
        if(isset($result_rp['total']) AND $result_rp['total']){
          echo number_format(($result_rp['online']*100/$result_rp['total']), 2) . '%';
        }else{
          echo '##.##%';
        }
      ?>
    </h2>
  </div>
  <div class="col-md-2 col-sm-4 col-xs-6 tile">
    <span>Tỉ lệ kết nối</span>
    <h2>
      <?php 
        if(isset($result_rp['online']) AND $result_rp['online']){
          echo number_format(($result_rp['connected']*100/$result_rp['online']), 2) . '%';
        }else{
          echo '##.##%';
        }
      ?>
    </h2>
  </div>
</div>
<br />
<!-- Agent Status -->
<?php
$i=0;
while ($row = mysqli_fetch_array($result_ext, MYSQLI_ASSOC)) {
  $id = $row['id'];
  $extension = $row['extension'];
  $ipclient = $row['ipclient'];
  if(empty($ipclient)){
    $ipclient = '127.0.0.1';
  }
  $fullname = $row['fullname'];
  $sipserver = $row['sipserver'];
  $registered = $row['registered'];
  $status = $row['status'];
  $appstatus = $row['appstatus'];
  $duration = $row['duration'];
  $updated_at = $row['updated_at'];

  $icon = 'icon';
  switch ($status) {
    case 'Ring':
      $icon = 'icon-ring';
      break;

    case 'Ringing':
      $icon = 'icon-ring';
      break;

    case 'Up':
      $icon = 'icon-connected';
      break;
    
    default:
      $icon = 'icon';
      break;
  }

  if($i%12 == 0){
    if($i > 0){
      echo '</div>';
    }
    echo '<div class="row">';
  }

  echo '<div class="animated flipInY col-lg-1 col-md-2 col-sm-3 col-xs-4">';
    echo '<div class="tile-stats registered">';
      if($status == "Up"){
        echo '<a class="'.$icon.'" href="'.base_url().'monitor/chanspy?ext='.$extension.'&sipserver='.$sipserver.'" target="_blank"></a>';
      }else{
        echo '<div class="'.$icon.'"></div>';
      }
      echo '<div class="count">'.$extension.'</div>';
      echo '<h3>'.$ipclient.'</h3>';
      echo '<p>'.$updated_at.'</p>';
      echo '<p style="border-top: 1px solid #FFF;">SIP: '.$sipserver.'</p>';
    echo '</div>';
  echo '</div>';

  $i++;
}
if($i > 0){
  echo '</div>';
}
?>