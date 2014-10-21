<div id="reviewFormContainer" title="Customer Reviews">
	<?php if (mysql_num_rows($reviewLists) == 0):?>
		<div class="notice">There is no review yet. Be the first one to comment!</div>
	<?php endif; ?>
	<p><a href="javascript:void();" onClick="$('#review_form').show('slow');"><img alt="Write a review" src="/static/images/writing.png" />Write a Review</a></p>
	
	<div id="review_form" style="display:none;">
	<?php
		if (CheckUserReviewed($p->id) == false)
		{
	?>
		<hr style="margin-top:5px;"/>
		<?php /*
		 * <div id="review_guideline">
			<h4>Review Guidelines</h4>
			<p>Here are some of the things that can cause a review to be denied:</p>
			<?php echo GetSetting("review_rules"); ?>
			<p>We may change these policies at any time without notice.</p>
		</div>*/?>
		<form id="fm_review">
		<table id="review_form_table">
			<tbody>
				<tr>
					<td><strong>Review title: *</strong><br>200 chars</td>
					<td><input type="text" name="rv_title" id="rv_title" class="form-control" required /></td>
				</tr>
				<tr>
					<td><strong>Overall rating: *</strong></td>
					<td><div id="star2"></div>
						<input type="hidden" name="rv_rating" class="form-control" id="rv_rating" required /></td>
				</tr>
				<tr>
					<td><strong>Pros:</strong><br>2000 chars</td>
					<td><textarea name="rv_pros" class="form-control" id="rv_pros"></textarea></td>
				</tr>
				<tr>
					<td><strong>Cons:</strong><br>2000 chars</td>
					<td><textarea name="rv_cons" class="form-control" id="rv_cons"></textarea></td>
				</tr>
				<tr>
					<td><strong>Other Thoughts:</strong><br>2000 chars</td>
					<td><textarea name="rv_thoughts" class="form-control" id="rv_thoughts"></textarea></td>
				</tr>
				<tr>
					<td><strong>Conclusion:</strong></td>
					<td><input type="radio" name="rv_conclusion" class="rv_conclusion" id="rv_conclusion_1" value="1"/> yes, I would recommend<br>
						<input type="radio" name="rv_conclusion" class="rv_conclusion" id="rv_conclusion_0" value="0"/> No, I would not recommend</td>
				</tr>
				<tr>
					<td><strong>Your Name:</strong></td>
					<td><input type="text" name="rv_name" class="form-control" id="rv_name" required /></td>
				</tr>
				<tr>
					<td><strong>Your Location:</strong></td>
					<td><input type="text" name="rv_location" class="form-control" id="rv_location"  /></td>
				</tr>
				<tr>
					<td><strong>Your Email*:</strong><br>(We keep this private)</td>
					<td><input type="text" name="rv_email" class="form-control" id="rv_email" required email /></td>
				</tr>
				<tr>
					<td colspan="2">

    <img id="siimage" style="border: 1px solid #000; margin-right: 15px" src="/static/securimage/securimage_show.php?sid=<?php echo md5(uniqid()) ?>" alt="CAPTCHA Image" align="left" />
    &nbsp;
    <a tabindex="-1" style="border-style: none;" href="#" title="Refresh Image" onclick="document.getElementById('siimage').src = '/static/securimage/securimage_show.php?sid=' + Math.random(); this.blur(); return false"><img src="/static/securimage/images/refresh.png" alt="Reload Image" height="32" width="32" onclick="this.blur()" align="bottom" border="0" /></a><br />
    <strong>Enter Code*:</strong><br />
    <input type="text" class="form-control" style="width:100px;" name="ct_captcha" id="ct_captcha" size="12" maxlength="8" />

					</td>
				</tr>
				<tr>
					<td colspan="2"><button class="btn" onClick="return SubmitComment();">Submit Review</button></td>
				</tr>
				
			</tbody>
		</table>
		</form>
		<?php
		} else {
			echo "<div class='success'>You have left review for this product.</div>";
		}
		?>
	</div>
</div>
<script type="text/javascript">
$.fn.raty.defaults.path = '/static/raty/img';
$('#star2').raty({click: function(score, evt) {
	$('#rv_rating').val(score);
}});

function SubmitComment()
{
	PostComment($("#rv_title").val()
		,$("#rv_rating").val()
		,$("#rv_pros").val()
		,$("#rv_cons").val()
		,$("#rv_thoughts").val()
		,$("#rv_name").val()
		,$("#rv_email").val()
		,$(".rv_conclusion:checked").val()
		,$("#rv_location").val()
		,$("#ct_captcha").val());
	return false;
}

function PostComment(title,rating,pros,cons,thoughts,name,email,conclusion,location,captcha)
{
	if (rating != 1 
		&& rating != 2
		&& rating != 3
		&& rating != 4
		&& rating != 5)
	{
		alert("Please leave your \"Overall rating\"");
		return;
	}
	if (title == "" 
		|| email == "")
	{
		alert("Please enter required fields");
		return;
	}
	$.post("/rating/index.html",{"pid":<?php echo $p->id;?>,"score":rating,"title":title,"pros":pros,"cons":cons,"thoughts":thoughts,"name":name,"email":email,"conclusion":conclusion,"location":location,"captcha":captcha},function(data){
		    	if (data.error_code == 1)
		    	{
		    		$("#ct_captcha").val("");
		    		document.getElementById('siimage').src = '/static/securimage/securimage_show.php?sid=' + Math.random();
		    		alert(data.error);
		    	}
		    	else
		    	{
			    	$("#review_form").html("<span class='review_form_thankyou'>"+data.message+"</span>");
			    	//$("#ratingValue").html(data.ratingValue);
			    	//$("#reviewCount").html(data.reviewCount);
			    	$('html, body').animate({
				        scrollTop: $("#review_form").offset().top
				    }, 500);
			    }
		    },"json");
}
SetValidationForm("#fm_review","right");
</script>

<div class="review_list">
	<table class="table">
	<?php
	while ($r = mysql_fetch_object($reviewLists))
	{
		$name = $r->name;
		$title = $r->title;
		$date = $r->created_date;
		$rate = $r->rate;
		$content = "";
		if ($r->pros != '')
			$content .= '<strong>Pros:</strong><p>'.$r->pros.'</p>';
		if ($r->cons != '')
			$content .= '<strong>Pros:</strong><p>'.$r->cons.'</p>';
		if ($r->comment != '')
			$content .= '<strong>Pros:</strong><p>'.$r->comment.'</p>';
		
		$stars = GetStars($rate);
		
		if ($name == '' )
			$name = "N/A";
		$date = date('m/d/Y h:i:s',strtotime($date));
		echo '<tr>
			<td width="20%">
				<strong>'.$name.'</strong>
				<p>'.$date.'</p>
			</td>
			<td>
				<span>'.$stars.'</span> '.$title.'
				<hr class="hrreview" />
				<div>'.$content.'</div>
			</td>
		</tr>';
	}
	?>
	</table>
</div>
