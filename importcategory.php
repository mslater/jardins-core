<?php
require "config.php";

$content = "-ds:Designers
--Garden Style
---Asian
---Coastal
---Contemporary
---Cottage
---Eclectic
---Formal
---Mediterranean
---Modern
---Rock Gardens
---Traditional
---Tropical
--Location
---Australia
---Europe
----France
----Holland
----Italy
---North America
----Canada
----USA
-----Arizona
-----California
-----Connecticut
-----Delaware
-----Florida
-----Georgia
-----Illinois
-----Maryland
-----Massachusetts
-----Missouri
-----Nevada
-----New Jersey
-----New York
-----Pennsylvania
-----Texas
-----Utah
-----Virginia
-----Washington
--Professionals
---AHBL Landscape Architecture
---Andrew Grossman
---Andrew Renn Design
---Bradanini & Associates
---C.O.S. Design
---Carl Balton & Associates
---Carson Poetzl
---Carter Rohrer Co.
---Chip N Dale's Custom Landscaping
---Debora Carl Landscape Design
---Denise Dering Design
---Designing Eden
---Dig Your Garden
---Elliott Brundage Landscape Design
---Envision Landscape studio
---Fernhill Landscapes
---Gardens by Gabriel
---Genevieve Schmidt Design
---Goodman Landscape Design
---Habitat Design
---Integration Design Studio
---Isler Homes
---Jacqueline van der Kloet
---Jean Marsh Design Inc
---Joel Loblaw
---JSL Exteriors Landscape Design/Build
---Katie Moss Landscape Design
---Lankford Associates Landscape Architects
---Liquidscapes
---Louis Benech
---MB Design & Build
---Pat Brodie Landscape Design
---Paul Moon Design
---Pedersen Associates
---Prideaux Design
---RDM Architecture
---Schmechtig Landscapes
---Shades Of Green Landscape Architecture
---Slater Associates
---Studio H Landscape Architecture
---Suzman Design Associates
---Taylor Lombardo Architects
---The Labyrinth Company
---Thomas Kyle Landscape Design
---Van De Voorde Elemental Design Group
---Verdance Fine Garden Design
---WA Design
---Wendy Resin
---Westover Landscape Design
-gi:Garden Ideas
--Design Ideas
---Accessories
---Arbors, Pergolas, Trellises
---Fire pits
---Front Yards
---Gates
---Pathways
---Patios
---Pools
---Small Gardens
---Stairs
---Walls
--My Garden Style
---Asian Style
---Coastal Style
---Contemporary Style
---Cottage Style
---Eclectic Style
---Formal Style
---Mediterranean Style
---Modern Style
---Rock Garden Style
---Traditional Style
---Tropical Style
--Plantings Ideas
---All Planting Types
----Bed & Border Ideas
----Climbing Garden Ideas
----Ground Cover Ideas
----Hedge Ideas
----Meadow & Lawn Ideas
----Pot & Container Ideas
---Bloom / Season of Interest
----Fall Season
----Spring Season
-----Early Spring Season
-----Late Spring Season
-----Mid Spring Season
----Summer Season
-----Early Summer Season
-----Late Summer Season
-----Mid Summer Season
----Winter Season
---Hardiness
----Zone 1
----Zone 10
----Zone 11
----Zone 12
----Zone 2
----Zone 3
----Zone 4
----Zone 5
----Zone 6
----Zone 7
----Zone 8
----Zone 9
---Light / Sun Requirements
----Full Sun Needs
----Partial Sun Needs
----Shade Needs
---Maintenance / Care
----Average Maintenance / Care
----High Maintenance / Care
----Low Maintenance / Care
---Moisture / Water Needs
----Average Water Needs
----High Water Needs
----Low Water Needs
---Plant Color
----Apricot Color
----Blue Color
----Brown Color
----Cream Color
----Gray Color
----Green Color
----Lavender Color
----Mixed Color
----Orange Color
----Pink Color
----Purple Color
----Red Color
----White Color
----Yellow Color
---Plant Type
----Annual Plant
----Bulb Plant
-----Crocus Plant
-----Daffodil Plant
-----Tulip Plant
----Ground Cover Plant
----Ornamental Grass
----Perennial Plant
----Rose
----Shrub
----Succulent
----Vine
-pl:Plants
--1. Hardiness Zone
---Hardiness Zone 1
---Hardiness Zone 10
---Hardiness Zone 11
---Hardiness Zone 12
---Hardiness Zone 2
---Hardiness Zone 3
---Hardiness Zone 4
---Hardiness Zone 5
---Hardiness Zone 6
---Hardiness Zone 7
---Hardiness Zone 8
---Hardiness Zone 9
--2. Period of Interest
---Fall
---Spring
----Early Spring
----Late Spring
----Mid Spring
---Summer
----Early Summer
----Late Summer
----Mid Summer
---Winter
--3. Sun Needs
---Full Sun
---Partial Sun
---Shade
--4. Usage
---Beds & Borders
---Containers
---Cut Flowers
---Ground Covers
---Hanging Baskets
---Hedges
---Rock Garden
---Wall
--5. Maintenance
---Average Maintenance
---High Maintenance
---Low Maintenance
--6. Watering Needs
---Average Water Need
---High Water Need
---Low Water Need
--7. Plant Type
---Annuals
---Bulbs
----Crocus
----Daffodils
----Dahlias
----Tulips
---Herbs
---Ornamental Grasses
---Perennials
---Roses
---Shrubs
---Trees
--8. Color
---Apricot
---Beige
---Blue
---Brown
---Cream
---Lavender
---Mixed Colors
---Orange
---Pink
---Purple
---Red
---White
---Yellow
-pr:Promenades";

$items = explode("\n",$content);
$parents = array();
$type = "";
foreach ($items as $i)
{
	preg_match('/-+/is',$i,$match);
	switch($match[0])
	{
	case "-":
		$line = trim(str_replace('-','',$i));
		$temp = explode(':',$line);
		$type = $temp[0];
		continue;
		break;
	case "--":
		$parent_id = 0;
		$cname = mysql_real_escape_string(trim(str_replace('-','',$i)));
		$last_node = "--";
		mysql_query("INSERT INTO product_category SET category_name='$cname', category_type='{$type}', parent_id='{$parent_id}' ") or die (mysql_error());
		$parents["--"] = mysql_insert_id();
		break;
	case "---":
		$parent_id = $parents["--"];
		$cname = mysql_real_escape_string(trim(str_replace('-','',$i)));
		$last_node = "---";
		mysql_query("INSERT INTO product_category SET category_name='$cname', category_type='{$type}', parent_id='{$parent_id}' ") or die (mysql_error());
		$parents["---"] = mysql_insert_id();
		break;
	case "----":
		$parent_id = $parents["---"];
		$cname = mysql_real_escape_string(trim(str_replace('-','',$i)));
		$last_node = "----";
		mysql_query("INSERT INTO product_category SET category_name='$cname', category_type='{$type}', parent_id='{$parent_id}' ") or die (mysql_error());
		$parents["----"] = mysql_insert_id();
		break;
	case "-----":
		$parent_id = $parents["----"];
		$cname = mysql_real_escape_string(trim(str_replace('-','',$i)));
		$last_node = "-----";
		mysql_query("INSERT INTO product_category SET category_name='$cname', category_type='{$type}', parent_id='{$parent_id}' ") or die (mysql_error());
		$parents["-----"] = mysql_insert_id();
		break;
	}
}
?>
