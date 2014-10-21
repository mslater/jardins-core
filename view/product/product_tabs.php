<div class="col-md-12 product_bottom_tabs product_detail_tabs">
  <ul>
  	<?php
  	if ($p->product_tabs == 1)
	{
	  	if (in_array('gi',$product_types))
	  	{
	  	?>
	    <li><a href="#tabs-1">Best Offers per Product</a></li>
	    <li><a href="#tabs-2">Best Offers per Seller</a></li>
	  	<?php
	  	}
	  	else
	  	{
	  	?>
	    <li><a href="#tabs-1">Where To Buy</a></li>
	    <li><a href="#tabs-2">Best Companions (<span id="countcomp"></span>)</a></li>
	  	<?php
	  	}
	  	?>
	    <li><a href="#tabs-3">Care</a></li>
   <?php } ?>
    <li><a href="#tabs-4">Customer Reviews (<?php echo mysql_num_rows($reviewLists);?>)</a></li>
  </ul>
  <?php
  if ($p->product_tabs == 1)
  {
  ?>
  <div id="tabs-1" class="clearfix" style="max-height: 502px;overflow: scroll;">
    <?php
    if (in_array('gi',$product_types))
    {




    	$productdata = mysql_query("SELECT gs.*,k.keyword FROM google_scraped AS gs
			INNER JOIN keyword AS k ON gs.keyword_id = k.id
			INNER JOIN keyword_category AS kc ON kc.id=k.category_id
			INNER JOIN product_seller_item AS psi ON psi.keyword_category_id=kc.id
			WHERE psi.product_id='{$p->id}' AND country='".USER_REGION."' group by gs.name, gs.merchant_name ORDER BY k.keyword") or die (mysql_error());
		if (mysql_num_rows($sproductdata) == 0)
		{
			echo '<div class="notice">There is no product in this section. We will update later.</div>';
		}

		$current_keyword = "";
		while ($rsp = mysql_fetch_object($productdata))
		{

			if ($current_keyword != $rsp->keyword)
			{
				echo '<p class="mitm">Product: '.ucwords($rsp->keyword).'</p>';
			}
			$current_keyword = $rsp->keyword;
			
			if ($rsp->downloaded_image != '' && $rsp->downloaded_image != '0')
				$rsp->picture = '/scaleimage.php?w=150&amp;h=150&amp;t=retailimage&amp;f='.$rsp->downloaded_image;
				
			$rsp->site_url = str_replace("&amp;", "&", $rsp->site_url);
			$rsp->site_url = str_replace("&", "&amp;", $rsp->site_url);
			
			echo '<div class="pdb">
				<a href="'.url_redirect(stripcslashes($rsp->site_url),"&spi=".$rsp->id).'" target="_blank" rel="nofollow"><img alt="'.htmlentities($rsp->name).'" data-original="'.$rsp->picture.'" class="pdb_i lazy" /></a>
				<div class="pdb_inf">
					<p>'.DisplayUTF8EncodedHTMLString($rsp->name).'</p>
					<p>From: <strong>'.$rsp->merchant_name.'</strong></p>
				</div>
				<div class="pdb_c">
					<p class="pdb_p">Price: '.CURRENCY.number_format($rsp->price,2).'</p>
					<a rel="nofollow" href="/wishlist/index.html?sid='.$rsp->id.'"><img class="pdb_aw" alt="add '.stripcslashes($rsp->name).' to wishlist" src="/static/images/add-to-wishlist.png" /></a>
				</div>
			</div>';
		}
	}
	else
	{
		$sproductdata = mysql_query("SELECT gs.* FROM google_scraped AS gs
			INNER JOIN keyword AS k ON gs.keyword_id = k.id
			INNER JOIN keyword_category AS kc ON kc.id=k.category_id
			INNER JOIN product_seller_item AS psi ON psi.keyword_category_id=kc.id
			WHERE psi.product_id='{$p->id}' AND country='".USER_REGION."' group by  gs.merchant_name, gs.name ORDER BY gs.name") or die (mysql_error());
		if (mysql_num_rows($sproductdata) == 0)
		{
			echo '<div class="notice">There is no product in this section. We will update later.</div>';
		}
		while ($rsp = mysql_fetch_object($sproductdata))
		{
			$rsp->site_url = str_replace("&amp;", "&", $rsp->site_url);
			$rsp->site_url = str_replace("&", "&amp;", $rsp->site_url);
			if ($rsp->downloaded_image != '' && $rsp->downloaded_image != '0')
				$rsp->picture = '/scaleimage.php?w=150&amp;h=150&amp;t=retailimage&amp;f='.$rsp->downloaded_image;
			echo '<div class="pdb">
				<a href="'.url_redirect(stripcslashes($rsp->site_url),"&spi=".$rsp->id).'" target="_blank" rel="nofollow"><img alt="'.htmlentities($rsp->name).'" data-original="'.$rsp->picture.'" class="pdb_i lazy" /></a>
				<div class="pdb_inf">
					<p>'.DisplayUTF8EncodedHTMLString($rsp->name).'</p>
					<p>From: <strong>'.$rsp->merchant_name.'</strong></p>
				</div>
				<div class="pdb_c">
					<p class="pdb_p">Price: '.CURRENCY.number_format($rsp->price,2).'</p>
					<a rel="nofollow" href="/wishlist/index.html?sid='.$rsp->id.'"><img class="pdb_aw" alt="add '.stripcslashes($rsp->name).' to wishlist" src="/static/images/add-to-wishlist.png" /></a>
				</div>
			</div>';
		}

	}
    ?>
  </div>
  <div id="tabs-2" class="clearfix">
    <?php
    if (in_array('gi',$product_types))
    {
		$sproductdata = mysql_query("SELECT gs.* FROM google_scraped AS gs
			INNER JOIN keyword AS k ON gs.keyword_id = k.id
			INNER JOIN keyword_category AS kc ON kc.id=k.category_id
			INNER JOIN product_seller_item AS psi ON psi.keyword_category_id=kc.id
			WHERE psi.product_id='{$p->id}' AND country='".USER_REGION."' group by  gs.merchant_name, gs.name ORDER BY gs.merchant_name") or die (mysql_error());
		if (mysql_num_rows($sproductdata) == 0)
		{
			echo '<div class="notice">There is no product in this section. We will update later.</div>';
		}
		$current_merchant = "";
		while ($rsp = mysql_fetch_object($sproductdata))
		{
			if ($current_merchant != $rsp->merchant_name)
			{
				echo '<p class="mitm">'.$rsp->merchant_name.'</p>';
			}
			$current_merchant = $rsp->merchant_name;
			
			$rsp->site_url = str_replace("&amp;", "&", $rsp->site_url);
			$rsp->site_url = str_replace("&", "&amp;", $rsp->site_url);
			
			if ($rsp->downloaded_image != '' && $rsp->downloaded_image != '0')
				$rsp->picture = '/scaleimage.php?w=150&amp;h=150&amp;t=retailimage&amp;f='.$rsp->downloaded_image;
			echo '<div class="pdb">
				<a href="'.url_redirect(stripcslashes($rsp->site_url),"&spi=".$rsp->id).'" target="_blank" rel="nofollow"><img alt="'.htmlentities($rsp->name).'" data-original="'.$rsp->picture.'" class="pdb_i lazy" /></a>
				<div class="pdb_inf">
					<p>'.DisplayUTF8EncodedHTMLString($rsp->name).'</p>
					<p>From: <strong>'.$rsp->merchant_name.'</strong></p>
				</div>
				<div class="pdb_c">
					<p class="pdb_p">Price: '.CURRENCY.number_format($rsp->price,2).'</p>
					<a rel="nofollow" href="/wishlist/index.html?sid='.$rsp->id.'"><img class="pdb_aw" alt="add '.stripcslashes($rsp->name).' to wishlist" src="/static/images/add-to-wishlist.png" /></a>
				</div>
			</div>';
		}
	}
	else
	{
		$sproductdata = mysql_query("SELECT p.*,rp.related_id FROM product AS p
			INNER JOIN related_product AS rp ON p.id = rp.related_id
			WHERE rp.product_id='{$p->id}' AND p.status=1  AND p.publishing_date < '".time()."'") or die (mysql_error());
		if (mysql_num_rows($sproductdata) == 0)
		{
			echo '<div class="notice">There is no product in this section. We will update later.</div>';
		}
		while ($r = mysql_fetch_object($sproductdata))
		{
			//get categories
			$spdcatdata = mysql_query("SELECT DISTINCT pc.category_type FROM product_category AS pc
				INNER JOIN product_in_category AS pic ON pic.category_id=pc.id
				WHERE pic.product_id='{$r->related_id}'");
			$spdcats = array();
			while ($r2 = mysql_fetch_object($spdcatdata))
				$spdcats[] = $r2->category_type;
			$tag = "";
			if (in_array("gi",$spdcats))
				$tag = '<img class="ptag" title="Garden Idea Product"  alt="Garden Idea Product" src="/static/images/gardenidea_tag.png" />';
			echo '<div class="pdb">
				<a href="'.url_product_detail($r->related_id).'" target="_blank">'.$tag.'<img alt="'.htmlentities($r->name).'" data-original="/scaleimage.php?w=150&amp;h=150&amp;t=productimage&amp;f='.$r->main_picture.'" class="pdb_i lazy" /></a>
				<div class="pdb_inf">
					<p>'.DisplayUTF8EncodedHTMLString($r->name).'</p>
					<p>'.stripcslashes($r->intro).'</p>
				</div>
				<div class="pdb_c">
					<p class="pdb_p">'.GetMinPriceProduct($r->related_id).'</p>
					<a rel="nofollow" href="/wishlist/index.html?pid='.$r->related_id.'"><img class="pdb_aw" alt="add '.stripcslashes($r->name).' to wishlist" src="/static/images/add-to-wishlist.png" /></a>
				</div>
			</div>';
		}
	}
    ?>
    <script type="text/javascript">
    	$("#countcomp").html("<?php echo mysql_num_rows($sproductdata);?>");
    </script>
  </div>
  <div id="tabs-3" class="clearfix">
    <?php if ($p->care_id != 0) :?>
		<?php echo GetForm($p->care_id);?>
	<?php else:?>
		<div class="notice">There is no Care Information for this product. We will update later.</div>
	<?php endif;?>
  </div>
  <?php
  }//end if ($p->product_tabs == 1)
  ?>
  <div id="tabs-4" class="clearfix">

      <?php include ("customer_review.php");?>

  </div>
</div>
<script>
$(".product_bottom_tabs").tabs();
</script>

