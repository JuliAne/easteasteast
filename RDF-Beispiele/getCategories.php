<?php
/**
 * User: Immanuel Plath
 * Date: 05.12.14
 */

// include section
//=========================
require_once("lib/EasyRdf.php");
require_once("graphLoader.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

// Function
// function returns all categories which could found in rdf data
// param: language example "de" or "en" ...
// param: categOne define name of first category contain all locations
// param: categTwo define name of second category contain all locations without a category
// return: html contains all categories
//=========================
function getCategories()
{  
    // parse rdf graph
    $menuCategories = "";
    // load full graph
    $rdfGraph = new RdfGraph(array("RDFData/jugendstadtplan.json"));
    $graph1 = $rdfGraph->getGraph();
    // count avaiable categories ($count = 2 because first two categories are static)
    $count          = 2;
    // contains alle categories
    $testarray      = array();
    // search in rdf graph for categories
    foreach ($graph1->resources() as $res) {
        // check if location has a category
        if ($res->hasProperty('jsp:hascategory') == 1) {
            // get all categories for a location
            foreach ($res->all('jsp:hascategory') as $art) {
                // check if category is found in step before
	      if (!in_array($art, $testarray)) { 
                    $testarray[] = $art;
                    $res2        = $graph1->resource($art);
                    $labelres2   = $res2->label("de");
                    
                    $type = str_replace("http://leipzig-data.de/Data/Jugendstadtplan/", "", $res2);
		    // echo "$type -- $labelres2 <br/>"; 
                    $menuCategories.= '
                    <li role="presentation" class=""><a aria-controls="home'.$count
		      .'" data-toggle="tab" id="home'
		      .$count.'-tab" role="tab" href="#home'.$count
		      .'" aria-expanded="false">'.$labelres2.'</a></li>';
                    $count++;
	      }
            }
	}
    }
    echo "<ul>".$menuCategories."</ul>"; 
}

getCategories(); // for testing

?>


