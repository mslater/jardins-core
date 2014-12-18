<?php

// product records with category_type = ds that show INSERT ERRORS:
//	2859 'A delightful Scenery - Dig Your Garden'
//  2861 
//  2862 
//  2881 
//  2882 
//  2898 


//  	Weird symbol in prod descrip of products 2631, 2636, 2637:
//			Featured at Â <a title="Isola Bella" 




// Connect to database
$conn=mysqli_connect("localhost","root","Muser123","jardinss_db");
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

// *****************  Set up global variables and data tables  ***************************************
//
$starting_product_id = 2594;  // lowest product id = 2594
$ending_product_id   = 4746;  // highest product id = 4746

$show_results_in_browser = 0;  // Set to 1 to display HTML table of summary results

// -------------------------------------------------------
//$output_data_table = 'LH_results_ARTICLES_bs';
$output_data_table = 'LH_results_ARTICLES_ds';
//$output_data_table = 'LH_results_ARTICLES_pr';
//$output_data_table = 'LH_results_PLANS';
//$output_data_table = 'LH_results_PLANTS_0_3400';
//$output_data_table = 'LH_results_PLANTS_3401_3900';
//$output_data_table = 'LH_results_PLANTS_3901_4400';
//$output_data_table = 'LH_results_PLANTS_4401_end';

// -------------------------------------------------------
//$category_select = "category_type='bs'";  	// select Articles categories bs
$category_select = "category_type='ds'";  	// select Articles categories ds 
//$category_select = "category_type='pr'";  		// select Articles categories pr
//$category_select = "category_type='gi'";		// select Plans categories
//$category_select = "category_type='pl'";  	// select Plants categories
// NOTES:
// 		category_type	Webvanta	category_select value
//   	-------------	--------	---------------------
// gi	Garden-Ideas	Plans		'gi'
// pl	Plants			Plants		'pl'
// bs	Basics			Articles	'bs' OR 'ds'  OR 'pr'
// pr	Promenades		Articles
// ds	Designers		Articles




// DROP TABLE `LH_column_lookup`, `LH_products_stage1`, `LH_products_stage2`, `LH_results_ARTICLES_bs`, `LH_results_ARTICLES_ds`, `LH_results_ARTICLES_pr`, `LH_results_PLANTS`, `LH_taxonomy_paths`;



// -------------------------------------------------------
// Patterns to search and replace to clean up name and description fields
$patterns = array();
$patterns[0] = '/&nbsp;/';   					// non-breaking space
$patterns[1] = '/\'/';       					// single quote
$patterns[2] = '/\& /';							// Ampersand followed by space
$patterns[3] = '/ align="center"/';				// table align center
$patterns[4] = '/ border=".*?"/';				// ANY table border tag
$patterns[5] = '/ cellpadding=".*?"/';			// ANY table cellpadding tag
$patterns[6] = '/ cellspacing=".*?"/';			// ANY table cellspacing tag
$patterns[7] = '/ style=".*?"/';				// ANY style tag
$patterns[8] = '/ class=".*?"/';				// ANY class tag
$patterns[9] = '/\<strong\>|\<\/strong\>/';		// bold
$patterns[10] = '/\<em\>|\<\/em\>/';				// italics
$patterns[11] = '/\<span\>|\<\/span\>/';			// clean up empty span tags
// and corresponding replacements for matches found
$replacements = array();
$replacements[0] = ' ';      					// replace non-breaking space codes with space
$replacements[1] = '&#39;';  					// replace single quotes with HTML code for single quote
$replacements[2] = '&amp; ';  					// replace ampersand with HTML code for ampersand, followed by space
$replacements[3] = '';       					// for the rest, replace with empty string
$replacements[4] = '';
$replacements[5] = '';
$replacements[6] = '';
$replacements[7] = '';
$replacements[8] = '';
$replacements[9] = '';
$replacements[10] = '';
$replacements[11] = '';

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
$query = "SELECT * FROM product_category WHERE (" . $category_select . " AND parent_id=0) ORDER BY category_name";
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
// Delete and recreate LH_products_stage1 table
$sql = 'DROP TABLE IF EXISTS LH_products_stage1';
if (mysqli_query($conn, $sql)) {
//    echo "Dropped table <em>LH_products_stage1</em><br>";
} else {
    echo "Error dropping table: " . mysqli_error($conn) . "<br>";
}
// Create new LH_products_stage2 table with initial fields only
$sql = "CREATE TABLE IF NOT EXISTS LH_products_stage1 (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(255),
description TEXT,
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
description TEXT,
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
name VARCHAR(255),
description TEXT,
product_id INT(11)
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

