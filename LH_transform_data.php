<?php

// NOTES ON PROBLEMS WITH SPECIFIC DATA RECORDS:
//
//  Weird symbol in prod descrip of products 2631, 2636, 2637, for example:
//			Featured at Â <a title="Isola Bella" 

//  12/14 - 1/15
//  Linton Hale for Webvanta
//  A set of functions to translate data from the jardins site  ( jardins-sans-secret.com )
//  Exports data to meet the needs of webvanta 
//
//  https://github.com/mslater/jardins-core )


// Connect to database
$conn=mysqli_connect("localhost","root","Muser123","jardinss_dev4");
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

// *****************  Set up global variables and data tables  ***************************************
$show_results_in_browser = 1;  // Set to 1 to display HTML table of summary results

// -------------------------------------------------------
// NOTES:
// 		category_type	Webvanta	category_select value
//   	-------------	--------	---------------------
// bs	Basics			Articles	'bs'
// ds	Designers		Articles	'ds'
// pr	Promenades		Articles	'pr'
// gi	Garden-Ideas	Plans		'gi'
// pl	Plants			Plants		'pl'

// Articles
//$starting_product_id = 4701;  // 4700;  // 2927;  // lowest product id = 2594
//$ending_product_id   = 4794;  // 4716;  // 2928;  // highest product id = 4793
//$output_data_table = 'LH_results_ARTICLES_bs';  	// BS
//$category_select = "category_type='bs'";  		// BS
//$output_data_table = 'LH_results_ARTICLES_ds';  	// DS
//$category_select = "category_type='ds'";  		// DS
//$output_data_table = 'LH_results_ARTICLES_pr';  	// PR
//$category_select = "category_type='pr'";  		// PR

// Garden ideas / Plans
$starting_product_id = 2594;  // 2919;  // lowest product id = 2594
$ending_product_id   = 4793;  // 2933;  // highest product id = 4793
$output_data_table = 'LH_results_PLANS';
$category_select = "category_type='gi'";		// select Plans categories

// Plants
//$starting_product_id = 4701;  // 4700;  // 2927;  // lowest product id = 2594
//$ending_product_id   = 4794;  // 4716;  // 2928;  // highest product id = 4793
//$output_data_table = 'LH_results_PLANTS_0_3400';  // or...
//$output_data_table = 'LH_results_PLANTS_3401_3900';  // or...
//$output_data_table = 'LH_results_PLANTS_3901_4400';  // or...
//$output_data_table = 'LH_results_PLANTS_4401_end';  // or...
//$category_select = "category_type='pl'";  	// select Plants categories

// -------------------------------------------------------
// Patterns to search and replace to clean up name and description fields
$patterns = array();
$patterns[0] = '/&nbsp;/';   					// non-breaking space
$patterns[1] = '/\'/';       					// single quote
$patterns[2] = '/\& /';							// Ampersand followed by space
$patterns[3] = '/href="http:\/\/www.jardins-sans-secret.com/';			// replace with href="
$patterns[4] = '/href="http:\/\/jardins-sans-secret.com/';				// replace with href="
$patterns[5] = '/href="https:\/\/www.jardins-sans-secret.com/';			// replace with href="
$patterns[6] = '/href="https:\/\/jardins-sans-secret.com/';				// replace with href="
$patterns[7] = '/src="http:\/\/www.jardins-sans-secret.com\/wp-content\/uploads/';		// replace with src="/jardin/images
$patterns[8] = '/\/lib\/kcfinder\/upload\/images/';					// replace with /jardin/images
$patterns[9] = '/\<p\> /';						// replace with <p>
$patterns[10] = '/\<p\>&nbsp;/';					// replace with <p>
$patterns[11] = '/\<p\>\s*?\<\/p\>/';				// remove: <p> </p> (empty paragraph tags)
$patterns[12] = '/\<p\>\s*?\<br\s?\/\>/';			// remove: <p> <br />
$patterns[13] = '/ align=".*?"/';				// remove ANY align attributes
$patterns[14] = '/ align=&quot;.*?&quot;/';
$patterns[15] = '/ width=".*?"/';				// remove ANY width attributes
$patterns[16] = '/ width=&quot;.*?&quot;/';
$patterns[17] = '/ border=".*?"/';				// remove ANY border attributes
$patterns[18] = '/ cellpadding=".*?"/';			// remove ANY cellpadding attributes
$patterns[19] = '/ cellspacing=".*?"/';			// remove ANY cellspacing attributes
$patterns[20] = '/ style=".*?"/';				// remove ANY style attributes
$patterns[21] = '/ style=&quot;.*?&quot;/';		// remove ANY style attributes
$patterns[22] = '/ class=".*?"/';				// remove ANY class attributes
$patterns[23] = '/\<strong\>|\<\/strong\>/';	// remove bold
$patterns[24] = '/\<em\>|\<\/em\>/';			// remove italics
$patterns[25] = '/\<span\>|\<\/span\>/';		// remove clean up empty span tags
$patterns[26] = '/ rel=".*?"/';					// remove
$patterns[27] = '/&Atilde;/';					// remove
$patterns[28] = '/&sbquo;/';					// remove
$patterns[29] = '/&Acirc;/';					// remove
$patterns[30] = '/\[caption id=.*?\].*?\[\/caption\]/';		// remove ANY caption tag
$patterns[31] = '/\[\/?toggle.*?\]/';			// remove ANY [toggle] or [toggles] tag
$patterns[32] = '/\[\/?get_data_from_google.*?\]/';				// remove ANY [get_data_from_google] tag
$patterns[33] = '/\[table id=.*?\/\]/';			// remove ANY [table id=#/] tag
$patterns[34] = '/\<div\>\<dl id=".*?"\>/';		// remove ANY <div><dl id=""> tag
$patterns[35] = '/\<\/dd\>\<\/dl\>\<\/div\>/';	// remove </dd></dl></div> tag
$patterns[36] = '/\<\/?dd>/';					// remove beginning or ending <dd> tag
$patterns[37] = '/\<\/?dl>/';					// remove beginning or ending <dl> tag
$patterns[38] = '/\<\/?dt>/';					// remove beginning or ending <dt> tag
$patterns[39] = '/\<h2\>\s?\<\/h2\>/';			// remove empty H2 tag
$patterns[40] = '/\<font\> color=".*?"/';		// remove font tags with color attribute
//$patterns[41] = '/Â \]/';				// remove 
//$patterns[42] = '/Â/';				// remove 

