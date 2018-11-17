<div id="example">
	<div id="chart"></div>
</div>
<script>
	var chart = $.parseJSON('<?php echo $chart; ?>');

    function createChart() {
        $("#chart").kendoChart(chart);
    }

    $(document).ready(createChart);
    $(document).bind("kendo:skinChange", createChart);
</script>