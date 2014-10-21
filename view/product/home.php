<!------------SLIDER------------>
<!-- is this file being edited ap 1-20-2014 testing......... -->
<link rel="stylesheet" href="/static/nivo-slider/v2/nivo-slider.css" type="text/css"/>
<script type="text/javascript" src="/static/nivo-slider/v2/jquery.nivo.slider.pack.js"></script>

<div id="slider" class="nivoSlider">
    <!-- lazy loading -->
	<?php
    $imc = 0;
	while ($r = mysql_fetch_object($dataslider))
	{
		if ($r->link != '')
		{
            if($imc == 0){
                echo '<a href="'.$r->link.'"><img src="/scaleimage.php?w=1046&h=450&t=productimage&f='.$r->image.'" alt="'.stripcslashes($r->caption).'" title="'.stripcslashes($r->caption).'" /></a>';
                $imc++;
            }else{
                echo '<a href="'.$r->link.'"><img src="" data-src="/scaleimage.php?w=1046&h=450&t=productimage&f='.$r->image.'" alt="'.stripcslashes($r->caption).'" title="'.stripcslashes($r->caption).'" /></a>';
            }

		}
		else
		{
            if($imc == 0){
                echo '<img src="/scaleimage.php?w=1046&h=450&t=productimage&f='.$r->image.'" alt="'.stripcslashes($r->caption).'" title="'.stripcslashes($r->caption).'"  />';
                $imc++;
            }else{
                echo '<img src="" data-src="/scaleimage.php?w=1046&h=450&t=productimage&f='.$r->image.'" alt="'.stripcslashes($r->caption).'" title="'.stripcslashes($r->caption).'"   />';
            }


		}
	}
	?>
</div>


<script type="text/javascript">
	$('#slider').nivoSlider({
		effect: "fold",
		directionNav: false, 
        controlNav: false,
        animSpeed: 500,
        pauseTime: 7500
        });
</script>
<!------------END SLIDER------------>
<div class="row">
<div class="col-md-9">
	<div class="style3">
		<div class="hentry1 color1">
			<h1>Garden Ideas</h1>
			<?php
			GetFeaturedProducts("gi");
			?>
		</div>
	</div>
	
	
	<div class="style3">
		<div class="hentry1 color2">
			<h1>Designers</h1>
			<?php
			GetFeaturedProducts("ds");
			?>
		</div>
	</div>
	
	
	<div class="style3">
		<div class="hentry1 color3">
			<h1>Promenades</h1>
			<?php
			GetFeaturedProducts("pr");
			?>
		</div>
	</div>
	
	
	<div class="style3">
		<div class="hentry1 color4">
			<h1>Plants</h1>
			<?php
			GetFeaturedProducts("pl");
			?>
		</div>
	</div>
	
	
	<div class="style3">
		<div class="hentry1 color5">
			<h1>Basics</h1>
			<?php
			GetFeaturedProducts("bs");
			?>
		</div>
	</div>
</div>
<?php include "view/product/sidebar.php";?>

</div>