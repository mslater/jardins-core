<form method="get">
<div class="input-prepend">
    <input type="hidden" name="view" value="google_products" />
    <span class="add-on">Keyword</span>
    <input type="text" value="<?php echo @$_GET["keyword"];?>" style="width:300px;" name="keyword" class="input-medium"> 
  </div>
  <br>
  <div class="input-prepend">
    <span class="add-on">Category</span>
    <input type="text" value="<?php echo @$_GET["category"];?>" style="width:300px;" name="category" class="input-medium"> 
  </div>
  <br>
  <div class="input-prepend">
    <span class="add-on">Merchant</span>
    <select name="merchant" id="merchant">
    	<?php
    	foreach ($merchants as $m)
			echo '<option value="'.($m).'">'.(utf8_decode($m)).'</option>';
    	?>
    </select> 
    <script>
    	$("#merchant").val("<?php echo $_GET["merchant"];?>");
    </script>
  </div>
  <br><br>
  <input style="margin-top:-12px;" type="submit" class="btn" value="Search" />
  <input style="margin-top:-12px;" type="submit" name="smSearchAll" class="btn" value="Search All" />
</form>

<div id="tabs">
  <ul>
    <li><a href="#tabs-1">Data added to front page (<?php echo mysql_num_rows($data_scraped);?>)</a></li>
    <li><a href="#tabs-2">Scraped Data (<?php echo mysql_num_rows($data_scraped2);?>)</a></li>
  </ul>
  <div id="tabs-1">
    <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Keyword</th>
        <th>Category</th>
        <th>Merchant</th>
        <th>Created Date</th>
        <th>Price</th>
        <th>Description</th>
        <th>Picture</th>
        <th>Site</th>
        <th>Country</th>
        <th>In Products</th>
      </tr>
    </thead>
    <tbody>
    	<?php 
    	while ($r = mysql_fetch_object($data_scraped))
		{
			echo '<tr id="pr_id_'.$r->id.'">
        <td>'.DisplayUTF8EncodedHTMLString($r->name).'</td>
        <td>'.$r->keyword.'</td>
        <td>'.$r->category.'</td>
        <td>'.DisplayUTF8EncodedHTMLString($r->merchant_name).'</td>
        <td>'.$r->created_date.'</td>
        <td>'.number_format($r->price,2).'</td>
        <td>'. (strlen($r->description) > 50 ? substr($r->description,0,49).'...' : $r->description) .'</td>
        <td><img src="'.$r->picture.'" style="height:80px;"/></td>
        <td><a target="_blank" href="'.$r->site_url.'">Site</a></td>
        <td>'.$r->country.'</td>
        <td>';
        $data_inpost = mysql_query("SELECT p.* FROM product AS p
        	  INNER JOIN product_seller_item AS ps ON p.id = ps.product_id
        	  INNER JOIN keyword_category AS kc ON kc.id = ps.keyword_category_id
        	  INNER JOIN keyword AS k ON k.category_id = kc.id
        	  INNER JOIN google_scraped AS gs ON gs.keyword_id = k.id
        	  WHERE gs.id='{$r->id}'") or die(mysql_error());
		if (mysql_num_rows($data_inpost) == 0)
		{
			echo 'none';
		}
		else
		{
			while ($r2 = mysql_fetch_object($data_inpost))
			{
				echo '<a target="_blank" href="/admin.php?view=products&edit_id='.$r2->id.'">'.$r2->name.'</a> <br>';
			}
		}
        echo '</td></tr>';
		}
    	?>
    </tbody>
  </table>
  </div>
  <div id="tabs-2">
    <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Merchant</th>
        <th>Created Date</th>
        <th>Price</th>
        <th>Description</th>
        <th>Picture</th>
        <th>Site</th>
        <th>Country</th>
      </tr>
    </thead>
    <tbody>
    	<?php 
    	while ($r = mysql_fetch_object($data_scraped2))
		{
			echo '<tr id="pr_id_'.$r->id.'">
        <td>'.DisplayUTF8EncodedHTMLString($r->name).'</td>
        <td>'.DisplayUTF8EncodedHTMLString($r->merchant_name).'</td>
        <td>'.$r->created_date.'</td>
        <td>'.number_format($r->price,2).'</td>
        <td>'.DisplayUTF8EncodedHTMLString($r->description).'</td>
        <td><img src="'.$r->picture.'" style="height:80px;"/></td>
        <td><a target="_blank" href="'.$r->site_url.'">Site</a></td>
        <td>'.$r->country.'</td>
        ';
        echo '</tr>';
		}
    	?>
    </tbody>
  </table>
  </div>
</div>


<script>
  $(function() {
    $( "#tabs" ).tabs();
  });
  </script>