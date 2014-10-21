<script type="text/javascript" src="/static/jquery.treeview/lib/jquery.cookie.js"></script>
<script type="text/javascript" src="/static/jquery.treeview/jquery.treeview.js"></script>
<link rel="stylesheet" type="text/css" href="/static/jquery.treeview/jquery.treeview.css" />

<div style="margin-top:5px;">
    <ol class="breadcrumb">
        <?php GenerateBreadcrumb($cat->category_name,"",$cat->id);?>
    </ol>
</div>

<?php
$menuname = "";
switch($leftpanelgroup)
{
    case "pl":
        $menuname = "Plants";
        break;
    case "ds":
        $menuname = "Designers";
        break;
    case "gi":
        $menuname = "Garden Ideas";
        break;
}
?>
<?php if ($menuname != ''):?>
    <div class="cref col-md-3 tbd">
        <?php if (count($selected_category) > 0):?>
            <h3 class="tbh">Selections</h3>
            <div class="tbc categorymenu" style="">
                <ul class="slct">
                    <?php
                    foreach ($selected_category as $ccc)
                    {
                        $catdata = mysql_query("SELECT * FROM product_category WHERE id='$ccc'");
                        while ($catdatarow = mysql_fetch_object($catdata))
                        {
                            echo '<li><a rel="dofollow" href="'.url_category($catdatarow->id).'?delone=1">'.$catdatarow->category_name.'</a></li>';
                        }
                    }
                    ?>
                </ul>
                <a rel="dofollow" href="<?php echo url_category($id)."?clearall=1&amp;lastgroup=".$leftpanelgroup;?>" class="clall">clear all</a>
            </div>
        <?php endif; ?>
        <h3 class="tbh">Refine By</h3>
        <div class="tbc categorymenu" style="">
            <h4><?php echo $menuname;?></h4>
            <ul id="browser1" class="browser treeview-famfamfam">
                <?php
                GetLeftMenu($leftpanelgroup,$menuname,0,$selected_property_root,$selected_category);
                ?>
            </ul>
        </div>
    </div>
<?php else:?>

    <div class="cref col-md-3 tbd">
        <h3 class="tbh">Refine By</h3>
        <div class="tbc categorymenu" style="">
            <h4>Plants</h4>
            <ul id="browser1" class="browser" class="filetree treeview-famfamfam">
                <?php
                GetLeftMenu("pl","Plants",0,$selected_property_root,$selected_category);
                ?>
            </ul>
            <h4>Garden Ideas</h4>
            <ul id="browser2" class="browser" class="filetree treeview-famfamfam">
                <?php
                GetLeftMenu("gi","Garden Ideas",0,$selected_property_root,$selected_category);
                ?>
            </ul>
            <h4>Designers</h4>
            <ul id="browser3" class="browser" class="filetree treeview-famfamfam">
                <?php
                GetLeftMenu("ds","Designers",0,$selected_property_root,$selected_category);
                ?>
            </ul>
        </div>
    </div>
<?php endif; ?>



<script>
    $("#browser1").treeview({collapsed: true,animated: "fast",persist: "location"});
    $("#browser2").treeview({collapsed: true,animated: "fast",persist: "location"});
    $("#browser3").treeview({collapsed: true,animated: "fast",persist: "location"});
    <?php if ($selected_category_root):?>
    	$("#tvc_<?php echo $selected_category_root;?> > ul").css("display","block");
    <?php endif; ?>
</script>
