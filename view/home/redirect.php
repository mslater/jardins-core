<br><br><br><br><br><br><br><br><br><br>
<center><h3>You are redirecting to:</h3>
<p><strong><?php echo $url;?></strong></p>
<h3>Please wait <span id="timer">5</span> second(s)...</h3>
</center>
<br><br><br><br><br><br><br><br><br><br>
<script type="text/javascript">
	var redttime = 5;
	setInterval(function(){
		if (redttime > 0)
			redttime--;
		$("#timer").html(redttime);
		if (redttime == 1) 
			window.location = "<?php echo $url;?>"; 
	},1000);
</script>