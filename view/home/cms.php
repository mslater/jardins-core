<?php
if (isset($info) && $info != '')
    echo '<div class="alert alert-info">'.$info.'</div>';
if (isset($error) && $error != '')
    echo '<div class="alert alert-danger">'.$error.'</div>';
?>

<div class="cms_container">
	<h2 class="cms_title"><?php echo $rcms->title;?></h2>
	<?php
	if ($_SESSION["isadmin"] === true)
		echo '<a href="/admin.php?view=cms_edit&id='.$rcms->id.'" class="label label-primary">Edit Page</a> ';
	?>
	<div class="cms_content cke_editable cke_editable_themed cke_contents_ltr cke_show_borders"><?php echo $rcms->content;?></div>
    <?php
    if ($rcms->id == 5)
    {
        require("lib/fns.php");
        if (isset($_POST['submitted']) && ('true' == $_POST['submitted'])) {
            process_form();

        } else {
            print_form();
        }
    }
    ?>
</div>