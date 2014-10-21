<div class="cpnp clearfix">
    <?php
    $sproductdata = mysql_query("SELECT p.*,rp.related_id FROM product AS p
		INNER JOIN related_product AS rp ON p.id = rp.related_id
		WHERE rp.product_id='{$p->id}' AND p.status=1  AND p.publishing_date < '".time()."'") or die (mysql_error());
	if (mysql_num_rows($sproductdata) == 0)
	{
		//echo '<div class="notice">There is no product in this section. We will update later.</div>';
	}
	while ($r = mysql_fetch_object($sproductdata))
	{
		$condition = '<br><div class="rqfgi">'.GetShortReq($r->requirement_id)."</div>";

		//get categories
		$spdcatdata = mysql_query("SELECT DISTINCT pc.category_type FROM product_category AS pc
			INNER JOIN product_in_category AS pic ON pic.category_id=pc.id
			WHERE pic.product_id='{$r->related_id}'");
		$spdcats = array();
		while ($r2 = mysql_fetch_object($spdcatdata))
			$spdcats[] = $r2->category_type;
		$tag = "";
		if (in_array("gi",$spdcats))
			$tag = '<img class="ptag" alt="Garden Idea Product" src="/static/images/gardenidea_tag.png" />';
		echo '<div class="pdb pdbc">
			<a href="'.url_product_detail($r->id).'"><img alt="'.stripcslashes($r->name).'" src="/scaleimage.php?w=172&amp;h=172&amp;t=productimage&amp;f='.$r->main_picture.'" class="pdb_i" /></a>
			<div class="pdb_inf">
				'. ($r->reward_icon != '' ? '<img alt="'.stripcslashes($r->name).'" src="/scaleimage.php?w=45&amp;h=45&amp;t=productimage&amp;f='.$r->reward_icon.'" class="pdbcl">' : "" ).'
				<h4 class="gih4">'.stripcslashes($r->name).'</h4>
				<p>'.stripcslashes($r->intro).$condition.'</p>
			</div>
			<div class="pdb_c">
				<div class="fb-like" style="float: left;margin-top: 6px;" data-href="'. BASE_URL . url_product_detail($r->id,"").'" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true" data-font="verdana"></div>
				<a rel="nofollow" href="/wishlist/index.html?pid='.$r->id.'" ><img class="pdb_aw" alt="add '.stripcslashes($r->name).' to wishlist" src="/static/images/add-to-wishlist.png" /></a>
				<p class="pdb_p">'.($r->product_tabs == 0 ? "" : GetMinPriceProduct($r->id)).'</p>
			</div>
		</div>';
	}
    ?>
  </div>