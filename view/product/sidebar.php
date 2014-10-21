<div class="col-md-3" style="overflow:hidden">
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
  		<a href="'.url_product_detail($r->id).'"><img alt="'.htmlentities($r->image_alt).'" class="spii lazy" data-original="/scaleimage.php?w=84&amp;h=84&amp;t=productimage&amp;f='.$r->main_picture.'" width="84" height="84"/></a>
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
  		<a href="'.url_product_detail($r->id).'"><img alt="'.htmlentities($r->image_alt).'" class="spii lazy" data-original="/scaleimage.php?w=84&amp;h=84&amp;t=productimage&amp;f='.$r->main_picture.'" width="84" height="84" /></a>
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
    
    if($r == null)
     {
        $rnd = rand();
       $query = mysql_query("SELECT * FROM product WHERE product.id IN (SELECT product_id FROM product_in_category WHERE category_id IN (SELECT category_id FROM `product_in_category` WHERE product_id = $p->id and category_id IN (1024, 1025, 1026, 1026, 1029,1030, 1031, 1032,1033, 1034, 1179, 1180))) AND product.id <> $p->id and product.status = 1 ORDER by RAND($rnd) limit 10");
       
       $r = mysql_fetch_object($query);
       
       $seicount = 0;
       while ($seicount < 10 && ($r = mysql_fetch_object($query)))
       {
          
                   $seicount++;
   	           echo '<div class="spi '.(($view != 'home' && $p->product_tabs == 1) && $seicount > 5 ? "sei" : "").'">';
                   echo '<a href="'.url_product_detail($r->id).'">';
                   echo '<img alt="'.htmlentities($r->image_alt).'" class="spii lazy" data-original="/scaleimage.php?w=84&amp;h=84&amp;t=productimage&amp;f='.$r->main_picture.'" width="84" height="84" /> </a>';
                   echo '<div class="spic"><h4>'.$r->name.'</h4><p>'.stripcslashes($r->intro).'</p></div></div>';
       }

     }
   else
    {
    $hardiness = explode("- ", $r->hardiness);
    $minH = intval($hardiness[0]);
    $maxH = intval($hardiness[1]);

    $periods = array(
      "Early Spring" => 1,
      "Mid Spring" => 2,
      "Late Spring" => 3,
      "Early Summer" => 4,
      "Mid Summer" => 5,
      "Late Summer" => 6,
      "Early Fall" => 7,
      "Fall" => 8,
      "Frost" => 9
    );

    $period = explode(" - ", $r->periodofinterest);
    
    $minP = $period[0];
    $maxP = $period[0];
    $pC = 1;

    if(count($period) == 2) 
     {
      $minP = $period[0];
      $maxP = $period[1];
      $pC = 2;

     }
      
    
     if( ( array_key_exists($minP,$periods) && array_key_exists($maxP,$periods) ) || $r->periodofinterest == "All Year Around")
     {
        if($r->periodofinterest != "All Year Around")
         {
           $minP = $periods[$minP];
           $maxP = $periods[$maxP];
         }



         $rnd = rand();

        $query = mysql_query("Select * from requirement INNER JOIN (Select * from product where product.requirement_id in (Select requirement.id  from requirement INNER JOIN (SELECT product.id, hardiness, sun1,sun2, sun3, sun4, periodofinterest FROM product INNER JOIN requirement on requirement.id = product.requirement_id and product.id = $p->id) as Params on (requirement.sun1 = Params.sun1 or requirement.sun1 = Params.sun2 or requirement.sun1 = Params.sun3 OR (requirement.sun2 <> '' AND (requirement.sun2 = Params.sun1 or requirement.sun2 = Params.sun2 or requirement.sun2 = Params.sun3)) OR (requirement.sun3 <> '' AND (requirement.sun3 = Params.sun1 or requirement.sun3 = Params.sun2 or requirement.sun3 = Params.sun3)) )) and product.id <> $p->id  and product.status = 1 ORDER BY RAND($rnd) DESC) as Temp on requirement.id = Temp.requirement_id");
   
        $seicount = 0;
        while ($seicount < 10 && ($r = mysql_fetch_object($query)))
         {

           $hardiness = explode("- ", $r->hardiness);
           $minH2 = intval($hardiness[0]);
           $maxH2 = intval($hardiness[1]);
         
    
           $period = explode(" - ", $r->periodofinterest);
    
           $minP2 = $period[0];
           $maxP2 = $period[0];
          
           if(count($period) != $pC)
           { 
            continue;
           }

           if(count($period) == 2) 
            {
              $minP2 = $period[0];
              $maxP2 = $period[1];
            }

           if( ( array_key_exists($minP2, $periods) && array_key_exists($maxP2, $periods) ) || $r->periodofinterest == "All Year Around" )
            {
               
              if($r->periodofinterest != "All Year Around")
               {
                 $minP2 = $periods[$minP2];
                 $maxP2 = $periods[$maxP2];
               }

 
              if( ( $r->periodofinterest == "All Year Around" || ( ($minP >= $minP2 && $maxP <= $maxP2) || ($minP <= $minP2 && $maxP >= $maxP2) ) ) &&
                  ( ($minH >= $minH2 && $maxH <= $maxH2) || ($minH <= $minH2 && $maxH >= $maxH2) )
                )
                { 
                     $results = true;
                    
                   $seicount++;
   	           echo '<div class="spi '.(($view != 'home' && $p->product_tabs == 1) && $seicount > 5 ? "sei" : "").'">';
                   echo '<a href="'.url_product_detail($r->id).'">';
                   echo '<img alt="'.htmlentities($r->image_alt).'" class="spii lazy" data-original="/scaleimage.php?w=84&amp;h=84&amp;t=productimage&amp;f='.$r->main_picture.'" width="84" height="84" /> </a>';
                   echo '<div class="spic"><h4>'.$r->name.'</h4><p>'.stripcslashes($r->intro).'</p></div></div>';
                }
            }

         }
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

var s = document.getElementById('sidebarprodl');
var r1 = document.getElementById('related_plants');
var r2 = document.getElementById('popular_plants');
var r3 = document.getElementById('recent_plants');

function ops()
{
var p = document.getElementById('product_description');
var h = p.parentNode.parentNode.offsetHeight;

s.parentNode.style.height=(h) + "px";
//console.log(h);
setTimeout(ops,1000);
}

$( document ).ready(function() {
//  ops();
});

setTimeout(ops,1000);



</script>

<?php  if ($view != 'home' && $p->product_tabs == 1){
        echo '<script>ShowSidebarProducts()</script>';}
?>



</div>