// and corresponding replacements for matches found
$replacements = array();
$replacements[0] = ' ';      					// replace non-breaking space codes with space
$replacements[1] = '&#39;';  					// replace single quotes with HTML code for single quote
$replacements[2] = '&amp; ';  					// replace ampersand with HTML code for ampersand, followed by space
$replacements[3] = 'href="';					// replace with href="
$replacements[4] = 'href="';					// replace with href="
$replacements[5] = 'href="';					// replace with href="
$replacements[6] = 'href="';					// replace with href="
$replacements[7] = 'src="/jardin/images';		// replace with src="/jardin/images
$replacements[8] = '/jardin/images';			// replace with /jardin/images
$replacements[9] = '<p>';						// replace with <p>
$replacements[10] = '<p>';						// replace with <p>
$replacements[11] = '';							// for the rest, replace with empty string
$replacements[12] = '';
$replacements[13] = '';       					
$replacements[14] = '';
$replacements[15] = '';
$replacements[16] = '';
$replacements[17] = '';
$replacements[18] = '';
$replacements[19] = '';
$replacements[20] = '';
$replacements[21] = '';
$replacements[22] = '';
$replacements[23] = '';
$replacements[24] = '';
$replacements[25] = '';
$replacements[26] = '';
$replacements[27] = '';
$replacements[28] = '';
$replacements[29] = '';
$replacements[30] = '';
$replacements[31] = '';
$replacements[32] = '';
$replacements[33] = '';
$replacements[34] = '';
$replacements[35] = '';
$replacements[36] = '';
$replacements[37] = '';
$replacements[38] = '';
$replacements[39] = '';
$replacements[40] = '';
//$replacements[41] = '';
//$replacements[42] = '';


