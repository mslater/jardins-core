<div itemscope itemtype="http://data-vocabulary.org/Product">

    <div class="" style="margin-top:5px;">
        <ol class="breadcrumb">
            <?php GenerateBreadcrumb($p->name, $p->id); ?>
        </ol>
    </div>

    <?php
    if ($p->wider_content == 0) {
        include "view/product/left_sidebar.php";
        echo ' <div class="col-md-6">';
    } else
        echo '<div class="col-md-9">';
    ?> 

    <div>
        <h1 class="product_name" itemprop="name"><?php echo stripcslashes($p->name); ?></h1>
        <h1 class="cmname"><?php echo stripcslashes($p->common_name); ?></h1>
        <div style="float: left;" id="star"></div>
        <span class="rating_block"> 
<?php if ($p->rating_count > 0): ?>
                <strong> <span itemprop="ratingValue" id="ratingValue" class="ratingValue"><?php echo number_format($p->rating, 1); ?></span> (based on <span itemprop="reviewCount" id="reviewCount"><?php echo $p->rating_count; ?></span> review<?php echo $p->rating_count > 1 ? "s" : ""; ?>)</strong>
            <?php else: ?>
                <strong><span>No reviews</span></strong>
            <?php endif; ?> 
        </span>
        <script type="text/javascript">
            $.fn.raty.defaults.path = '/static/raty/img';
            $('#star').raty({score: <?php echo $p->rating; ?>, click: function(score, evt) {
                    $(".product_detail_tabs > .ui-tabs-panel").hide();
                    $(".product_detail_tabs > #tabs-4").show();

                    $('#review_form').show();
<?php if ($p->product_tabs == 1): ?>
                        $('html, body').animate({
                            scrollTop: $("#tabs-4").offset().top
                        }, 1000);
<?php else: ?>
                        $('#reviewFormContainer').dialog({width: 610, height: 600});
<?php endif; ?>
                    $('#star2').raty({"score": score});
                    $('#rv_rating').val(score);
                }});
        </script>
        <input type="hidden"  content="in_stock" itemprop="availability" />
        <div class="ssnbs">

<?php
if ($_SESSION["isadmin"] === true)
    echo '<a href="/admin.php?view=products&amp;edit_id=' . $p->id . '" class="label label-primary">Edit Post</a> ';
?>

        </div>
    </div>

    <div <?php
            if ($p->wider_content == 0)
                echo 'class="product_description_container first cke_editable cke_editable_themed cke_contents_ltr cke_show_borders"';
            else
                echo 'class="product_description_container cke_editable cke_editable_themed cke_contents_ltr cke_show_borders"';
?> >  



<?php
if ($p->main_picture != '')
    if($p->wider_content == 0)
      echo '<img itemprop="image" alt="' . htmlentities($p->image_alt) . '" class="mainpicture" src="/scaleimage.php?o=1&amp;h=470&amp;w=470&amp;t=productimage&amp;f=' . $p->main_picture . '" />';
    else
      echo '<img itemprop="image" alt="' . htmlentities($p->image_alt) . '" class="mainpicture" src="/scaleimage.php?o=1&amp;h=600&amp;w=600&amp;t=productimage&amp;f=' . $p->main_picture . '" />';

?>
        <div class="published clear"><p>

        <?php if (!in_array('gi', $product_types) && !in_array('ds', $product_types)): ?>
                    <span class="pdpbd"><?php
        if ($p->product_tabs == 1) {
            if (in_array('gi', $product_types))
                echo GetTotalPriceGI($p->id, true);
            else
                echo GetMinPriceProduct($p->id, true);
        }
            ?></span>
                    <?php endif; ?>
                <a rel="nofollow" href="/wishlist/index.html?pid=<?= $p->id ?>"><img alt="wishlist" class="pdb_aw" src="/static/images/add-to-wishlist.png" style="top:-13px;position: absolute;" /></a>



            </p>

        </div>
<script type="text/javascript">
var addthis_config = addthis_config||{};
addthis_config.data_track_addressbar = false;
addthis_config.data_track_clickback = false;
</script>
        <div class="siic">            
            <!-- AddThis Button BEGIN -->
            <div class="addthis_toolbox addthis_default_style" style="width:400px;margin-top: 10px;">
            <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
            <a class="addthis_button_tweet"></a>
              <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
            <a class="addthis_button_pinterest_pinit" pi:pinit:layout="horizontal" pi:pinit:url="<?php echo "https://www.jardins-sans-secret.com".url_product_detail($p->id);?>" pi:pinit:media="http://www.addthis.com/cms-content/images/features/pinterest-lg.png"></a>
          
            </div>
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ngochoang"></script>
            <!-- AddThis Button END -->
        </div>


        <div id="product_description" itemprop="description">
<?php echo stripcslashes($p->description); ?>
        </div>
        <a style="float: right;margin-top: 10px;" id="product_description_toggle" href="javascript:ToggleDescription()">Read More</a>


<?php
if (in_array('gi', $product_types))
    include 'view/product/product_garden_idea.php';
?>
    </div>

</div>


<?php include "view/product/sidebar.php"; ?>

<?php
include "view/product/product_tabs.php";
?>


<script type="text/javascript">
                function ToggleDescription()
                {
                    if ($("#product_description").css("max-height") == "860px")
                    {
                        $("#product_description").css("max-height", "9999999px");
                        $("#product_description_toggle").html("Read Less");
                    }
                    else
                    {
                        $("#product_description").css("max-height", "860px");
                        $("#product_description_toggle").html("Read More");
                    }
                }

                function CheckReadMoreButton()
                {
                    var h1 = $("#product_description").height();
                    ToggleDescription();
                    var h2 = $("#product_description").height();
                    if (h1 == h2)
                        $("#product_description_toggle").remove();
                    ToggleDescription();
                }
                CheckReadMoreButton();

                if ($(".product_item_front img").length > 0)
                    $(".product_item_front img").tooltip({track: true, tooltipClass: "custom-tooltip-styling"});
                if ($(".picture_tooltip").length > 0)
                    $(".picture_tooltip").tooltip({track: true, tooltipClass: "custom-tooltip-styling"});
                if ($(".imgPreview").length > 0)
                    $(".imgPreview").imgPreview();
</script>


</div>