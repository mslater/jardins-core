<?php
if ($_SESSION["isadmin"] == true)
{
    echo '<a rel="dofollow" href="/admin.php?view=categories&amp;catsec='.$cat->category_type.'&amp;parent_id='.$cat->id.'" class="label label-primary">Edit This Category</a> ';
}
?>

<div class="pcl col-md-9">
    <h1 class="product_name"><?php echo $cat->category_name;?></h1>
    <div class="ctool">

        <div class="form-inline">
            <span>View: </span>
            <select class="form-control" style="width: 150px;height: 26px;" id="from">
                <option value="8">View 8 items</option>
                <option value="16">View 16 items</option>
                <option value="20">View 20 items</option>
                <option value="32">View 32 items</option>
                <option value="50">View 50 items</option>
                <option value="64">View 64 items</option>
                <option value="100">View 100 items</option>
            </select>
        
        <script>
            $("#from").change(function(){
                <?php
                if ($id == "search")
                    echo 'window.location = "/category/search/search.html?k='.$k.'&f=0&append=1&ps=" + $(this).val();';
                else
                    echo 'window.location = "'.url_category($id).'?f=0&append=1&ps=" + $(this).val();';
                ?>
            });
            $("#from").val("<?php echo $pagesize; ?>");
        </script>
        <div class="paging">
            <?php
            if ($from == 0)
                echo '<span class="page previous-page disabled">&lt; Prev</span>';
            else
            {
                if ($id == "search")
                    echo '<a class="page previous-page" href="/category/search/search.html?k='.$k.'&amp;f='.($from-$pagesize).'&amp;append=1&amp;ps='.$pagesize.'">&lt; Prev</a>';
                else
                    echo '<a class="page previous-page" href="'.url_category($id).'?f='.($from-$pagesize).'&amp;append=1&amp;ps='.$pagesize.'">&lt; Prev</a>';
            }
            $p = 1;
            $totalp = $cachedproductCount;
            $fromt = $from - 3*$pagesize;
            if ($fromt < 0)
                $fromt = 0;
            $countt = 0;
            for ($i = $fromt;$i<$totalp;$i += $pagesize)
            {
                $countt++;
                if ($countt > 15)
                    break;
                $p = $i/$pagesize + 1;
                if ($i == $from)
                    echo '<span class="page current-page">'.$p.'</span>';
                else
                {
                    if ($id == "search")
                        echo '<a class="page" href="/category/search/search.html?k='.$k.'&amp;f='.$i.'&amp;append=1&amp;ps='.$pagesize.'">'.$p.'</a>';
                    else
                        echo '<a class="page" href="'.url_category($id).'?f='.$i.'&amp;append=1&amp;ps='.$pagesize.'">'.$p.'</a>';
                }
            }
            if ($from + $pagesize >= $totalp)
                echo '<span class="page next-page disabled">Next &gt;</span>';
            else
            {
                if ($id == "search")
                    echo '<a class="page next-page" href="/category/search/search.html?k='.$k.'&amp;f='.($from+$pagesize).'&amp;append=1&amp;ps='.$pagesize.'">Next &gt;</a>';
                else
                    echo '<a class="page next-page" href="'.url_category($id).'?f='.($from+$pagesize).'&amp;append=1&amp;ps='.$pagesize.'">Next &gt;</a>';
            }
            ?>
            </span>
        </div>
        </div>
    </div>

    <div class="pclis">
        <?php

        if (count($cachedproducts) == 0)
        {
            echo '<div class="notice">There is no product in this section. We will update later.</div>';
        }
        foreach ($cachedproducts as $rsp)
        {
            $cats = GetCategories($rsp->id);
			$tag = "From";
			if (in_array('pl',$cats) || in_array('bs',$cats))
				$tag = "Price";
			if (!in_array('gi',$cats) && !in_array('gi',$cats))
			{
	            if (in_array('gi',$cats))
	                $price = GetTotalPriceGI($rsp->id,false,$tag);
	            else
	                $price = GetMinPriceProduct($rsp->id,false,$tag);
            }
			$stars = GetStars($rsp->rating);
            echo '<div class="pdb pdbc">
			<a href="'.url_product_detail($rsp->id).'"><img alt="'.htmlentities($rsp->image_alt).'" title="'.htmlentities($rsp->image_alt).'" src="/scaleimage.php?w=172&amp;h=172&amp;t=productimage&amp;f='.$rsp->main_picture.'" class="pdb_i" /></a>
			<div class="pdb_inf">
				'. ($rsp->reward_icon != '' ? '<img alt="'.htmlentities($rsp->image_alt).'" title="'.htmlentities($rsp->image_alt).'" src="/scaleimage.php?w=45&amp;h=45&amp;t=productimage&amp;f='.$rsp->reward_icon.'" class="pdbcl">' : "" ).'
				<h4>'.stripcslashes($rsp->name).'</h4>
				<p>'.stripcslashes($rsp->intro).'</p>
			</div>
			<div class="pdb_c">
				<div class="pdbst">'.$stars.' <strong class="ratingValue2">Reviews ('. $rsp->rating_count.') </strong></div>
				<a rel="dofollow" href="/wishlist/index.html?pid='.$rsp->id.'" ><img class="pdb_aw" src="/static/images/add-to-wishlist.png" /></a>
				<p class="pdb_p">'.($rsp->product_tabs == 0 ? "" : $price).'</p>
			</div>
		</div>';
        }
        ?>
    </div>
</div>