// -------------------------------------------------------
// Delete and recreate LH_column_lookup table
$sql = 'DROP TABLE IF EXISTS LH_column_lookup';
if (mysqli_query($conn, $sql)) {
//    echo "Dropped table <em>LH_column_lookup</em><br>";
} else {
    echo "Error dropping table: " . mysqli_error($conn) . "<br>";
}
$sql = "CREATE TABLE IF NOT EXISTS LH_column_lookup (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
column_name varchar(255),
temp_taxonomy_path varchar(1020)
)";
if (mysqli_query($conn, $sql)) {
//    echo "Created table: <em>LH_column_lookup</em><br>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
// Add column names and place holders for temp_taxonomy_path variables, used to concatenate multiple rows of taxonomy path values
// DON'T include planting ideas (id<>1035)  		
// Add "Planting Ideas" children (parent_id=1035)
$query = "SELECT * FROM product_category WHERE (" . $category_select . "AND (parent_id=0 OR parent_id=1035) AND id<>1035 ) ORDER BY category_name";
$result = mysqli_query($conn,$query);
while($row    = mysqli_fetch_assoc($result))
  {
  $column_name = str_replace(" ", "_", $row['category_name']);
  $sql = "INSERT INTO LH_column_lookup (id, column_name, temp_taxonomy_path) VALUES (NULL, '" . $column_name . "', '')";
  if (mysqli_query($conn, $sql)) {
//      echo "<working...>";
  } else {
      echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
  }
  }

// -------------------------------------------------------
// Delete and recreate LH_taxonomy_paths table
$sql = 'DROP TABLE IF EXISTS LH_taxonomy_paths';
if (mysqli_query($conn, $sql)) {
//    echo "Dropped table <em>LH_taxonomy_paths</em><br>";
} else {
    echo "Error dropping table: " . mysqli_error($conn) . "<br>";
}
$sql = "CREATE TABLE IF NOT EXISTS LH_taxonomy_paths (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
category_id INT(11),
parent_id INT(11),
category_name varchar(255),
root_category_name varchar(255),
taxonomy_path varchar(1020)
)";
if (mysqli_query($conn, $sql)) {
//    echo "Created table: <em>LH_taxonomy_paths</em><br>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

// -------------------------------------------------------
// Delete and recreate LH_related_images
$sql = 'DROP TABLE IF EXISTS LH_related_images' ;
if (mysqli_query($conn, $sql)) {
//    echo "Dropped table <em>LH_related_images</em><br>";
} else {
    echo "Error dropping table: " . mysqli_error($conn) . "<br>";
}
$sql = "CREATE TABLE IF NOT EXISTS LH_related_images (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
product_id INT(11),
image2 varchar(255),
image3 varchar(255),
image4 varchar(255),
image5 varchar(255),
image6 varchar(255),
image7 varchar(255),
image8 varchar(255),
image9 varchar(255),
image10 varchar(255),
image2_alt TEXT,
image3_alt TEXT,
image4_alt TEXT,
image5_alt TEXT,
image6_alt TEXT,
image7_alt TEXT,
image8_alt TEXT,
image9_alt TEXT,
image10_alt TEXT
)";
if (mysqli_query($conn, $sql)) {
//    echo "Created table: <em>LH_related_images</em><br>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

// *****************  Generate Lookup Table with related images for each product***************************************
//
// Add all product numbers and related images to LH_related_images
	$query = "SELECT product_id, name, caption FROM product_images 
			WHERE (product_id>='" . $starting_product_id . "' AND product_id <='" . $ending_product_id . "' )
			ORDER BY product_id";

// AND product_id<>2859 AND product_id<>2861 AND product_id<>2862 AND product_id<>2881 AND	product_id<>2882 AND product_id<>2898

	$result = mysqli_query($conn,$query);

	$prev_product_id = 0;
	while($row    = mysqli_fetch_assoc($result))
	  {
	      $curr_product_id = $row['product_id'];
		  $cleaned_caption = preg_replace($patterns, $replacements, $row['caption']);

	      if ( $prev_product_id !== $curr_product_id ) {
			  $sql = "INSERT INTO LH_related_images
			        (id, product_id, image2, image2_alt)
			        VALUES
			        (NULL, '" . $row['product_id'] . "','" . $row['name'] . "','" . $cleaned_caption ."')";
			  $this_cnt = 2;

			  if (mysqli_query($conn, $sql)) {
			  //      echo "";
			  } else {
			      echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
			  }
			  $prev_product_id = $row['product_id'];
		  } else {
		      $this_cnt = $this_cnt + 1;
			  if ( $this_cnt < 11 ) {
			     $sql = "UPDATE LH_related_images SET image" . $this_cnt . "='" . $row['name'] . "', image" . $this_cnt . "_alt='" . $cleaned_caption . "' WHERE product_id ='" . $row['product_id'] . "'"; 
			     if (mysqli_query($conn, $sql)) {
			         //echo "Working...";
			     } else {
			         echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
			     }
			  }
	  	  }
	  }
//    echo "Completed generating <em>LH_related_images</em> lookup table.<br>";


// -------------------------------------------------------
// Delete and recreate LH_products_stage1 table
$sql = 'DROP TABLE IF EXISTS LH_products_stage1';
if (mysqli_query($conn, $sql)) {
//    echo "Dropped table <em>LH_products_stage1</em><br>";
} else {
    echo "Error dropping table: " . mysqli_error($conn) . "<br>";
}
// Create new LH_products_stage1 table with initial fields only
$sql = "CREATE TABLE IF NOT EXISTS LH_products_stage1 (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(255),
product_id INT(11),
category_id INT(11),
parent_id INT(11),
category_name varchar(255),
category_type varchar(50)
)";
if (mysqli_query($conn, $sql)) {
//    echo "Created table: <em>LH_products_stage1</em><br>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

// -------------------------------------------------------
// Delete and recreate LH_products_stage2 table
$sql = 'DROP TABLE IF EXISTS LH_products_stage2';
if (mysqli_query($conn, $sql)) {
//    echo "Dropped table <em>LH_products_stage2</em><br>";
} else {
    echo "Error dropping table: " . mysqli_error($conn) . "<br>";
}
// Create new LH_products_stage2 table with initial fields only
$sql = "CREATE TABLE IF NOT EXISTS LH_products_stage2 (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(255),
product_id INT(11),
category_id INT(11),
parent_id INT(11),
category_name varchar(255),
category_type varchar(50)
)";
if (mysqli_query($conn, $sql)) {
//    echo "Created table: <em>LH_products_stage2</em><br>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

// -------------------------------------------------------
// Delete and recreate output_data_table
$sql = 'DROP TABLE IF EXISTS ' . $output_data_table ;
if (mysqli_query($conn, $sql)) {
//    echo "Dropped table <em>" . $output_data_table . "</em><br>";
} else {
    echo "Error dropping table: " . mysqli_error($conn) . "<br>";
}
$sql = "CREATE TABLE IF NOT EXISTS " . $output_data_table . " (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
product_id INT(11),
name VARCHAR(255),
halfcleaned_description TEXT,
description TEXT,
created_date timestamp,
draft varchar(50),
main_picture varchar(255),
calculator_name TEXT,
calculator_size TEXT,
requirement_id int(11),
seo_keyword TEXT,
seo_description TEXT,
layout_type varchar(50),
seo_title varchar(255),
care_id int(11),
contact_form_id int(11),
map_address varchar(255),
view_count bigint(20),
wider_content tinyint(4),
product_tabs tinyint(4),
isfeatured tinyint(4),
reward_icon varchar(255),
common_name TEXT,
intro TEXT,
image_alt TEXT,
publishing_date bigint(20),
reviewer int(11),
rating_count int(11),
rating double,
last_modified timestamp,
slug varchar(255),
image2 varchar(255),
image3 varchar(255),
image4 varchar(255),
image5 varchar(255),
image6 varchar(255),
image7 varchar(255),
image8 varchar(255),
image9 varchar(255),
image10 varchar(255),
image2_alt TEXT,
image3_alt TEXT,
image4_alt TEXT,
image5_alt TEXT,
image6_alt TEXT,
image7_alt TEXT,
image8_alt TEXT,
image9_alt TEXT,
image10_alt TEXT
)";
if (mysqli_query($conn, $sql)) {
//    echo "Created table: <em>" . $output_data_table . "</em><br>";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
// Add columns 
$query = "SELECT * FROM LH_column_lookup";
$result = mysqli_query($conn,$query);
//echo "<table border='1' cellpadding='2' cellspacing='0'><tr><th>Column name</th></tr>";
while($row    = mysqli_fetch_assoc($result))
  {
  // use top-level category names as column names for new output_data_table
  $sql = "ALTER TABLE " . $output_data_table . " ADD (" . $row['column_name'] . " varchar(1020))";
  if (mysqli_query($conn, $sql)) {
//      echo "<tr><td>" . $column_name . "</td></tr>";
  } else {
      echo "<tr><td>Error adding column: " . mysqli_error($conn) . "</td></tr>";
  }
  }
//echo "</table>";
//echo "<hr>";


// *****************  Display header image, demonstrating transformation concept...  ***************************************
//
echo "<img src='jardin-to-webvanta.png' height=92 width=314>";
echo "<hr>";


// *****************  Show checkboxes and submit button  *****************************************
$webvanta_data_type = $_POST["webvanta_data_type"];

//if (!isset($_POST['submit'])) { // if page is not submitted to itself echo the form
//	echo "<html>";
//	echo "<body>";
//	echo "<form method='post' action='".$PHP_SELF."'>";
//	echo "<input type='checkbox' value='Articles' name='webvanta_data_type[]'> Articles<br />";
//	echo "<input type='checkbox' value='Plans' name='webvanta_data_type[]'> Plans<br />";
//	echo "<input type='checkbox' value='Plants' name='webvanta_data_type[]'> Plants<br />";
//	echo "<input type='submit' value='submit' name='submit'>";
//	echo "</form>";
//} else {

    foreach ($webvanta_data_type as $f) {
      echo $f."<br />";
    }

	// For stage 1 make LH_products_stage1 table, with only rows of the selected category type(s) and with cleaned up data.
	
	
	// INSERT ERRORS OCCURRING ON SIX PRODUCTS WHERE category_type='ds', SO EXCLUDING FROM OUTPUT FOR NOW...
	$query = "SELECT p.id, p.name, pic.product_id, pic.category_id, pc.parent_id, pc.category_name, pc.category_type FROM product p 
			JOIN product_in_category pic ON p.id=pic.product_id 
			JOIN product_category pc ON pic.category_id=pc.id
			WHERE (p.id>='" . $starting_product_id . "' AND p.id <'" . $ending_product_id . "' AND " . $category_select . " AND p.status=1)
			ORDER BY p.id, pc.category_name";    
// TESTING ***** ONLY USE VISIBLE PRODUCTS, WHERE STATUS = 1
// **************


//  AND p.id<>2859 AND p.id<>2861 AND p.id<>2862 AND p.id<>2881 AND	p.id<>2882 AND p.id<>2898 

	$result = mysqli_query($conn,$query);
	
	// Add data to LH_products_stage1 table
	while($row    = mysqli_fetch_assoc($result))
	  {
//	  $cleaned_name = preg_replace($patterns, $replacements, $row['name']);

	  $sql = "INSERT INTO LH_products_stage1
	        (id, product_id, category_id, parent_id, category_name, category_type)
	        VALUES
	        (NULL, '" . $row['product_id'] . "', '" . $row['category_id'] . "', '" . $row['parent_id'] . "', '" . $row['category_name'] . "', '" . $row['category_type'] . "')";
//	  $sql = "INSERT INTO LH_products_stage1
//	        (id, name, product_id, category_id, parent_id, category_name, category_type)
//	        VALUES
//	        (NULL, '" . $cleaned_name . "', '" . $row['product_id'] . "', '" . $row['category_id'] . "', '" . $row['parent_id'] . "', '" . $row['category_name'] . "', '" . $row['category_type'] . "')";
	        
	                
	  if (mysqli_query($conn, $sql)) {
//		    echo "";
	  } else {
	      echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	  }
	  }
    echo "Completed table <em>LH_products_stage1</em><br>";


	// *****************  Generate Lookup Table with Root Categories and Taxonomy Paths for each Category  ***************************************
	//
	// Get all products in this selected category, then traverse from leaf to root, building up the taxonomy path Webvanta likes so much
	$query = "SELECT * FROM product_category WHERE (" . $category_select . ") ORDER BY id";
	$result = mysqli_query($conn,$query);
	while($row    = mysqli_fetch_assoc($result))
	  {
	  $root_category_name = $row['category_name'];

	  // If at root of hierarchy,set path to empty 
	  if ( $row['parent_id'] == 0 ) {
	      $taxonomy_path = '';
	  // Else find the parent, and parent's parent, and on until root of hierarchy, accumulating
	  } else {
	      $this_parent_id = $row['parent_id'];

		  // Add parent, except in special case when parent = "Planting Ideas",
		  // then don't include, set path = EMPTY to clear value of previous product
		  if ( $this_parent_id != 1035 ) {
		  	  $taxonomy_path = preg_replace($patterns, $replacements, $row['category_name']);
		  } else {
		  	  $taxonomy_path = '';
		  }

	      while ( $this_parent_id > 0 AND $this_parent_id != 1035 )
	        {
			// Get name and id of parent of current parent
	        $parent_results = mysqli_query($conn,"SELECT category_name, parent_id FROM product_category WHERE id = '" . $this_parent_id . "'");
	        $parent_array = mysqli_fetch_assoc($parent_results);
	        $parent_category_name = preg_replace($patterns, $replacements, array_values($parent_array)[0]);
	        $parent_id_of_parent = array_values($parent_array)[1];

	        if ( $parent_id_of_parent > 0 ) {
				// Special case: when parent = Planting Ideas 
				if ( $parent_id_of_parent == 1035 ) {
					// Don't add parent_category name 
//		            $taxonomy_path = $parent_category_name . ">" . $taxonomy_path;

					// Set root category to parent name, in order to add info to 2nd level term
					$root_category_name = $parent_category_name;
		        } else {
		            $taxonomy_path = $parent_category_name . ">" . $taxonomy_path;
				}
	        } else {
	          $root_category_name = $parent_category_name; 
	        }
	        $this_parent_id = $parent_id_of_parent;
	        }
	  }

	  $sql = "INSERT INTO LH_taxonomy_paths
	        (id, category_id, parent_id, category_name, root_category_name, taxonomy_path)
	        VALUES
	        (NULL, '" . $row['id'] . "', '" . $row['parent_id'] . "', '" . $row['category_name'] . "', '" . $root_category_name . "', '" . $taxonomy_path . "')";
	  if (mysqli_query($conn, $sql)) {
	//      echo "";
	  } else {
	      echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	  }
	  }
    echo "Completed generating <em>LH_taxonomy_paths</em> lookup table.<br>";

	
	// *****************  Add columns to LH_categories_table, then add data  ***************************************
	//
	// Add columns to output table, one for each taxonomy term (category_name) in selection
	// Select top-level rows of category_type plant, to be used as taxonomy terms in Webvanta

	$query = "SELECT * FROM LH_column_lookup";
	$result = mysqli_query($conn,$query);
	while($row    = mysqli_fetch_assoc($result))
	  {
	  // use top-level category names as column names for new LH_products_stage2 table
	  $sql = "ALTER TABLE LH_products_stage2 ADD (" . $row['column_name'] . " varchar(1020))";
	  if (mysqli_query($conn, $sql)) {
	//      echo $column_name . "<br>";
	  } else {
	      echo "<tr><td>Error adding column: " . mysqli_error($conn) . "</td></tr>";
	  }
	  }

	// Get cleaned data from stage 1 table	
	$query = "SELECT * FROM LH_products_stage1 ORDER BY id, category_name";
	$result = mysqli_query($conn,$query);
	
	// Add root category and taxonomy path data to LH_products_stage2 table
	while($row    = mysqli_fetch_assoc($result))
	  {
	
	  $taxonomy_path_results = mysqli_query($conn,"SELECT root_category_name, taxonomy_path FROM LH_taxonomy_paths WHERE category_id = '" . $row['category_id'] . "'");
	  $taxonomy_path_array = mysqli_fetch_assoc($taxonomy_path_results);
	  $this_root_category_name = str_replace(" ", "_", array_values($taxonomy_path_array)[0]);
	  $taxonomy_path = array_values($taxonomy_path_array)[1];
	
	  // If top-level category, then only set basic column values 
	  if ( $row['parent_id'] == 0 ) {
	    $sql = "INSERT INTO LH_products_stage2
	          (id, product_id, category_id, parent_id, category_name, category_type)
	          VALUES
	   	      (NULL, '" . $row['product_id'] . "', '" . $row['category_id'] . "', '" . $row['parent_id'] . "', '" .$row['category_name'] . "', '" . $row['category_type'] . "')";

	  // If there is a taxonomy path, add it to particular column with the same name as the root category, the top-level taxonomy term
	  } else {
	    $sql = "INSERT INTO LH_products_stage2
	          (id, product_id, category_id, parent_id, category_name, category_type, " . $this_root_category_name . ")
	          VALUES
	   	      (NULL, '" . $row['product_id'] . "', '" . $row['category_id'] . "', '" . $row['parent_id'] . "', '" .$row['category_name'] . "', '" . $row['category_type'] . "', '" . $taxonomy_path . "')";
	  }
	  if (mysqli_query($conn, $sql)) {
	//      echo "";
	  } else {
	      echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	  }
	  }
    echo "Completed table <em>LH_products_stage2</em><br>";
	
	// Generate list of columns, one for each taxonomy term (category_name) in selection
	// Select top-level rows of category_type, to be used as taxonomy terms in Webvanta
	$query = "SELECT * FROM LH_column_lookup";
	$result = mysqli_query($conn,$query);
	$column_names_array = array();
	while($row    = mysqli_fetch_assoc($result))
	  {
	  // top-level category names are used as column names for the LH_products_stage2 table
	  $column_names_array[] = $row['column_name'];
echo "************* column_name = " . $column_name . "<hr>";
	  }
	
	// Summarize table for final results, generate output data table with one row per product, with cleaned up names and description, and concatenated taxonomy paths
	$query = "SELECT * FROM LH_products_stage2 s2 
			JOIN product p ON s2.product_id=p.id
			JOIN LH_related_images ri ON s2.product_id=ri.product_id";
	$result = mysqli_query($conn,$query);
	
	$curr_product_id = '';
	while($row    = mysqli_fetch_assoc($result))
	  {
	  // When product switches, insert new row with initial data
	  if ( $curr_product_id <> $row['product_id'] ) {
	    $curr_product_id = $row['product_id'];
	    
	    // delete taxonomy_paths from previous product

        $cleaned_name = preg_replace($patterns, $replacements, $row['name']);
// ***
// NOT SURE IF/HOW TO DO THIS?  DESIRED RESULT IS TO HAVE SINGLE QUOTES, NOT HTML CODES FOR SINGLE QUOTES...
	  // turn any &#39; codes in the "name" field back to single quotes
//	  $cleaned_name = preg_replace('/&\#39;/', '\'', $cleaned_name);

	    $cleaned_description = preg_replace($patterns, $replacements, $row['description']);
	    $cleaned_seo_keyword = preg_replace($patterns, $replacements, $row['seo_keyword']);
	    $cleaned_seo_description = preg_replace($patterns, $replacements, $row['seo_description']);
	    $cleaned_calculator_name = preg_replace($patterns, $replacements, $row['calculator_name']);
	    $cleaned_seo_title = $cleaned_name; // Using "name" field as seo_title, BETTER CONTENT
	    $cleaned_common_name = preg_replace($patterns, $replacements, $row['common_name']);
	    $cleaned_intro = preg_replace($patterns, $replacements, $row['intro']);
	    $cleaned_image_alt = preg_replace($patterns, $replacements, $row['image_alt']);
	    
	    // Replace stars (HTML char &#9733;) with bulleted list (HTML unordered list)
  	    if ( preg_match('/&\#9733;/',$cleaned_description) ) {
  	    
// *** TESTING
$halfcleaned_description = $cleaned_description;

		    //   Step 1: Replace first instance of star with <ul><li>    
		    $cleaned_description = preg_replace('/&\#9733;/', '<ul>\r\n\<li>', $cleaned_description, 1); 

		    //   Step 2: Replace all other stars with <li>
		    $cleaned_description = preg_replace('/&\#9733;/', '<li>', $cleaned_description); 

		    //   Step 3: Split string into array at each instance of <li>
	  	    $substrings = explode("<li>", $cleaned_description);

		    //   Step 4: For each substring except the first, if <br> tag in substring, replace first occurrence with </li><p>, else replace first occurrence of </p> with </li>
		    for ($i = 0; $i < count($substrings); ++$i) {
		  	    if ( $i > 0 ) { 
					//  
					if ( preg_match('/\<br\s*?\/\>/',$substrings[$i]) ) {
		            	$substrings[$i] = preg_replace('/\s*?\<br\s*?\/\>/', '</li>\r\n\r\n<p>', $substrings[$i], 1);
		            } else {
			            $substrings[$i] = preg_replace('/\s*?\<\/p\>/', '</li>', $substrings[$i], 1); 
		            } 
		        }

			    //   Step 5: For last substring only, replace </li> with </li></ul> 
		  	    if ( $i == (count($substrings)-1) ) {
		            $substrings[$i] = preg_replace('/<\/li\>/', '</li>\r\n</ul>', $substrings[$i], 1); 
		        }
		    }

		    //   Step 6: Re-concatenate array into string
		    $cleaned_description = implode("<li>", $substrings);

		    //   Step 7: Replace <p><ul> with <ul>
		    $cleaned_description = preg_replace('/\<p\>\<ul\>/', '<ul>', $cleaned_description); 

		    //   Step 8: Replace <p><li> with <li>
		    $cleaned_description = preg_replace('/\<p\>\s*?\<li\>\s+?/', '<li>', $cleaned_description); 

		    //   Step 9: Replace <br /><li> with  </li><li> 
		    $cleaned_description = preg_replace('/\<br\s\/\>\s*?\<li\>/', '</li>\r\n\r\n<li>', $cleaned_description); 

		    //   Step 10: Replace </li><table> with  </li></ul><table> 
		    $cleaned_description = preg_replace('/\<\/li\>\s*?\<table\>/', '</li>\r\n</ul>\r\n\r\n<table>', $cleaned_description); 

		    //   Step 11: Replace </table><li> with  </table><ul><li> 
		    $cleaned_description = preg_replace('/\<\/table\>\s*?\<li\>/', '</table>\r\n\r\n<ul>\r\n<li>', $cleaned_description); 

		    //   Step 12: Replace </h2><li> with </h2><ul><li>
		    $cleaned_description = preg_replace('/\<\/h2\>\s*?\<li\>/', '</h2>\r\n\r\n<ul>\r\n<li>', $cleaned_description); 

		    //   Step 13: Replace </li><h2> with  </li></ul><h2> 
		    $cleaned_description = preg_replace('/\<\/li\>\s*?\<h2\>/', '</li>\r\n</ul>\r\n\r\n<h2>', $cleaned_description); 
		    
// ********************************

		    //   Step : In case there's more than one bulleted list in description, replace </li><p> with  </li></ul><p> 
		    $cleaned_description = preg_replace('/\<\/li\>\s*?\<p\>/', '</li>\r\n</ul>\r\n\r\n<p>', $cleaned_description); 

		    //   Step : For same reason, replace  </p><li>  with </p><ul><li>
		    $cleaned_description = preg_replace('/\<\/p\>\s*?\<li\>/', '</p>\r\n\r\n<ul>\r\n<li>', $cleaned_description);
//echo "DESCRIP after: " . $cleaned_description . "<br><br><br><br><HR>";
	    }

	    // If calculator_size field begins with "Size", delete the word and replace hyphen with comma 
        if   ( preg_match('/[^Size]/', $row['calculator_size']) ) {
		  $cleaned_calculator_size = preg_replace('/Size /', '', $row['calculator_size']);	// delete "Size "
		  $cleaned_calculator_size = preg_replace('/[-;]/', ',', $cleaned_calculator_size);	// replace hyphen and semi-colon with comma
		  $cleaned_calculator_size = preg_replace('/\s+/', '', $cleaned_calculator_size);		// remove any spaces
	    } 

	    // "status" field from "product" table is to assigned to "draft" field in Webvanta, and if draft cell = "0" replace with "true" -> if not "0" leave blank
	    if ( $row['status'] == 0) { 
	  	  $cleaned_draft = 'true';
	    } else {
	      $cleaned_draft = '';
	    }

	    if ( empty($row['isfeatured']) ) {
	    	$cleaned_isfeatured = 0;
	    } else {
	    	$cleaned_isfeatured = $row['isfeatured'];
	    }	  

	    $sql = "INSERT INTO " . $output_data_table . " (id, product_id, name, halfcleaned_description, description, created_date, draft, main_picture, calculator_name, calculator_size, requirement_id, seo_keyword, seo_description, layout_type, seo_title, care_id, contact_form_id, map_address, view_count, wider_content, product_tabs, isfeatured, reward_icon, common_name, intro, image_alt, publishing_date, reviewer, rating_count, rating, last_modified, slug";
	    foreach ($column_names_array as $value) {
	      $sql = $sql . ", " . $value;
	    }
	    $sql = $sql . ", image2, image2_alt, image3, image3_alt, image4, image4_alt, image5, image5_alt, image6, image6_alt, image7, image7_alt, image8, image8_alt, image9, image9_alt, image10, image10_alt) VALUES (NULL, '" . $row['product_id'] . "', '" . $cleaned_name . "', '" . $halfcleaned_description . "', '" . $cleaned_description . "', '" . $row['created_date'] . "', '" . $cleaned_draft . "', '" . $row['main_picture'] . "', '" . $cleaned_calculator_name . "', '" . $cleaned_calculator_size . "', '" . $row['requirement_id'] . "', '" . $cleaned_seo_keyword  . "', '" . $cleaned_seo_description  . "', '" . $row['layout_type'] . "', '" . $cleaned_seo_title  . "', '" . $row['care_id'] . "', '" . $row['contact_form_id'] . "', '" . $row['map_address'] . "', '" . $row['view_count'] . "', '" . $row['wider_content'] . "', '" . $row['product_tabs'] . "', '" . $cleaned_isfeatured . "', '" . $row['reward_icon'] . "', '" . $cleaned_common_name  . "', '" . $cleaned_intro . "', '" . $cleaned_image_alt . "', '" . $row['publishing_date'] . "', '" . $row['reviewer'] . "', '" . $row['rating_count'] . "', '" . $row['rating'] . "', '" . $row['last_modified'] . "', '" . $row['slug'];
	    foreach ($column_names_array as $value) {
	      $sql = $sql . "', '" . $row[$value];
	    }
	    $sql = $sql . "', '" . $row['image2'] . "', '" . $row['image2_alt'] . "', '" . 
		    $row['image3'] . "', '" . $row['image3_alt'] . "', '" . 
		    $row['image4'] . "', '" . $row['image4_alt'] . "', '" . 
		    $row['image5'] . "', '" . $row['image5_alt'] . "', '" . 
		    $row['image6'] . "', '" . $row['image6_alt'] . "', '" . 
		    $row['image7'] . "', '" . $row['image7_alt'] . "', '" . 
		    $row['image8'] . "', '" . $row['image8_alt'] . "', '" . 
		    $row['image9'] . "', '" . $row['image9_alt'] . "', '" . 
		    $row['image10'] . "', '" . $row['image10_alt'] . "')";
	    if (mysqli_query($conn, $sql)) {
	        //echo "Working...";
	        echo "</tr>";
	    } else {
	        echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	    }
	    foreach ($column_names_array as $value) {
	      // Accumulate taxonomy path, concatenating new terms to any pre-existing taxonomy path
	      $sql = "UPDATE LH_column_lookup SET temp_taxonomy_path='" . $row[$value] . "' WHERE column_name='" . $value . "'"; 
//echo "<hr><hr>" . "Accumulate taxonomy path, temp_taxonomy_path= " . $row[$value] . "<br>";
//echo "column_name= '" . $value . "<hr>"; 
	      if (mysqli_query($conn, $sql)) {
	          //echo "Working...";
	      } else {
	          echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	      }
	    }
	  } else {
	    // For all but first row of a product, update taxonomy-related columns (accumulate and concatenate)
	    foreach ($column_names_array as $value) {
	      // Get this column's existing temp_taxonomy_path value
	      $column_query = "SELECT temp_taxonomy_path FROM LH_column_lookup WHERE column_name='" . $value . "'";
		  $column_result = mysqli_query($conn,$column_query);
		  $this_taxonomy_path = array_values(mysqli_fetch_assoc($column_result))[0];
		  if ($row[$value] != '' ) {
			if ( ($this_taxonomy_path != '') AND (substr($this_taxonomy_path, -1) != ',') ) {
			  $this_taxonomy_path = $this_taxonomy_path . ",";
			}
	
	        // Update accumulating taxonomy path, check each column in each row and concatenate new values
	        $sql = "UPDATE LH_column_lookup SET temp_taxonomy_path='" . $this_taxonomy_path . $row[$value] . "' WHERE column_name='" . $value . "'"; 
	        if (mysqli_query($conn, $sql)) {
	          //echo "Working...";
	        } else {
	            echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	        }
	
	        // Update values in summary results table
	        $sql = "UPDATE " . $output_data_table . " SET " . $value . "='" . $this_taxonomy_path . $row[$value] . "' WHERE product_id='" . $row['product_id'] . "'"; 

//echo "Update values in summary results:<br>";
//echo "  column value= " . $value . "<br>";
//echo "  this_taxonomy_path= " . $this_taxonomy_path . "<br>";
//echo "  row[value]= " . $row[$value] . "<br>";
//echo "  product_id= '" . $row['product_id'] . "<hr>"; 

	        if (mysqli_query($conn, $sql)) {
	          //echo "Working...";
	        } else {
	            echo "Error: " . $sql . "<br>" . mysqli_error($conn) ;
	        }
	//    	echo "";
		  }
	    }
	  }
	  }
    echo "Completed all updates to <em>summary results</em>.<br>";
	
	
	// -------------------------------------------------------
	// Display final results
	// -------------------------------------------------------
	if ( $show_results_in_browser == 1 ) {
		$query = "SELECT * FROM " . $output_data_table;
		$result = mysqli_query($conn,$query);
		echo "<table border='1' cellpadding='2' cellspacing='0'><tr><th>product_id</th><th>name</th><th>half-cleaned description</th><th>description</th>";
		foreach ($column_names_array as $value) {
		  echo "<th>" . $value . "</th>";
		}
		echo "</tr>";
		while($row    = mysqli_fetch_assoc($result))
		  {
		    echo "<tr>";
		    echo "<td>" . $row['product_id'] . "</td>";
		    echo "<td>" . $row['name'] . "</td>";
		    echo "<td>" . $row['halfcleaned_description'] . "</td>";
		    echo "<td>" . $row['description'] . "</td>";
		    foreach ($column_names_array as $value) {
		      echo "<td>" . $row[$value] . "</td>";
		    }
		    echo "</tr>";
		  }
		echo "</table>";
		echo "<hr>";
	}


//}  // End of php form else statement

mysqli_close($conn);
?> 