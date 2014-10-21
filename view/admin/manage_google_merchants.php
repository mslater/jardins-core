
<link rel="stylesheet" href="/static/tablesorter/theme.blue.css">
<script src="/static/tablesorter/jquery.tablesorter.min.js" ></script>
<div>
	Last scraped: <?php echo date("m/d/Y h:i:s",GetSetting("last_scraped_time"));?> Result: <strong><?php echo GetSetting("last_scraped_result"); ?></strong>
</div>
<form method="post">
<div class="input-prepend">
    <span class="add-on">Merchant</span>
    <input type="text" placeholder="merchant-site.com" value="<?php echo @$m->merchant;?>" style="width:300px;" name="merchant" class="input-medium">
    <input type="text" placeholder="seller id" value="<?php echo @$m->seller_id;?>" style="width:100px;" name="seller_id" class="input-medium">
    <select class="" name="country" id="country">
    	<option value="US">US</option>
    	<option value="UK">UK</option>
    	<option value="FR">FR</option>
    	<option value="AU">AU</option>
    	<option value="DE">DE</option>
    	<option value="IT">IT</option>
    	<option value="NL">NL</option>
    	<option value="ES">ES</option>
    	<option value="CH">CH</option>
    </select> 
    <script>
    	$("$country").val("<?php echo @$m->country;?>");
    </script>
  </div>
  <input style="margin-top:-12px;" type="submit" name="smAddMerchant" class="btn" value="Submit Merchant" />
</form>
<?php if (!isset($_GET["edit_id"])):?>
<table class="tablesorter">
	<thead>
		<tr>
			<td>Merchant</td>
			<td>Country</td>
			<td>Last Scraped</td>
			<td>Number of items</td>
			<td>Clicks</td>
			<td>Actioins</td>
		</tr>
	</thead>
	<tbody>
		<?php
		while ($r = mysql_fetch_object($merchants))
		{
			$data = mysql_query("SELECT COUNT(*) AS counter FROM google_scraped2 WHERE merchant_id='$r->id'") or die ("ERROR 4554:" . mysql_error());
			$rr = mysql_fetch_object($data);
			echo '<tr>
					<td>'.$r->merchant.' at seller: '.$r->seller_id.'</td>
					<td>'.$r->country.'</td>
					<td>'.date("m/d/Y",$r->last_scraped).'</td>
					<td>'.$rr->counter.'</td>
					<td>'.$r->view.'</td>
					<td><a onClick="return confirm(\'Are you sure?\');" href="admin.php?view=google_merchants&delete_id='.$r->id.'">Delete</a>
					<a href="admin.php?view=google_merchants&edit_id='.$r->id.'">Edit</a>
					<a href="/index.php?view=DownloadMerchantProducts&mid='.$r->id.'" target="_blank">Scrape</a></td>
				</tr>';
		}
		?>
	</tbody>
</table>
<script>
	$(function() {
		$(".tablesorter").tablesorter({
	    theme : 'blue'
	  });
	});
</script>
<?php endif; ?>