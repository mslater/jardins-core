
<div class="pcl col-md-9">
    <h1 class="product_name"><?php
        switch ($id)
        {
            case "gi":
                echo "Garden Ideas";
                break;
            case "ds":
                echo "Designers";
                break;
            case "bs":
                echo "Basics";
                break;
            case "pl":
                echo "Plants";
                break;
            case "pr":
                echo "Promenades";
                break;
        }
        echo stripcslashes(@$cat->category_name);
        ?></h1>

    <?php
    if ($_SESSION["isadmin"] == true)
    {
        echo '<a href="/admin.php?view=categories&amp;catsec='.$cat->category_type.'&amp;parent_id='.$cat->id.'" class="label label-primary">Edit This Category</a> ';
    }
    ?>

    <div class="clis">
        <?php
        /*
        while ($rsp = mysql_fetch_object($catcdata))
        {
            if ($rsp->only_show_treeview == 0)
            {
                $datac = mysql_query("SELECT COUNT(*) AS counter FROM product_in_category
                    INNER JOIN product ON product.id=product_in_category.product_id
                    WHERE category_id='{$rsp->id}' AND product.status=1") or die (mysql_error());
                $countrow = mysql_fetch_object($datac);
                if ($countrow->counter > 0)
                {
                    echo '<div class="clisi">
                                    <a href="'.url_category($rsp->id).'"><img alt="Category '.htmlentities($rsp->category_name).'" title="Category '.htmlentities($rsp->category_name).'" src="/scaleimage.php?w=145&amp;h=145&amp;t=productimage&amp;f='.$rsp->picture.'" /></a>
                                    <a class="itl" href="'.url_category($rsp->id).'">'.stripcslashes($rsp->category_name).' ('.$countrow->counter.')</a>
                            </div>';
                }
            }
        }
         
         */
        while ($rsp = mysql_fetch_object($catcdata))
        {
            if ($rsp->only_show_treeview == 0)
            {
                    echo '<div class="clisi">
                                    <a href="'.url_category($rsp->id).'"><img alt="Category '.htmlentities($rsp->category_name).'" title="Category '.htmlentities($rsp->category_name).'" src="/scaleimage.php?w=145&amp;h=145&amp;t=productimage&amp;f='.$rsp->picture.'" /></a>
                                    <a class="itl" href="'.url_category($rsp->id).'">'.stripcslashes($rsp->category_name).'</a>
                            </div>';
             
            }
        }
        ?>
    </div>
</div>