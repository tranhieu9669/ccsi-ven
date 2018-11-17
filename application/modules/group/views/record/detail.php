<script src="<?php echo base_url();?>assets/audiojs/audiojs/audio.min.js"></script>
<script>
  	audiojs.events.ready(function() {
    	audiojs.createAll();
  	});
</script>

<style type="text/css">
	.audiojs{
		margin: 0px auto;
	}

	#record{
		padding: 10px 0px;
	}
</style>

<div id="record">
	<audio id="file-record" src="<?php echo $base64;?>" preload="auto"></audio>
</div>