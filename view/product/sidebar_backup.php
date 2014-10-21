<?php
session_start();
$_SESSION["p"] = $p;
?>
<script>
$.ajax({ type: "GET",   
         url: "/view/product/sidebar.ajax.php",   
         async: true,
         success : function(text)
         {
            document.getElementById("SideBarAjaxResponse").innerHTML = text;
         }
});
</script>


<div id="SideBarAjaxResponse"/>
<?php exit?>

<div class="col-md-3">
<?php
global $view;
?>
<?php if (@$p->requirement_id != 0) :?>
<div class="tbd">
	<h3 class="tbh">Requirement</h3>
	<div class="tbc" style="width: 253px;">
		<?php echo GetRequirementForm($p->requirement_id);?>
	</div>
</div>
<?php endif;?>

<?php if ($p->contact_form_id != 0) :?>
<div class="tbd contactform">
	<h3 class="tbh">Contact</h3>
	<div class="tbc" style="">
		<?php echo GetForm($p->contact_form_id);?>
	</div>
</div>
<?php endif;?>

<?php
if ($p->map_address != '')
{
	echo '<a style="display: block;width: 253px;" target="_blank" href="https://maps.google.com/?q='. urlencode($p->map_address).'"><img src="http://maps.googleapis.com/maps/api/staticmap?center='. urlencode($p->map_address).'&amp;size=300x300&amp;maptype=roadmap&amp;markers=color:blue%7Clabel:A%7C'. urlencode($p->map_address).'&amp;sensor=false" /></a><br><a href="https://maps.google.com/?q='. urlencode($p->map_address).'" target="_blank" class="quiet" style="text-decoration:none;">View large map</a>';
}
?>


<?php if (@$p->calculator_name !='' && $p->calculator_size != ''):?>
	<script src="/static/c.js.php"></script>
	<button class="jqueryui-button" onClick="$('#newcalculator').dialog({width:480});" style="margin-top:7px;width:255px;">Open Calculator</button>
	<?php echo GetCalculator(stripcslashes($p->calculator_name),stripcslashes($p->calculator_size));?>
<?php endif;?>

<?php if (isset($p) && $p->product_tabs == 0):?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.product_bottom_tabs').hide();
		});
	</script>
	<button class="jqueryui-button" onClick="$('#review_form').show();$('#reviewFormContainer').dialog({width:610,height:600});" style="margin-top:7px;width:255px;">Customer Reviews (<?php echo $p->rating_count;?>)</button>
<?php endif;?>

<div style="margin-top:7px;" id="sidebarprodl" class="product_detail_tabs product_recent">
  <ul>
    <?php 
       if($view != 'home')
        {
          echo '<li><a href="#related_plants">May We Suggest</a></li>';
        }
    ?>

    <li><a href="#popular_plants">Popular</a></li>
    <li><a href="#recent_plants">Recent</a></li>
  </ul>
  <div id="popular_plants" class="pdtc clearfix">
  <?php
  $query = mysql_query("SELECT * FROM product WHERE status=1 AND publishing_date < '".time()."' ORDER BY LOG10(view_count)*LOG10(".time()." - publishing_date) DESC LIMIT 15");
  $seicount = 0;
  while ($r = mysql_fetch_object($query))
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
  $query = mysql_query("SELECT * FROM product WHERE status=1 AND publishing_date < '".time()."' ORDER BY created_date DESC LIMIT 15");
  $seicount = 0;
  while ($r = mysql_fetch_object($query))
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
  $query = mysql_query("SELECT * FROM product WHERE id IN (SELECT DISTINCT product_id FROM product_in_category WHERE category_id IN (SELECT DISTINCT category_id FROM product_in_category WHERE product_id = $p->id)) and status=1 ORDER BY LOG10(view_count)*LOG10(".time()." - publishing_date) and id <> $p->id DESC LIMIT 15");
  $seicount = 0;
  while ($r = mysql_fetch_object($query))
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


<script>
$("#sidebarprodl").idTabs("!mouseover"); 

function ShowSidebarProducts()
{
	if ($(".sei").css("display") == "none")
	{
		$(".sei").css("display","block");
		$("#sblink").html("View Less");
	}
	else
	{
		$(".sei").css("display","none");
		$("#sblink").html("View More");
	}
}
</script>
<?php  if ($view != 'home' && $p->product_tabs == 1){
	echo '<a href="javascript:ShowSidebarProducts()" id="sblink" >View More</a>';
} ?>

<script>
ShowSidebarProducts()
</script>


</div>