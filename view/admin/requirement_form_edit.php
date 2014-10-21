<script src="<?php echo BASE_URL. 'static/ckeditor/ckeditor.js';?>"></script>

<h3>Requirement Form</h3>
<form id="requirement_form" method="post">
	<div class="span-10">
		<label>Name</label>
		<input type="text" required class="input-xlarge" name="name" value="<?php echo stripcslashes($form->name);?>" />
		<label>Related Product Category</label>
		<select name="related_category" id="related_category">
			<option value="">Related Product Category</option>
			<?php
			while ($r = mysql_fetch_object($data_category))
			{
				echo '<option value="'.$r->id.'">'.$r->category.'</option>';
			}
			?>
		</select>
		<label>Hardiness</label>
                <select id="hardinessMin" name="hardinessMin" style="width:60px">
                <?php
		for($i=2;$i <= 11;$i++)
		{
		  echo '<option value="'.$i.'">'.$i.'</option>';
		}
		?>
		</select>
                - 
                <select id="hardinessMax" name="hardinessMax" style="width:60px">
                <?php
		for($i=2;$i <= 11;$i++)
		{
		  echo '<option value="'.$i.'">'.$i.'</option>';
		}
		?>
		</select>

                <?php
                  /*<input type="text" class="input-xlarge" name="hardiness" value="<?php echo stripcslashes($form->hardiness);?>"  /> */
                ?>

		<?php
		for($i=1;$i <= 4;$i++)
		{
			echo '<label>Sun '.$i.'</label><select id="sun'.$i.'" name="sun'.$i.'">
				<option value="">None</option>
				<option value="Full Sun">Full Sun</option>
				<option value="Full Shade">Full Shade</option>
				<option value="Partial Sun">Partial Sun</option>
			</select>';
		}
		?>

		<label>Period Of Interest</label>
                             

               <span>From:</span><br/>
               <select id="periodofinterestMin" name="periodofinterestMin">
		  <option value=""></option>
		  <option value="Early Spring">Early Spring</option>
		  <option value="Mid Spring">Mid Spring</option>
		  <option value="Late Spring">Late Spring</option>
		  <option value="Early Summer">Early Summer</option>
		  <option value="Mid Summer">Mid Summer</option>
		  <option value="Late Summer">Late Summer</option>
		  <option value="Early Fall">Early Fall</option>
		  <option value="Fall">Fall</option>
		  <option value="Frost">Frost</option>
		</select>

               <br/>
               <span>To:</span><br/>

                <select id="periodofinterestMax" name="periodofinterestMax">
		  <option value=""></option>
		  <option value="Early Spring">Early Spring</option>
		  <option value="Mid Spring">Mid Spring</option>
		  <option value="Late Spring">Late Spring</option>
		  <option value="Early Summer">Early Summer</option>
		  <option value="Mid Summer">Mid Summer</option>
		  <option value="Late Summer">Late Summer</option>
		  <option value="Early Fall">Early Fall</option>
		  <option value="Fall">Fall</option>
		  <option value="Frost">Frost</option>
		</select>


		<input type="text" class="input-xlarge" name="periodofinterest" id="periodofinterest" value="<?php echo stripcslashes($form->periodofinterest);?>" />

		<label>Difficulty</label>
		<input type="text" class="input-xlarge" name="difficulty" value="<?php echo stripcslashes($form->difficulty);?>" />
		
	</div>
	<div class="span-10">
		<label>Watering</label>
		<input type="text" class="input-xlarge" name="water" value="<?php echo stripcslashes($form->water);?>" />
		<label>Maintaining</label>
		<input type="text" class="input-xlarge" name="maintain" value="<?php echo stripcslashes($form->maintain);?>" />
		<label>Plant Type 1</label>
		<input type="text" class="input-xlarge" name="plant_type1" value="<?php echo stripcslashes($form->plant_type1);?>" />
		<label>Plant Type 2</label>
		<input type="text" class="input-xlarge" name="plant_type2" value="<?php echo stripcslashes($form->plant_type2);?>" />
		<!--label>Plant Type 3</label>
		<input type="text" class="input-xlarge" name="plant_type3" value="<?php echo stripcslashes($form->plant_type3);?>" /-->
		<label>Height</label>
		<input type="text" class="input-xlarge" name="height" value="<?php echo stripcslashes($form->height);?>" />
		<label>Spacing</label>
		<input type="text" class="input-xlarge" name="spacing" value="<?php echo stripcslashes($form->spacing);?>" />
		<label>Depth</label>
		<input type="text" class="input-xlarge" name="depth" value="<?php echo stripcslashes($form->depth);?>" />
		<label>Feature</label>
		<input type="text" class="input-xlarge" name="feature" value="<?php echo stripcslashes($form->feature);?>" />
		<label>Use</label>
		<input type="text" class="input-xlarge" name="use" value="<?php echo stripcslashes($form->use);?>" />
		<label>Soil Type</label>
		<input type="text" class="input-xlarge" name="soil_type" value="<?php echo stripcslashes($form->soil_type);?>" />
		<label>Soil PH</label>
		<input type="text" class="input-xlarge" name="soil_ph" value="<?php echo stripcslashes($form->soil_ph);?>" />
		<label>Soil Drainage</label>
		<input type="text" class="input-xlarge" name="soil_drainage" value="<?php echo stripcslashes($form->soil_drainage);?>" />
	</div>
	<div class="span-24">
		<input type="submit" class="btn" name="smUpdateReq" value="Submit"/>
		<?php if (isset($r)):?>
		<a class="btn" href="/admin.php?view=forms_edit&copyid=<?php echo $form->id;?>&type=req">Copy this form</a>
		<?php endif; ?>
	</div>
