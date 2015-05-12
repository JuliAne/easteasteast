<?php
/**
 * User: Hans-Gert Gräbe
 * Date: 12.02.2015
 * Make turtle out of jugendstadtplan.json
 */

require_once("lib/EasyRdf.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

$graph = new EasyRdf_Graph("http://leipzig-data.de/Data/JSP-Alt/");
$graph->parseFile("RDFData/jugendstadtplan.json");
echo $graph->serialise("turtle");

?>


