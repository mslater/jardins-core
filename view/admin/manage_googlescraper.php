<link rel="stylesheet" href="/static/tablesorter/theme.blue.css">
<script src="/static/tablesorter/jquery.tablesorter.min.js" ></script>

<div id="floating_menu" style="width:900px;background:white;"></div>
<?php $xajax->
printJavascript(); ?>

<script>
  function Beginxajax_SearchAll()
  {
	$("#smSearchAll").hide();
  $("#smSearchAllwait").show();
  xajax_SearchAll();
}
</script>
<div id="action_block" class="action_block">
  <div class="input-prepend">
    <span class="add-on">
      Keyword
      </span>
      <input type="text" style="width:300px;" id="keyword" class="input-medium">
      
      </div>
      <div class="input-prepend">
        <span class="add-on">
          Category
        </span>
        <input type="text" style="width:300px;" id="category" class="input-medium">
        
      </div>
      <input id="smAddKeyword" onClick="xajax_AddKeyword($('#keyword').val(),$('#category').val())" style="margin-top:-12px;" type="button" class="btn" value="Add" />
      <br>
      <br>
      <input id="smSearchAll" onClick="Beginxajax_SearchAll()" style="margin-top:-12px;" type="button" class="btn" value="Running 200 Oldest Updated Queries" />
      <span id="smSearchAllwait" style="display:none;">
        <img src="/static/images/lightbox-ico-loading.gif" />
      </span>
</div>

<table class="tablesorter">
  <thead>
    <tr>
      <th class="sorter-title">
        Keyword
      </th>
      <th>
        Category
      </th>
      <th>
        Last Scraped
      </th>
      <th>
        # of products
      </th>
      <th>
        Actions
      </th>
    </tr>
  </thead>
  <tbody id="keyword_list">
	<?php
	while ($row = mysql_fetch_object($keywords))
	{
		echo '<tr id="kw_row_'.$row->keyword_id.'">
		  <td><input type="text" id="kw_'.$row->keyword_id.'" value="'.$row->keyword.'" /></td>
		  <td>'.$row->category.'</td>
		  <td class="scd">'.date("m/d/Y h:i:s",$row->last_update).'</td>
		  <td class="scc">'.$row->nbproducts.'</td>
		  <td><input type="button" class="btn" onClick="xajax_UpdateKeyword('.$row->keyword_id.',$(\'#kw_'.$row->keyword_id.'\').val())" value="Update" />
		  <input type="button" class="btn" onClick="xajax_DeleteKeyword('.$row->keyword_id.')" value="Delete" />
		  <a href="/admin.php?view=google_products&kid='.urlencode($row->keyword_id).'" target="_blank" class="btn">View</a></td>
		</tr>';
	}
	?>
    </tbody>
</table>

<div id="scraped_result_box" style="display:none;" title="Scraper Results">
  <div id="scraped_result_box_content">
  </div>
</div>


<script>
function fixDiv() {
  var $cache = $('#floating_menu'); 
  if ($(window).scrollTop() > 100) 
    $cache.css({'position': 'fixed', 'top': '10px'}); 
  else
    $cache.css({'position': 'relative', 'top': 'auto'});
}

$.tablesorter.addParser({
	  // set a unique id
	  id: 'title',
	  is: function(s, table, cell) {
	  	
	    return false;
	  },
	  format: function(s, table, cell, cellIndex) {
	    var reg = /value=\"([^\"]*)\"/
	    return cell.innerHTML.match(reg)[1];
	  }
	});

$(function() {
	$("#floating_menu").append("<ul class='nav nav-tabs'>"+$('.nav-tabs').html()+"<ul>");
	$("#floating_menu").append("<div class='navbar'>"+$('.navbar').html()+"</div>");
	$("#floating_menu").append("<div class='action_block'>"+$('#action_block').html()+"</div>");
	$('.action_block:eq(1)').remove();
	$('.nav-tabs:eq(0)').remove();
	$('.navbar:eq(0)').remove();
	
	$(window).scroll(fixDiv);
	fixDiv();

	
	
	$(".tablesorter").tablesorter({
    theme : 'blue'
  });
});

</script>