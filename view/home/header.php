<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <title><?php echo @$page_title;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type">
    
    <?php
    if ($seo_description != '')
        echo '<meta name="description" content="'.htmlentities($seo_description).'" />
        ';
	else {
		echo '<meta name="description" content="'.htmlentities(GetSetting("site_description")).'" />
		';
	}
    ?>
    <?php
    if ($seo_keywords != '')
        echo '<meta name="keywords" content="'.htmlentities($seo_keywords).'" />
        ';
	else {
		echo '<meta name="keywords" content="'.htmlentities(GetSetting("site_keyword")).'"/>
		';
	}
    ?>

    <?php
       if ($canonical_URL != '')
          echo '<link rel="canonical" href="'.$canonical_URL.'" />';
    ?>
    

  </head>
  
    <body  oncontextmenu="return false;">

<!-- Start Alexa Certify Javascript -->
<script type="text/javascript">
_atrk_opts = { atrk_acct:"oKtNi1a4ZP00gW", domain:"jardins-sans-secret.com",dynamic: true};
(function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
</script>
<noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=oKtNi1a4ZP00gW" style="display:none" height="1" width="1" alt="" /></noscript>
<!-- End Alexa Certify Javascript -->

  <div id="fb-root"></div>
<script type="text/javascript">(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.async=true;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!--  
<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-42416916-1', 'jardins-sans-secret.com');
  ga('send', 'pageview');

</script>-->
  

  
<div class="topmenu_container">
	<div class="container topmenu_container_inner">
		<ul>
			<li><a href="/">Home</a></li>
			<li><a href="<?php echo url_cms(1);?>">About</a></li>
			<li><a href="<?php echo url_cms(5);?>">Contact Us</a></li>
			<!--li><a href="<?php echo url_cms(4);?>">Map</a></li-->
			<?php
			if (isset($_SESSION["isadmin"]))
                        {
                            //echo '<li><a href="/vendor-register.html">Merchant Center</a></li>';
                            echo  ' <li><a rel="nofollow" href="/admin.php">Admin Panel</a></li>';
                            echo  ' <li><a rel="nofollow" href="/admin.php?view=logout">Admin Logout</a></li>';
                        }
			?>
			<li><a rel="nofollow" href="/vendor-register.html">Merchant Center</a></li>
		</ul>
	</div>
</div>
  
  
  <div class="container" style="margin-top:4px;">
  <div class="header">
		<a rel="nofollow" href="/"><img alt="Jardins Sans Secret" src="/static/images/logo.png" class="main_logo" style='width:100%; max-width:408px'/></a>
                <a rel="nofollow" href="https://www.facebook.com/pages/Jardins-Sans-Secret/517793518306020" target="_blank" class="fanpagelogo"><img alt="Find us on facebook" src="/static/images/LikeUsOnFacebook_Icon.jpg" width="120" height="43"/></a>
		<div class="srcf">
                    <form id="search_form" class="form-inline" action="/category/search/search.html">
                        <input type="text" class="form-control" style="width:432px" value="<?php echo (isset($_GET["k"]) ? $_GET["k"] : "");?>" placeholder="Search Keyword" 
				required id="k" name="k" />
                                <input type="submit" class="btn" value="Search" />
                                <select class="form-control" style="width:200px;" name="glb_country" id="glb_country" onchange="CountryChange()">
					<option value="US">United States</option>
					<option value="CAN">Canada</option>
					<option value="FR">France</option>
					<option value="UK">United Kingdom</option>
					<option value="AU">Australia</option>
					<option value="DE">Germany</option>
					<option value="IT">Italy</option>
					<option value="NL">The Netherlands</option>
					<option value="CH">Switzerland</option>
					<option value="ES">Spain</option>
					
					<option value="euro_countries_dutchgardenworld">Austria</option>
					<option value="euro_countries_dutchgardenworld">Belgium</option>
					<option value="euro_countries_dutchgardenworld">Bulgaria</option>
					<option value="euro_countries_dutchgardenworld">China</option>
					<option value="euro_countries_dutchgardenworld">Corsica</option>
					<option value="euro_countries_dutchgardenworld">Croatia</option>
					<option value="euro_countries_dutchgardenworld">Cyprus</option>
					<option value="euro_countries_dutchgardenworld">Czech Republic</option>
					<option value="euro_countries_dutchgardenworld">Denmark</option>
					<option value="euro_countries_dutchgardenworld">Estonia</option>
					<option value="euro_countries_dutchgardenworld">Finland</option>
					<option value="euro_countries_dutchgardenworld">Gibraltar</option>
					<option value="euro_countries_dutchgardenworld">Greece</option>
					<option value="euro_countries_dutchgardenworld">Hungary</option>
					<option value="euro_countries_dutchgardenworld">Ireland</option>
					<option value="euro_countries_dutchgardenworld">Israel</option>
					<option value="euro_countries_dutchgardenworld">Japan</option>
					<option value="euro_countries_dutchgardenworld">Latvia</option>
					<option value="euro_countries_dutchgardenworld">Lithuania</option>
					<option value="euro_countries_dutchgardenworld">Luxembourg</option>
					<option value="euro_countries_dutchgardenworld">Malta</option>
					<option value="euro_countries_dutchgardenworld">Norway</option>
					<option value="euro_countries_dutchgardenworld">Poland</option>
					<option value="euro_countries_dutchgardenworld">Portugal</option>
					<option value="euro_countries_dutchgardenworld">Romania</option>
					<option value="euro_countries_dutchgardenworld">Russia</option>
					<option value="euro_countries_dutchgardenworld">Scotland</option>
					<option value="euro_countries_dutchgardenworld">Serbia/Montenegro</option>
					<option value="euro_countries_dutchgardenworld">Slovakia</option>
					<option value="euro_countries_dutchgardenworld">Slovenia</option>
					<option value="euro_countries_dutchgardenworld">Sweden</option>
					<option value="euro_countries_dutchgardenworld">Turkey</option>
					<option value="euro_countries_dutchgardenworld">Ukraine</option>
				</select>
			</form>
		</div>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript">
		  $(function() {
			var cache = {};
			$( "#k" ).autocomplete({
			  minLength: 2,
			  source: function( request, response ) {
				var term = request.term;
				if ( term in cache ) {
				  response( cache[ term ] );
				  return;
				}

				$.getJSON( "/autocompletesearch/index.html", request, function( data, status, xhr ) {
				  cache[ term ] = data;
				  response( data );
				});
			  }
			});
		  });
                  
		  </script>
		
				
				<script type="text/javascript">
				$(function(){
					$("#glb_country").val('<?php echo USER_REGION;?>');
				});
				function CountryChange()
				{
					$.post('/changecountry/index.html',{"country":$("#glb_country").val()}, function(data) {
						document.location.reload(true);
					});
					return false;
				}
				</script>
		
		
		<?php 
		$wishlist = unserialize($_SESSION["wishlist"]);
		$wl_total_price = 0;
		if ($wishlist != null && count($wishlist) > 0)
		{
			foreach ($wishlist as $w)
			{
				if ($w["type"] == "product")
				{
					$data = mysql_query("SELECT * FROM product WHERE id='".$w["id"]."'");
					$r = mysql_fetch_object($data);
					if ($r)
					{
						$price = GetMinPriceProduct($r->id);
						preg_match('/[0-9\.,]+/',$price,$match);
						$price = $match[0];
						if ($price != '')
							$wl_total_price += $price;
					}
				}
				elseif ($w["type"] == "seller")
				{
					$data = mysql_query("SELECT * FROM google_scraped WHERE id='".$w["id"]."'");
					$r = mysql_fetch_object($data);
					if ($r)
					{
						$wl_total_price += $r->price;
					}
				}
			}
			
			
			echo '<div class="slc">
				<img src="/static/images/bookmark_new_list.png" />
				<p><strong>'.count($wishlist).'</strong> item in your <strong>Wishlist</strong><br>
			Subtotal <strong>'.CURRENCY.$wl_total_price.'</strong> <a rel="nofollow" href="/wishlist/index.html">Review</a></p>
			</div>';
		}
		?>
		<nav id="mainmenu">
			<ul>
				<li><a href="/">Home</a></li>
				<?php GetMenu("gi","Garden Ideas",'color1');?>
				<?php GetMenu("ds","Designers",'color2');?>
				<li class="color3"><a href="<?php echo url_category_type("pr");?>">Promenades</a></li>
				<?php GetMenu("pl","Plants",'color4');?>
				<li class="color5"><a href="<?php echo url_category_type("bs");?>">Basics</a></li>
				<!--li><a style="width: 252px;" href="#">Collection Search</a></li-->
			</ul>
		</nav>
  </div>
  
  
    <script type="text/javascript"  src="/static/jqi.min.js.php"></script>
    <!--<script> 
     jQl.loadjQ('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js');
    </script>-->
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

    <script type="text/javascript"  src="/static/jquery.tooltipster.min.js.php"></script>
    <script type="text/javascript">
    function SetValidationForm(e,t){$(e+" *").tooltipster({trigger:"custom",onlyOne:false,position:t});$(e).validate({errorPlacement:function(e,t)     {$(t).tooltipster("update",$(e).text());$(t).tooltipster("show")},success:function(e,t){$(t).tooltipster("hide")}})}

    </script>
    <link rel="stylesheet" href="<?php echo BASE_URL."static/tooltipster.css.php";?>" />
     <link rel="stylesheet" href="<?php echo BASE_URL."static/bootstrap/css/bootstrap.min.css.php";?>" />
    <link rel="stylesheet" href="<?php echo BASE_URL."static/jquery-ui/css/jquery-ui.css.php";?>" />
    
    <link rel="stylesheet" href="<?php echo BASE_URL."static/style.css.php";?>" />
    <script type="text/javascript"  src="/static/jquery.validate.min.js.php"></script>
    <script type="text/javascript"  src="/static/jquery.tooltipster.min.js.php"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL."static/tooltipster.css.php";?>" />

    <script type="text/javascript" src="/static/jquery.idTabs.min.js"></script>
    <?php
    foreach ($script as $i)
		echo '
		<script type="text/javascript" src="'.$i.'"></script>
		';
    ?>

    <?php   
    foreach ($css as $i)
		echo '
		<link rel="stylesheet" type="text/css" href="'.$i.'" />
		';
    ?>
  
    <script>
    SetValidationForm("#search_form","top");
    </script>

<script>

var loaded=0;
var allImagesLoaded = 0;

function loadImages()
{
var n = 0;

    var elements= document.getElementsByClassName("lazy");

     for(i = 0; i < elements.length; i++)
      {
          if(elements[i].src=="")
          {
           elements[i].src = elements[i].getAttribute("data-original");
            n++; 
          }

       }

if(n == 0 && loaded==1)
{allImagesLoaded=1;
}

}

$(window).mousemove(function () {
if(loaded==1 && allImagesLoaded == 0)
{
  loadImages();
}

});

$( document ).ready(function() {
  
  loaded = 1;
});



</script>
  <div class="sitebody unselectable">
     