if (!isset($_POST['submit'])) { // if page is not submitted to itself echo the form
	echo "<html>";
	echo "<body>";
	echo "<form method='post' action='".$PHP_SELF."'>";
	echo "<input type='checkbox' value='Articles' name='webvanta_data_type[]'> Articles<br />";
	echo "<input type='checkbox' value='Plans' name='webvanta_data_type[]'> Plans<br />";
	echo "<input type='checkbox' value='Plants' name='webvanta_data_type[]'> Plants<br />";
	echo "<input type='submit' value='submit' name='submit'>";
	echo "</form>";
} else {

    foreach ($webvanta_data_type as $f) {
      echo $f."<br />";
    }

	// For stage 1 make LH_products_stage1 table, with only rows of the selected category type(s) and with cleaned up names and descriptions.
	
	
	// INSERT ERRORS OCCURRING ON SIX PRODUCTS WHERE category_type='ds', SO EXCLUDING FROM OUTPUT FOR NOW...
	$query = "SELECT p.id, p.name, p.description, pic.product_id, pic.category_id, pc.parent_id, pc.category_name, pc.category_type FROM product p 
			JOIN product_in_category pic ON p.id=pic.product_id 
			JOIN product_category pc ON pic.category_id=pc.id
			WHERE (p.id>='" . $starting_product_id . "' AND p.id <'" . $ending_product_id . "' AND " . $category_select . " AND 
			p.id<>2859 AND p.id<>2861 AND p.id<>2862 AND p.id<>2881 AND	p.id<>2882 AND p.id<>2898 )
			ORDER BY p.id, pc.category_name";
//	$query = "SELECT p.id, p.name, p.description, pic.product_id, pic.category_id, pc.parent_id, pc.category_name, pc.category_type FROM product p 
//			JOIN product_in_category pic ON p.id=pic.product_id 
//			JOIN product_category pc ON pic.category_id=pc.id
//			WHERE (p.id>='" . $starting_product_id . "' AND p.id <'" . $ending_product_id . "' AND " . $category_select . ")
//			ORDER BY p.id, pc.category_name";

	$result = mysqli_query($conn,$query);
	
	// Add data to LH_products_stage1 table
//	  echo "<table border='1' cellpadding='2' cellspacing='0'><tr><th>name</th><th>product_id</th><th>category_id</th><th>parent_id</th><th>category_name</th><th>category_type</th></tr>";
	while($row    = mysqli_fetch_assoc($result))
	  {
	  $cleaned_name = preg_replace($patterns, $replacements, $row['name']);
	  $cleaned_description = preg_replace($patterns, $replacements, $row['description']);

	  $sql = "INSERT INTO LH_products_stage1
	        (id, name, description, product_id, category_id, parent_id, category_name, category_type)
	        VALUES
	        (NULL, '" . $cleaned_name . "', '" . $cleaned_description . "', '" . $row['product_id'] . "', '" . $row['category_id'] . "', '" . $row['parent_id'] . "', '" . $row['category_name'] . "', '" . $row['category_type'] . "')";
	  if (mysqli_query($conn, $sql)) {
//		    echo "<tr>";
//		    echo "<td>" . $row['name'] . "</td>";
//		    echo "<td>" . $row['product_id'] . "</td>";
//		    echo "<td>" . $row['category_id'] . "</td>";
//		    echo "<td>" . $row['parent_id'] . "</td>";
//		    echo "<td>" . $row['category_name'] . "</td>";
//		    echo "<td>" . $row['category_type'] . "</td>";
//		    echo "</tr>";
	  } else {
	      echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	  }
	  }
//	echo "</table>";
    echo "Completed table <em>LH_products_stage1</em><br>";



	// *****************  Generate Lookup Table with Root Categories and Taxonomy Paths for each Category  ***************************************
	//
	// Get plant categories, then traverse from leaf to root, building up the taxonomy path Webvanta likes so much
	// insert data into table and display table of results
	$query = "SELECT * FROM product_category WHERE (" . $category_select . ") ORDER BY id";
	$result = mysqli_query($conn,$query);
	//echo "<table border='1' cellpadding='2' cellspacing='0'><tr><th>id</th><th>parent_id</th><th>category_name</th><th>root_category_name</th><th>taxonomy_path</th></tr>";
	while($row    = mysqli_fetch_assoc($result))
	  {
	  $root_category_name = $row['category_name'];
	  if ( $row['parent_id'] == 0 ) {
	      $taxonomy_path = '';
	  } else {
		  $taxonomy_path = preg_replace($patterns, $replacements, $row['category_name']);
//	      $taxonomy_path = $row['category_name'];

	      $this_parent_id = $row['parent_id'];
	      while ( $this_parent_id > 0 )
	        {
	        $parent_results = mysqli_query($conn,"SELECT category_name, parent_id FROM product_category WHERE id = '" . $this_parent_id . "'");
	        $parent_array = mysqli_fetch_assoc($parent_results);
	        $parent_category_name = preg_replace($patterns, $replacements, array_values($parent_array)[0]);
	        $parent_id_of_parent = array_values($parent_array)[1];
	        if ( $parent_id_of_parent > 0 ) {
	          $taxonomy_path = $parent_category_name . ">" . $taxonomy_path;
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
	//      echo "<tr>";
	//      echo "<td>" . $row['id'] . "</td>";
	//      echo "<td>" . $row['parent_id'] . "</td>";
	//      echo "<td>" . $row['category_name'] . "</td>";
	//      echo "<td>" . $root_category_name . "</td>";
	//      echo "<td>" . $taxonomy_path . "</td>";
	//      echo "</tr>";
	  } else {
	      echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	  }
	  }
	//echo "</table>";
	//echo "<hr>";
    echo "Completed generating <em>LH_taxonomy_paths</em> lookup table.<br>";

	
	// *****************  Add columns to LH_categories_table, then add data  ***************************************
	//
	// Add columns to plants table, one for each taxonomy term (category_name) in selection
	// Select top-level rows of category_type plant, to be used as taxonomy terms in Webvanta

	$query = "SELECT * FROM LH_column_lookup";
	$result = mysqli_query($conn,$query);
	//echo "<table border='1' cellpadding='2' cellspacing='0'><tr><th>Column name</th></tr>";
	while($row    = mysqli_fetch_assoc($result))
	  {
	  // use top-level category names as column names for new LH_products_stage2 table
	  $sql = "ALTER TABLE LH_products_stage2 ADD (" . $row['column_name'] . " varchar(1020))";
	  if (mysqli_query($conn, $sql)) {
	//      echo "<tr><td>" . $column_name . "</td></tr>";
	  } else {
	      echo "<tr><td>Error adding column: " . mysqli_error($conn) . "</td></tr>";
	  }
	  }
	//echo "</table>";
	//echo "<hr>";
	
	// Get cleaned data from stage 1 table	
	$query = "SELECT * FROM LH_products_stage1 ORDER BY id, category_name";
	$result = mysqli_query($conn,$query);
	
	// Add root category and taxonomy path data to LH_products_stage2 table
	//echo "<table border='1' cellpadding='2' cellspacing='0'><tr><th>name</th><th>product_id</th><th>category_id</th><th>parent_id</th><th>category_name</th><th>category_type</th><th>root_category_name</th><th>taxonomy_path</th></tr>";
	while($row    = mysqli_fetch_assoc($result))
	  {
	
	  $taxonomy_path_results = mysqli_query($conn,"SELECT root_category_name, taxonomy_path FROM LH_taxonomy_paths WHERE category_id = '" . $row['category_id'] . "'");
	  $taxonomy_path_array = mysqli_fetch_assoc($taxonomy_path_results);
	  $root_category_name = str_replace(" ", "_", array_values($taxonomy_path_array)[0]);
	  $taxonomy_path = array_values($taxonomy_path_array)[1];
	  //	echo "****" . $cleaned_taxonomy_path . "****<br>";
	
	  // If top-level category, then only set basic column values 
	  if ( $row['parent_id'] == 0 ) {
	    $sql = "INSERT INTO LH_products_stage2
	          (id, name, description, product_id, category_id, parent_id, category_name, category_type)
	          VALUES
	   	      (NULL, '" . $row['name'] . "', '" . $row['description'] . "', '" . $row['product_id'] . "', '" . $row['category_id'] . "', '" . $row['parent_id'] . "', '" .$row['category_name'] . "', '" . $row['category_type'] . "')";
	  // If there is a taxonomy path, add it to particular column with the same name as the root category, the top-level taxonomy term
	  } else {
	    $sql = "INSERT INTO LH_products_stage2
	          (id, name, description, product_id, category_id, parent_id, category_name, category_type, " . $root_category_name . ")
	          VALUES
	   	      (NULL, '" . $row['name'] . "', '" . $row['description'] . "', '" . $row['product_id'] . "', '" . $row['category_id'] . "', '" . $row['parent_id'] . "', '" .$row['category_name'] . "', '" . $row['category_type'] . "', '" . $taxonomy_path . "')";
	  }
	  if (mysqli_query($conn, $sql)) {
	//      echo "<tr>";
	//      echo "<td>" . $row['name'] . "</td>";
	//      echo "<td>" . $row['product_id'] . "</td>";
	//      echo "<td>" . $row['category_id'] . "</td>";
	//      echo "<td>" . $row['parent_id'] . "</td>";
	//      echo "<td>" . $row['category_name'] . "</td>";
	//      echo "<td>" . $row['category_type'] . "</td>";
	//      echo "<td>" . $root_category_name . "</td>";
	//      echo "<td>" . $taxonomy_path . "</td>";
	      echo "</tr>";
	  } else {
	      echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	  }
	  }
	//echo "</table>";
	//echo "<hr>";
    echo "Completed table <em>LH_products_stage2</em><br>";
	
	
	// Generate list of columns, one for each taxonomy term (category_name) in selection
	// Select top-level rows of category_type plant, to be used as taxonomy terms in Webvanta
	$query = "SELECT * FROM LH_column_lookup";
	$result = mysqli_query($conn,$query);
	$column_names_array = array();
	while($row    = mysqli_fetch_assoc($result))
	  {
	  // top-level category names are used as column names for the LH_products_stage2 table
	  $column_names_array[] = $row['column_name'];
	//  echo $column_name . "<br>";
	  }
	//echo "<hr>";

	
	// Summarize table for final results, generate output data table with one row per product, with cleaned up names and description, and concatenated taxonomy paths
	$query = "SELECT * FROM LH_products_stage2";
	$result = mysqli_query($conn,$query);
	
	
	$curr_product_id = '';
	//echo "<table border='1' cellpadding='2' cellspacing='0'><tr><th>product_id</th><th>row</th><th>LH_column name</th><th>column value</th><th>this_taxonomy_path</th></tr>";
	while($row    = mysqli_fetch_assoc($result))
	  {
	  // When product switches, insert new row with initial data
	  if ( $curr_product_id <> $row['product_id'] ) {
	    $curr_product_id = $row['product_id'];
	
	    $sql = "INSERT INTO " . $output_data_table . " (id, product_id, name, description";
	    foreach ($column_names_array as $value) {
	      $sql = $sql . ", " . $value;
	    }
	    $sql = $sql . ") VALUES (NULL, '" . $row['product_id'] . "', '" . $row['name'] . "', '" . $row['description'];
	    foreach ($column_names_array as $value) {
	      $sql = $sql . "', '" . $row[$value];
	    }
	    $sql = $sql . "')";
	    if (mysqli_query($conn, $sql)) {
	        //echo "Working...";
	        echo "</tr>";
	    } else {
	        echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	    }
	    foreach ($column_names_array as $value) {
	      // Accumulate taxonomy path, concatenating new terms to any pre-existing taxonomy path
	      $sql = "UPDATE LH_column_lookup SET temp_taxonomy_path='" . $row[$value] . "' WHERE column_name='" . $value . "'"; 
	//      echo $sql . "<br>";
	      if (mysqli_query($conn, $sql)) {
	          //echo "Working...";
	      } else {
	          echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	      }
	//      echo "row value: " . $row[$value] . "<br>";
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
	//		  echo "path: " . $this_taxonomy_path . "    &nbsp;&nbsp;| last char: " . substr($this_taxonomy_path, -1) . "<br>";
			  $this_taxonomy_path = $this_taxonomy_path . ",";
			}
	
	        // Update accumulating taxonomy path, check each column in each row and concatenate new values
	        $sql = "UPDATE LH_column_lookup SET temp_taxonomy_path='" . $this_taxonomy_path . $row[$value] . "' WHERE column_name='" . $value . "'"; 
	//        echo $sql . "<br>";
	        if (mysqli_query($conn, $sql)) {
	          //echo "Working...";
	        } else {
	            echo "<tr><td>Error: " . $sql . "<br>" . mysqli_error($conn) . "</td></tr>";
	        }
	
	        // Update values in summary results table
	        $sql = "UPDATE " . $output_data_table . " SET " . $value . "='" . $this_taxonomy_path . $row[$value] . "' WHERE product_id='" . $row['product_id'] . "'"; 
	//        echo $sql . "<br>";
	        if (mysqli_query($conn, $sql)) {
	          //echo "Working...";
	        } else {
	            echo "Error: " . $sql . "<br>" . mysqli_error($conn) ;
	        }
	//    	echo "<tr>";
	//    	echo "<td>" . $row['product_id'] . "</td>";
	//    	echo "<td>" . $row['id'] . "</td>";
	//    	echo "<td>" . $value . "</td>";
	//    	echo "<td>" . $row[$value] . "</td>"; 
	//    	echo "<td>" . $this_taxonomy_path . "</td>";
	//    	echo "</tr>";
		  }
	    }
	  }
	  }
	//echo "</table>";
	//echo "<hr>";
    echo "Completed all updates to <em>summary results</em>.<br>";
	
	
	// -------------------------------------------------------
	// Display final results (without descriptions)
	// -------------------------------------------------------
	if ( $show_results_in_browser == 1 ) {
		$query = "SELECT * FROM " . $output_data_table;
		$result = mysqli_query($conn,$query);
	//	if ($show_results_in_browser) {
	//	}
		echo "<table border='1' cellpadding='2' cellspacing='0'><tr><th>product_id</th><th>name</th>";  // <th>description</th>";
		foreach ($column_names_array as $value) {
		  echo "<th>" . $value . "</th>";
		}
		echo "</tr>";
		while($row    = mysqli_fetch_assoc($result))
		  {
		    echo "<tr>";
		    echo "<td>" . $row['product_id'] . "</td>";
		    echo "<td>" . $row['name'] . "</td>";
		//    echo "<td>" . $row['description'] . "</td>";
		    foreach ($column_names_array as $value) {
		      echo "<td>" . $row[$value] . "</td>";
		    }
		    echo "</tr>";
		  }
		echo "</table>";
		echo "<hr>";
	}


}  // End of php form else statement

mysqli_close($conn);
?> 