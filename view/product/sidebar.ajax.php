  <?php
   session_start();
   $query1 = $_SESSION["q1"];
   $query2 = $_SESSION["q2"];
   $query3 = $_SESSION["q3"];

  $seicount = 0;
  for ($r as $query1)
  {
  	$seicount++;
  	echo '<div class="spi '.(($view != 'home' && $p->product_tabs == 1) && $seicount > 5 ? "sei" : "").'">
  		<a href="'.url_product_detail($r->id).'"><img alt="'.htmlentities($r->image_alt).'" class="spii" src="/scaleimage.php?w=54&amp;h=54&amp;t=productimage&amp;f='.$r->main_picture.'" width="54" height="54"/></a>
  		<div class="spic">
  			<h4>'.$r->name.'</h4>
  			<p>'.stripcslashes($r->intro).'</p>
  		</div>
  	</div>';
  }
  ?>
  </div>
  <div id="recent_plants" class="pdtc clearfix">
  <?php
  $seicount = 0;
  for ($r as $query2)
  {
  	$seicount++;
  	echo '<div class="spi '.(($view != 'home' && $p->product_tabs == 1) && $seicount > 5 ? "sei" : "").'">
  		<a href="'.url_product_detail($r->id).'"><img alt="'.htmlentities($r->image_alt).'" class="spii" src="/scaleimage.php?w=54&amp;h=54&amp;t=productimage&amp;f='.$r->main_picture.'" width="54" height="54" /></a>
  		<div class="spic">
  			<h4>'.$r->name.'</h4>
  			<p>'.stripcslashes($r->intro).'</p>
  		</div>
  	</div>';
  }
  ?>
  </div>


  
  <div id="related_plants" class="pdtc clearfix">
  <?php
  $seicount = 0;
  for ($r as $query3)
  {
  	$seicount++;
  	echo '<div class="spi '.(($view != 'home' && $p->product_tabs == 1) && $seicount > 5 ? "sei" : "").'">
  		<a href="'.url_product_detail($r->id).'"><img alt="'.htmlentities($r->image_alt).'" class="spii" src="/scaleimage.php?w=54&amp;h=54&amp;t=productimage&amp;f='.$r->main_picture.'" width="54" height="54" /></a>
  		<div class="spic">
  			<h4>'.$r->name.'</h4>
  			<p>'.stripcslashes($r->intro).'</p>
  		</div>
  	</div>';
  }
  ?>
  </div>
</div>

