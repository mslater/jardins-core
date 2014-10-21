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

<div style="margin-top:7px;"  id="sidebarprodl" class="product_detail_tabs product_recent">
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
  $query = mysql_query("SELECT * FROM product WHERE status=1 AND publishing_date < '".time()."' ORDER BY LOG10(view_count)*LOG10(".time()." - publishing_date) DESC LIMIT 10");
  $seicount = 0;
  while ($r = mysql_fetch_object($query))
  {
  	$seicount++;
  	echo '<div class="spi '.(($view != 'home' && $p->product_tabs == 1) && $seicount > 5 ? "sei" : "").'">
  		<a href="'.url_product_detail($r->id).'"><img alt="'.htmlentities($r->image_alt).'" class="spii lazy" data-original="/scaleimage.php?w=54&amp;h=54&amp;t=productimage&amp;f='.$r->main_picture.'" width="54" height="54"/></a>
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
  $query = mysql_query("SELECT * FROM product WHERE status=1 AND publishing_date < '".time()."' ORDER BY created_date DESC LIMIT 10");
  $seicount = 0;
  while ($r = mysql_fetch_object($query))
  {
  	$seicount++;
  	echo '<div class="spi '.(($view != 'home' && $p->product_tabs == 1) && $seicount > 5 ? "sei" : "").'">
  		<a href="'.url_product_detail($r->id).'"><img alt="'.htmlentities($r->image_alt).'" class="spii lazy" data-original="/scaleimage.php?w=54&amp;h=54&amp;t=productimage&amp;f='.$r->main_picture.'" width="54" height="54" /></a>
  		<div class="spic">
  			<h4>'.$r->name.'</h4>
  			<p>'.stripcslashes($r->intro).'</p>
  		</div>
  	</div>';
  }
  ?>
  </div>



  <?php if($view != 'home') echo '<div id="related_plants" class="pdtc clearfix">' ?>
    
  
  <?php

   if($view != 'home')
     {
    

    $query = mysql_query("Select * FROM requirement WHERE id = $p->requirement_id");
    $r = mysql_fetch_object($query);
   
    $hardiness = explode("- ", $r->hardiness);
    $minH = intval($hardiness[0]);
    $maxH = intval($hardiness[1]);

    $query = mysql_query("SELECT category_id FROM `product_in_category`  INNER JOIN product_category ON category_id = product_category.id WHERE product_id = $p->id AND category_id <> 1045 AND category_id <> 1049 AND category_id <> 1053 AND category_id <> 1122 AND category_id <> 1126 AND category_id <> 1130 AND ( (category_id between 1121 AND 1130) or (category_id between 1044 and 1053) ) ORDER BY category_id ASC");
    
    $minP1 = 0;
    $minP2 = 0;
    $maxP1 = 0;
    $maxP2 = 0;
    $cats = Array();


    while( ($s = mysql_fetch_object($query)) )
     { 
     
        $r =  $s->category_id;

       if($minP1 == 0)
         {
           if($r <= 1053) 
            {
              $minP1 = 1;
            }
         }

         if($r <= 1053 && $maxP1 < $r)
         {
          $maxP1 = $r;
         }


        if($minP2 == 0)
         {
           if($r > 1053 &&  $r <= 1130) 
            {
              $minP2 = 1;
            }
         }

         if($r > 1053 && $r <= 1130 && $maxP2 < $r)
         {
          $maxP2 = $r;
         }
     
      array_push($cats,$r);
     }
    

  if($minP1 == 0 && $minP2 != 0)
   { 
     $l = count($cats);
     for($i = 0; $i < $l; $i++)
      { 
       array_push($cats,$cats[$i]-77);
      }
   }
  else if($minP1 != 0 && $minP2 == 0)
   { 
     $l = count($cats);
     for($i = 0; $i < $l; $i++)
      { 
       array_push($cats,$cats[$i]+77);
      }

   }


    if($minP1 ==  0 && $minP2 == 0)
     {
       exit(); 
     }




    /*
     
  $query = mysql_query("Select * from requirement INNER JOIN (
Select * from product where product.requirement_id in (Select requirement.id  from requirement INNER JOIN (SELECT product.id, hardiness, sun1, sun2, sun3, sun4, periodofinterest FROM product INNER JOIN requirement on requirement.id = product.requirement_id and product.id = $p->id) as Params on requirement.periodofinterest = Params.periodofinterest  AND (requirement.sun1 = Params.sun1 or requirement.sun1 = Params.sun2 or requirement.sun1 = Params.sun3 OR (requirement.sun2 <> '' AND (requirement.sun2 = Params.sun1 or requirement.sun2 = Params.sun2 or requirement.sun2 = Params.sun3)) OR (requirement.sun3 <> '' AND (requirement.sun3 = Params.sun1 or requirement.sun3 = Params.sun2 or requirement.sun3 = Params.sun3)) )) and product.id <> $p->id  and product.status = 1 ORDER BY LOG10(view_count)*LOG10(".time()." - publishing_date) DESC) as Temp on requirement.id = Temp.requirement_id");
*/



$query = mysql_query("SELECT product_id FROM `product_in_category`  INNER JOIN product_category ON category_id = product_category.id WHERE category_id in (".implode(',', $cats).") GROUP by category_id HAVING count(*) = ".(count($cats)/2)." or having count(*) =".count($cats)." ORDER BY category_id ASC");

 
/*echo "SELECT product_id FROM `product_in_category`  INNER JOIN product_category ON category_id = product_category.id WHERE category_id in (".implode(',', $cats).") GROUP by category_id HAVING count(*) = ".(count($cats)/2)." or having count(*) =".count($cats)." ORDER BY category_id ASC";*/


$query = mysql_query("SELECT product_id FROM `product_in_category` INNER JOIN product_category ON category_id = product_category.id WHERE category_id in (".implode(',', $cats).") ORDER BY category_id ASC");

$p_ids = Array();
while($s = mysql_fetch_object($query)){
  array_push($p_ids, $s->product_id);
}


  $query = mysql_query("Select * from requirement INNER JOIN (
Select * from product where product.requirement_id in (Select requirement.id  from requirement INNER JOIN (SELECT product.id, hardiness, sun1, sun2, sun3, sun4, periodofinterest FROM product INNER JOIN requirement on requirement.id = product.requirement_id and product.id = $p->id) as Params on (requirement.sun1 = Params.sun1 or requirement.sun1 = Params.sun2 or requirement.sun1 = Params.sun3 OR (requirement.sun2 <> '' AND (requirement.sun2 = Params.sun1 or requirement.sun2 = Params.sun2 or requirement.sun2 = Params.sun3)) OR (requirement.sun3 <> '' AND (requirement.sun3 = Params.sun1 or requirement.sun3 = Params.sun2 or requirement.sun3 = Params.sun3)) )) and product.id <> $p->id  and product.status = 1 ORDER BY LOG10(view_count)*LOG10(".time()." - publishing_date) DESC) as Temp on requirement.id = Temp.requirement_id");


   
  $seicount = 0;
  while ($seicount < 10 && ($r = mysql_fetch_object($query)))
  {

    $hardiness = explode("- ", $r->hardiness);
    $minH2 = intval($hardiness[0]);
    $maxH2 = intval($hardiness[1]);
    
    if( in_array($r->id, $p_ids) && ( ($minH >= $minH2 && $maxH <= $maxH2) || ($minH <= $minH2 && $maxH >= $maxH2) ) )
     { 

  	$seicount++;
  	echo '<div class="spi '.(($view != 'home' && $p->product_tabs == 1) && $seicount > 5 ? "sei" : "").'">
  		<a href="'.url_product_detail($r->id).'"><img alt="'.htmlentities($r->image_alt).'" class="spii lazy" data-original="/scaleimage.php?w=54&amp;h=54&amp;t=productimage&amp;f='.$r->main_picture.'" width="54" height="54" /> </a>
  		<div class="spic">
  			<h4>'.$r->name.'</h4>
  			<p>'.stripcslashes($r->intro).'</p>
  		</div>
  	</div>';
  }
  }
  }
  ?>

  <?php if($view != 'home') echo '</div>' ?>
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
var p = document.getElementById('product_description');
var s = document.getElementById('sidebarprodl');
var r1 = document.getElementById('related_plants');
var r2 = document.getElementById('popular_plants');
var r3 = document.getElementById('recent_plants');

 if(p != null)
  {
 //   p.style.maxHeight="999999px";

   var sr1 =  r1.style.display;
   var sr2 =  r2.style.display;
   var sr3 =  r3.style.display;

   // r1.style.display='none';
   // r2.style.display='none';
   // r3.style.display='none';
   
   //s.parentNode.style.height=(p.parentNode.parentNode.clientHeight + 20) + "px";
//   s.parentNode.style.overflow="hidden";

 //   var h =p.parentNode.offsetHeight - s.parentNode.parentNode.offsetHeight;

   // p.style.maxHeight="860px";
    //s.style.maxHeight=h + "px";
    //s.style.display='';

  

   // r1.style.display=sr1;
   // r2.style.display=sr2;
   // r3.style.display=sr3;
    
   }
</script>

<?php  if ($view != 'home' && $p->product_tabs == 1){
        echo '<script>ShowSidebarProducts()</script>';}
?>



</div>