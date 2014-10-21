
<div style="display:none;">
	<script type="text/javascript">
		<!--//--><![CDATA[//><!--
			var images = new Array()
			function preload() {
				for (i = 0; i < preload.arguments.length; i++) {
					images[i] = new Image()
					images[i].src = preload.arguments[i]
				}
			}
			preload(
			<?php
			$plcontent = "";
			foreach ($images as $i)
			{
				if ($plcontent == '')
					$plcontent .= '"/scaleimage.php?t=productimage&amp;w=254&amp;h=167&amp;f='.urlencode($i["name"]).'"';
				else
					$plcontent .= ',"/scaleimage.php?t=productimage&amp;w=254&amp;h=167&amp;f='.urlencode($i["name"]).'"';
			}
			
			echo $plcontent;
			?>
			);
		//--><!]]>
	</script>
</div>

<div class="col-md-3 first" style="margin-top:0px;">
 	<?php if (count($images) > 0):?>
 	<div>
 		<a class="full_image" title="<?php echo $images[0]["caption"];?>" href="<?php echo '/upload/product_images/'.$images[0]["name"]; ?>"><img  class="showing_image" src="<?php echo '/scaleimage.php?t=productimage&amp;w=254&amp;h=167&amp;f='.urlencode($images[0]["name"]);?>"  alt="<?php echo htmlentities($images[0]["alt"]);?>" /></a>
 	</div>
 	<div id="productimagescont">
		<div id="productimages">
		<?php
		foreach ($images as $i)
		{
			echo '<img alt="'.htmlentities($i["alt"]).'" onClick="ShowPicImage(\''.str_replace("'","\\'",str_replace("\"","'",$i["caption"])).'\',\''.urlencode($i["name"]).'\')" src="/scaleimage.php?t=productimage&amp;w=75&amp;h=75&amp;f='.urlencode($i["name"]).'" width="75" height="75" />
			';
		}
		?>
		</div>
		<div class="clearfix"></div>
		 <a class="prev" id="gl_prev" href="#"><span>prev</span></a>
		 <a class="next" id="gl_next" href="#"><span>next</span></a>
  </div>
  <?php endif; ?>
  
  


 
 </div>
 
  <script type="text/javascript">

$(document).ready(function() {
    $("#productimages").carouFredSel({
		items				: 3,
		auto    : false,
		height: 150,
		prev	: {	
			button	: "#gl_prev"
		},
		next	: { 
			button	: "#gl_next"
		}
	});

});

$('.full_image').lightBox({
	imageLoading: '/static/images/lightbox-ico-loading.gif',
	imageBtnClose: '/static/images/lightbox-btn-close.gif',
	containerResizeSpeed  : 100
});
$('.full_image').append('<img class="ad-zoom" alt="magnify" src="/static/images/item-card-image-magnify.png" style="margin-left:144.92px;">');
function ShowPicImage(caption,img)
{
	$(".showing_image").attr("src","/scaleimage.php?t=productimage&w=254&h=167&f=" + img);
	$(".full_image").attr("href","/upload/product_images/" + img);
	$(".full_image").attr("title",caption);
	$('.full_image').lightBox({
		imageLoading: '/static/images/lightbox-ico-loading.gif',
		imageBtnClose: '/static/images/lightbox-btn-close.gif',
		containerResizeSpeed  : 100
	});
}

</script>