</form>
<script>
<?php if(isset($form)):?>
$("#sun1").val('<?php echo $form->sun1;?>');
$("#sun2").val('<?php echo $form->sun2;?>');
$("#sun3").val('<?php echo $form->sun3;?>');
$("#sun4").val('<?php echo $form->sun4;?>');

$("#related_category").val('<?php echo $form->related_category;?>');

$("#hardinessMin").val('<?php echo explode(" - ",$form->hardiness)[0] ?>');
$("#hardinessMax").val('<?php echo explode(" - ",$form->hardiness)[1] ?>');



$("#periodofinterestMin").change(function(){
   var p1 = $("#periodofinterestMin").val();
   var p2 = $("#periodofinterestMax").val();
   
  if(p1 == p2)
     {
      $("#periodofinterest").val(p1);
     }
    else
    { 
      if(p1 != "" && p2 != "")
       {
        $("#periodofinterest").val(p1+" - "+p2);  
       }
      else if(p1 == "")
      {
        $("#periodofinterest").val(p2);  
      }
      else if(p2 =="")
      {
        $("#periodofinterest").val(p1);  
      }
      else
       {
                $("#periodofinterest").val("");  

      }
     }

});


$("#periodofinterestMax").change(function(){
   var p1 = $("#periodofinterestMin").val();
   var p2 = $("#periodofinterestMax").val();
   
  
 if(p1 == p2)
     {
      $("#periodofinterest").val(p1);
     }
    else
    { 
      if(p1 != "" && p2 != "")
       {
        $("#periodofinterest").val(p1+" - "+p2);  
       }
      else if(p1 == "")
      {
        $("#periodofinterest").val(p2);  
      }
      else if(p2 =="")
      {
        $("#periodofinterest").val(p1);  
      }
      else
       {
                $("#periodofinterest").val("");  

      }
     }
});

var p = $("#periodofinterest").val();


var pi =  p.split(" - ");

if(pi.length == 2)
{ 
    $("#periodofinterestMin").val(pi[0]);
    $("#periodofinterestMax").val(pi[1]);
}
else
{
    $("#periodofinterestMin").val(pi[0]);
    $("#periodofinterestMax").val(pi[0]);
}




<?php endif;?>
    
SetValidationForm("#requirement_form","top");

</script>




